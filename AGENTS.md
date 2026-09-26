# AGENTS.md — Smart Hospitals (Laravel 10 HMS)

Laravel 10.50.3 (`composer.lock` pinned) on PHP `^8.1` (verified 8.2.12), MySQL/MariaDB only.
No CI, no lint/test npm scripts, no `opencode.json`. Executable sources of truth:
`composer.json`, `phpunit.xml`, `routes/web.php`, `database/seeders/DatabaseSeeder.php`.

## Setup (Windows)

```bat
copy .env.example .env
:: edit .env: DB_DATABASE / DB_USERNAME / DB_PASSWORD (local test DB was hms_test)
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

- `npm` / Mix build is optional — compiled assets are already committed under `public/`. Only run it when changing `resources/js|scss`.
- Never commit `.env`.

## Verify

- `php artisan test` — only 2 example tests (PHPUnit 10, `<source>`-style `phpunit.xml` with array drivers). Keep it green; don't "fix" by downgrading PHPUnit.
- `php -l` on touched files; `php artisan view:cache` after Blade changes; `php artisan route:list` after route changes (~82 routes expected).
- `php artisan optimize:clear` after config/view/route changes.

## Architecture quirks (do not "normalize")

- **Models live flat in `app/*.php`, not `app/Models/`.** `app/clinic.php` (lowercase, class `App\Clinic`) triggers a known harmless PSR-4 warning — do not rename/move it.
- **No real API**: `routes/api.php` is a stub. All features are web routes in `routes/web.php` (`Request → middleware → Controller → Eloquent/DB → Blade`).
- **Auth is role middleware on `users.user_type`** (`app/Http/Middleware/`, registered in `app/Http/Kernel.php`): `doctor` passes for doctor+admin, `staff` = general|doctor|admin, `pharmacist` = pharmacist|admin. There is no separate `doctors` table and no `nurse`/`reception` role (`general` covers reception).
- **`patients.id` is manual** (`YYMMDD+seq`, `$incrementing=false`, signed bigint). Related FKs were hand-matched for signed/unsigned in migrations — do not retune column types without running `migrate:fresh` on a scratch DB first.
- **Dead routes are intentional redirects** — do not resurrect controllers/views for them:
  `/reportgeneration` → `/clinicreports`, `/emails` → `/createnoticeview`, `/outpreport` → `/attendancereport`.
- **`GET /attendance` is a fingerprint-kiosk route behind `guest` middleware** — a 302 for logged-in users is expected, not a bug. (Method-name case difference vs `markAttendance` is harmless; PHP methods are case-insensitive.)

## Seeders

- Order in `DatabaseSeeder` matters: `Users` first (`Attendances` needs users 1–5).
- Only `DemoDataSeeder` is safe to re-run (`php artisan db:seed --class=DemoDataSeeder`); a full `db:seed` re-run can duplicate data.
- Seeded logins (all password `12345678` unless noted): `shakthisachintha@gmail.com` (admin), `ssakunchamikara@gmail.com` (doctor), `sachinthaindu95@gmail.com` (pharmacist), `sanduniiresha1029@gmail.com` (general).

## Conventions

- StyleCI `laravel` preset with `unused_use` disabled — don't churn imports to satisfy other linters.
- Locales: `resources/lang` (`en`/`si`/`ar`), switched via `/lang/{en|si|ar}` (`SetLanguage` middleware); sidebar helper `Active::checkRoute` is aliased in `config/app.php`, backed by `app/Helpers/Active.php`.
- Activity logging (`spatie/laravel-activitylog`) and `activity()` calls are load-bearing audit trails — keep them when editing controllers.
- Do not commit, amend, or push unless explicitly asked (this repo previously carried uncommitted route fixes awaiting approval).

## Phase History (Rev 1-3)

Commits (chronological, do not amend):
- 19db5a0 — Phase 1+2: quick wins + security hardening
- b8da20b — Phase 3: real medicine stock (FEFO, batch/expiry)
- 1cce5fe — Phase 4: doctors table, scheduled_at, UI field
- 3be5691 — Phase 6: invoice system (also absorbed uncommitted Phase 5 PDF work: ReportController PDF methods + reports/pdf views — Phase 5 has no separate commit)
- 3032418 — Phase 6-Rev2: independent invoice per operation
- 276803a — Phase 6-Rev3: PDF error handling + smooth appointment flow
- f788ab2 — Phase 7: feature tests (27 passed, isolated test DB)

## Architecture Updates (Phases 3-7)

- **Independent invoices per operation**: Since Rev2, each medical action (appointment, consultation, medicine, ward) generates its own invoice with `invoice_type`. No daily-aggregated invoices.
- **Fingerprint kiosk uses GET**: `/attendance` accepts only `GET /attendance?finger=...&time=...` (no POST). The `markAttendance` method reads query params.
- **Tests run against isolated DB**: `phpunit.xml` uses `DB_DATABASE=test` to protect dev data from `RefreshDatabase`.
- **Services table drives prices**: Prices seeded in `services` table (CONSULT=5000, APPOINTMENT=1000, MED=500, WARD=10000). Change prices by updating the seeder + re-run.
- **Models without `$fillable`**: `Patients`, `Appointment`, `Prescription_Medicine` require manual hydration (`new Model; $m->field = X; $m->save();`) in tests.
- **PDF error handling**: `InvoiceController::pdf` catches `ModelNotFoundException` and redirects to `/invoices` with a flash error instead of showing a gray 404.
- **Appointment auto-print is a link**: `/createchannel` JSON response includes `invoice_id`; UI shows a print link. Auto-popup is blocked by Chrome, so we use `target="_blank"`.
- **Medicine stock FEFO**: `markIssued` uses FEFO (`orderByRaw('ISNULL(expiry_date), expiry_date ASC')`) with `lockForUpdate()` inside a transaction to prevent concurrent double-issue.
- **RTL PDF**: All PDF views use `dir="rtl"` + `DejaVu Sans` font for Arabic rendering.
