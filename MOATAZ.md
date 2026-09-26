# MOATAZ.md — الشرح الكامل لمشروع Smart Hospitals
## من الفكرة إلى الإنتاج: رحلة نظام إدارة مستشفى

> **من أنا؟** معتز بشير محمد مصلح — قائد فريق Smart Hospitals.
> **ما هذا الملف؟** شرح كامل للمشروع: كل ميزة، كل تقنية، كل قرار هندسي.
> **لمن؟** د. ساهر الهمداني + المقيّمين + أي مطور يريد فهم المشروع.

---

## 📚 الفهرس

1. [المشروع — لماذا Smart Hospitals؟](#1-الفصل-الأول-المشروع--لماذا-smart-hospitals)
2. [الأساسيات — كيف بنينا النظام](#2-الفصل-الثاني-الأساسيات--كيف-بنينا-النظام)
3. [الميزات — كل ميزة بالتفصيل](#3-الفصل-الثالث-الميزات--كل-ميزة-بالتفصيل)
4. [الأمان — كيف نحمي النظام](#4-الفصل-الرابع-الأمان--كيف-نحمي-النظام)
5. [قاعدة البيانات](#5-الفصل-الخامس-قاعدة-البيانات)
6. [الاختبارات — كيف نضمن الجودة](#6-الفصل-السادس-الاختبارات--كيف-نضمن-الجودة)
7. [التطوير — كيف بُني المشروع](#7-الفصل-السابع-التطوير--كيف-بُني-المشروع)
8. [المشاكل الهندسية وحلولها](#8-الفصل-الثامن-المشاكل-الهندسية-وحلولها)
9. [ربط المشروع بمحاضرات د. ساهر](#9-الفصل-التاسع-ربط-المشروع-بمحاضرات-د-ساهر)
10. [المعمارية المستهدفة (Service Layer)](#10-الفصل-العاشر-المعمارية-المستهدفة-service-layer)
11. [كيف تشغّل المشروع](#11-الفصل-الحادي-عشر-كيف-تشغّل-المشروع)
12. [الإحصاءات النهائية](#12-الفصل-الثاني-عشر-الإحصاءات-النهائية)
13. [الدروس المستفادة](#13-الفصل-الثالث-عشر-الدروس-المستفادة)
14. [شكر وتقدير](#14-الفصل-الرابع-عشر-شكر-وتقدير)
15. [المراجع](#15-الفصل-الخامس-عشر-المراجع)
16. [المعلومات الفنية](#16-الفصل-السادس-عشر-المعلومات-الفنية)
17. [الخاتمة](#17-الخاتمة)

---

## 1. الفصل الأول: المشروع — لماذا Smart Hospitals؟

### 1.1 القصة من البداية

بدأت الحكاية من مشهد مألوف في المستشفيات اليمنية: ممرات مزدحمة، دفاتر ورقية متهالكة، وموظف استقبال يقلّب صفحات سجل بحثاً عن اسم مريض — بينما الطابور يطول. سألنا أنفسنا: لماذا تُدار صحة الناس بأدوات القرن الماضي؟ من هنا وُلدت فكرة **Smart Hospitals**: نظام ويب واحد يحمل المستشفى كاملاً — مرضاه ومواعيده وأدويته وفواتيره — في المتصفح، بلا ورق وبلا ضياع.

### 1.2 المشكلة بتفصيل

1. **صعوبة البحث:** إيجاد ملف مريض قديم في الدفاتر يستغرق دقائق طويلة، وأحياناً يستحيل.
2. **ضياع المواعيد:** حجوزات بخط اليد تُفقد أو تُكرر، ولا ترقيم عادل للمراجعين.
3. **خطر طبي:** وصفات غير مؤرشفة تعني إمكانية صرف دواء خاطئ أو تكرار جرعة دون علم.
4. **مخزون أعمى:** أدوية تنتهي صلاحيتها على الرفوف دون أن يدري أحد — هدر مالي وخطر سريري.
5. **حضور شكلي:** دفاتر الحضور الورقية قابلة للتلاعب ولا تُنتج إحصاءات.
6. **تقارير مستحيلة:** أي قرار إداري (كم صرفنا؟ كم حضر؟) يتطلب أياماً من الجمع اليدوي.
7. **لغة واحدة:** الأنظمة الجاهزة أجنبية ومكلفة ولا تدعم العربية.

### 1.3 الحل: Smart Hospitals

نظام ويب متكامل بلغة PHP (Laravel 10) يقدم:

1. تسجيل مرضى إلكترونياً مع **بطاقة باركود** ورقم `YYMMDD+seq`.
2. **مواعيد يومية** بترقيم تلقائي ووقت جدولة.
3. **فحص طبي** + قياسات حيوية (ضغط/سكر/كولسترول) + وصفات مؤرشفة.
4. **مخزون أدوية** بنظام الدفعات FEFO (الأقرب انتهاءً يُصرف أولاً).
5. **فواتير مستقلة** لكل عملية (حجز/كشف/دواء/رقود) + دفع جزئي/كلي.
6. **تقارير PDF** بالعربية (عيادة/حضور/منوّمون/شهري).
7. **حضور موظفين** بجهاز بصمة (HTTP GET) أو يدوياً.
8. **4 أدوار و3 لغات** (عربي RTL + إنجليزي + سنهالي).

### 1.4 الفرق قبل/بعد

| العملية | قبل (ورق) | بعد (النظام) |
|---|---|---|
| البحث عن مريض | دقائق في الدفاتر | ثوانٍ بالاسم/الهاتف/الرقم الوطني |
| حجز موعد | ورقة قد تضيع | رقم تسلسلي + فاتورة تلقائية |
| الوصفة | تُنسى أو تضيع | مؤرشفة ومرتبطة بالمريض والطبيب |
| المخزون | انتهاء مفاجئ | تنبيهات (نافد/منخفض/قارب الانتهاء) |
| الحضور | توقيع يدوي | بصمة + مدة محسوبة تلقائياً |
| التقرير الشهري | أيام جمع يدوي | PDF بضغطة واحدة |

### 1.5 من يستخدم النظام؟

| الدور | من هو؟ | ماذا يفعل؟ |
|---|---|---|
| Admin | مدير المستشفى | كل الصلاحيات: مستخدمون، تقارير، إحصائيات، فواتير |
| Doctor | الطبيب | فحص، وصفات، ملفات مرضى، رقود وخروج |
| Pharmacist | الصيدلي | صرف أدوية، متابعة المخزون |
| General | موظف الاستقبال | تسجيل مرضى، مواعيد، فواتير |

### 1.6 رقمياً — النظام في سطور

- **21 commit** (3 تأسيسية + 9 مراحل + توثيق وفريق).
- **27 اختبار** ناجح (43 assertions) على قاعدة معزولة.
- **17 model** و**16 controller** و**~90 route**.
- **24 migration** و**14 seeder**.
- **6 جداول جديدة** صممناها: `medicine_stocks`، `doctors`، `services`، `invoices`، `invoice_items`، `invoice_payments` (+ عمودا `scheduled_at` و`invoice_type`).

---

## 2. الفصل الثاني: الأساسيات — كيف بنينا النظام

### 2.1 التقنيات المستخدمة (ولماذا؟)

| التقنية | الإصدار | لماذا اخترناها؟ |
|---|---|---|
| Laravel 10 | 10.50.3 | MVC ناضج + Eloquent + حماية جاهزة (CSRF/ Auth) + مجتمع ضخم |
| PHP | 8.2 | أداء عالٍ + أنواع صارمة + مدعوم حتى 2026 |
| MySQL / MariaDB | 5.7+ | علائقي موثوق، يعمل على أي استضافة محلية (XAMPP) |
| Blade | مدمج | قوالب بسيطة بلا Build step، تكفي لنظام إداري |
| AdminLTE 2 | 2.4.12 | لوحة تحكم احترافية جاهزة بدل تصميم من الصفر |
| Bootstrap | 3.4.1 | شبكة متجاوبة مستقرة مع AdminLTE 2 |
| jQuery | 3.x | AJAX سريع للنماذج (حجز/صرف) دون SPA معقد |
| Chart.js | 2.x | رسوم الإحصائيات بأقل كود |
| DataTables | 1.x | بحث/ترتيب/ترقيم للجداول الكبيرة |
| dompdf | 3.x | توليد PDF من Blade نفسه + دعم DejaVu للعربية |
| spatie-activitylog | 4.x | سجل تدقيق لكل عملية (متطلب إداري وقانوني) |
| milon-barcode | 10.x | باركود بطاقة المريض (Code39) |

### 2.2 لماذا Laravel 10؟

أولاً: **MVC الحقيقي** — فصل نظيف بين المسارات والمنطق والعرض، وهو ما درّسه د. ساهر في المختبرات. ثانياً: **Eloquent** يختصر مئات أسطر SQL ويحمي من الحقن عند الاستخدام الصحيح. ثالثاً: **منظومة أمان جاهزة** (bcrypt، CSRF، middleware) بدل إعادة اختراع العجلة. رابعاً: **النظام البيئي** — حزم spatie وdompdf وفرت علينا أسابيع. اخترنا 10 لا 11/12 لأنها مستقرة وموثقة ومتوافقة مع PHP 8.2 المتاح في المعامل.

### 2.3 المعمارية العامة

```
المتصفح
  │  HTTP (GET/POST)
  ▼
public/index.php
  ▼
routes/web.php  (~90 مساراً — لا API منفصل)
  ▼
Middleware: auth → role (admin/doctor/staff/pharmacist) → SetLanguage
  ▼
Controller (منطق العمل)
  ▼
Model / Eloquent / DB façade  ←→  MySQL
  ▼
Blade View (قالب AdminLTE) 
  ▼
HTML Response
```

### 2.4 بنية المجلدات

```
Smart-Hospitals/
├── app/                        # المنطق: 17 model مسطحة + Controllers + Middleware
│   ├── Http/Controllers/       # 16 controller (Patient, Medicine, Invoice, Report...)
│   ├── Http/Middleware/        # الصلاحيات: Admin/Doctor/Staff/Pharma/SetLanguage
│   └── *.php                   # الـ models في الجذر (اتفاقية المشروع — ليست app/Models)
├── config/                     # إعدادات Laravel (app/auth/database/mail/backup)
├── database/
│   ├── migrations/             # 24 migration (بما فيها 6 جداول جديدة)
│   └── seeders/                # 14 seeder (بيانات عرض + فريق العمل)
├── docs/                       # SRS + AI_Log (توثيق أكاديمي)
├── public/                     # الأصول المبنية: AdminLTE + bower_components + css/rtl.css
├── resources/
│   ├── views/                  # قوالب Blade (template/main + patient + invoices + reports/pdf)
│   └── lang/                   # ar/en/si (عربي RTL كامل)
├── routes/web.php              # كل المسارات (api.php stub فقط)
└── tests/Feature/              # 5 ملفات اختبار (25 اختباراً) + Example
```

---

## 3. الفصل الثالث: الميزات — كل ميزة بالتفصيل

### 3.1 تسجيل المرضى
**ما يفعله:** موظف الاستقبال يسجل مريضاً جديداً (خارجي أو رقود) ويطبع بطاقة باركود.
**كيف يعمل تقنياً:** `PatientController@registerPatient` يولّد رقم `YYMMDD+seq`، يحفظ الصورة base64، ويسجل `activity()`.
**التحديات:** الرقم اليدوي يتعارض مع الترقيم التلقائي.
**الحل:** `public $incrementing = false` في model `Patients` + FKs متوافقة يدوياً في الـ migrations.

### 3.2 المواعيد اليومية
**ما يفعله:** حجز برقم تسلسلي يومي + وقت جدولة مرئي.
**كيف يعمل:** `addChannel` يحسب `number = مواعيد اليوم + 1` ويحفظ `scheduled_at` (أُضيف في المرحلة 4).
**ميزة خاصة:** كل حجز يولّد **فاتورة حجز تلقائية** (1000 YER) مع رابط طباعة فوري.

### 3.3 الفحص الطبي
**ما يفعله:** الطبيب يدخل التشخيص والقياسات والأدوية من اقتراحات Typeahead.
**كيف يعمل:** سجل `Prescription` واحد + سطور `medicine_prescription`، والقياسات (ضغط/سكر/كولسترول) كمصفوفات JSON.
**التحديات:** قياسات متعددة القيم بتواريخ مختلفة.
**الحل:** تخزين JSON `{value, updated}` بدل أعمدة جامدة.

### 3.4 صرف الأدوية + المخزون (FEFO)
**ما يفعله:** الصيدلي يصرف أدوية الوصفة بضغطة لكل دواء.
**كيف يعمل:** `markIssued` يسحب من `medicine_stocks` مرتبة بالأقرب انتهاءً (`ISNULL(expiry_date), expiry_date ASC`).
**التحديات:** صيدليان يصرفان آخر وحدة معاً.
**الحل:** `DB::transaction` + `lockForUpdate()` — الثاني ينتظر ويرى الرصيد المحدّث.

### 3.5 الفواتير المستقلة لكل عملية
**ما يفعله:** كل عملية طبية = فاتورة منفصلة (`invoice_type`) بدل فاتورة يومية مجمعة، مع دفع جزئي/كلي وحالات `unpaid/partial/paid`.
**كيف يعمل:** `createForPatient($pid, $appId, $type)` + 5 hooks في نقاط العمل + `recalculate()` تلقائي عبر model events.
**القصة:** Rev1 (فاتورة يومية مجمعة) → Rev2 (مستقلة لكل عملية) → Rev3 (معالجة أخطاء + تدفق سلس).
**التحديات:** الدمج القديم أخفى تكلفة كل عملية.
**الحل:** فواتير مستقلة قابلة للطباعة فرادى.

### 3.6 التقارير PDF
**ما يفعله:** 4 تقارير (عيادة/حضور/منوّمون/شهري) + فواتير + إيصالات.
**كيف يعمل:** نفس قوالب Blade تُrender عبر `barryvdh/laravel-dompdf`.
**التحديات:** العربية تظهر مربعات في معظم مولدات PDF.
**الحل:** خط `DejaVu Sans` المضمّن + `dir="rtl"` (ملفات ~878KB تثبت التضمين).

### 3.7 الحضور
**ما يفعله:** تسجيل دخول/خروج + مدة محسوبة + تقويم.
**كيف يعمل:** `GET /attendance?finger=X&time=Y` — أول سجل بلا `end` يُغلق، وإلا يُفتح جديد.
**اكتشاف مهم:** جهاز البصمة يستخدم **GET لا POST** (أثبتناه باختبار فاشل أولاً: 405 على POST).

### 3.8 RTL والعربية
**ما يفعله:** عربية كاملة باتجاه صحيح في كل الصفحات.
**كيف يعمل:** `<html dir="rtl">` مشروط + `public/css/rtl.css` (يُحمّل للعربية فقط).
**التحديات:** نسخة AdminLTE المورّدة معدّلة (Sidebar بعرض مختلف).
**الحل:** قرأنا CSS الفعلي واكتشفنا **250px بدل 230px** المفترضة — درس: لا تخمّن الأرقام.

### 3.9 الإحصائيات
**ما يفعله:** رسوم Chart.js شهرية + Top أدوية.
**كيف يعمل:** استعلامات مجمعة + قائمة سنوات **ديناميكية** من بيانات المواعيد (كانت ثابتة 2018–2020).

---

## 4. الفصل الرابع: الأمان — كيف نحمي النظام

### 4.1 المصادقة
bcrypt لكل كلمات المرور + جلسات Laravel + `LoginController` يوجه إلى `/dash`. لا تخزين نصي إطلاقاً.

### 4.2 الصلاحيات
4 middleware على عمود `users.user_type` (مسجلة في `Kernel.php`):
`admin` (مدير فقط) / `doctor` (طبيب+مدير) / `staff` (استقبال+طبيب+مدير) / `pharmacist` (صيدلي+مدير).

### 4.3 CSRF
`VerifyCsrfToken` مفعّل + `@csrf` في كل نموذج + `_token` في كل AJAX.

### 4.4 SQL Injection
وجدنا **9 مواضع** `whereRaw` بمدخلات مستخدم مباشرة (`'$num'`, `$request->year`) → حوّلناها لـ parameter binding ودوال `whereYear/whereMonth/whereDate`.

### 4.5 Validation
تحقق في الـ controllers (تسجيل/فواتير/كلمات مرور `min:8`) — وخطوة مقترحة: Form Requests مخصصة.

### 4.6 قصة اكتشاف 3 ثغرات
1. **`POST /register` بلا `admin`**: أي مستخدم مسجل كان يصنع حساب مدير (الأخطر — أثبتناها حياً ثم أغلقناها).
2. **`POST /getattendancebyid`**: نسخة بلا `admin` من صفحة إدارية — أُغلقت.
3. **`GET /herbs`**: تفريغ جدول الأدوية كاملاً بلا حماية — أُضيف `auth`.

---

## 5. الفصل الخامس: قاعدة البيانات

### 5.1 الجداول الرئيسية (16 جدولاً)

| الجدول | الغرض | أهم الأعمدة |
|---|---|---|
| `users` | الموظفون | name, email, password(bcrypt), user_type, fingerprint |
| `patients` | المرضى (id يدوي) | id `YYMMDD+seq`, name, nic, telephone, SoftDeletes |
| `appointments` | المواعيد | patient_id, number (يومي), doctor_id?, scheduled_at, admit, completed |
| `prescriptions` | الوصفات | patient/appointment/doctor_id, diagnosis, bp/blood_sugar/cholestrol (JSON) |
| `medicines` | الأدوية | name_en/sinhala, qty (عدّاد صرف تراكمي) |
| `medicine_prescription` | pivot | prescription/medicine_id, note, issued |
| `medicine_stocks` ★ | المخزون الحقيقي | medicine_id, batch_number, quantity, expiry_date |
| `doctors` ★ | بيانات الأطباء | user_id, specialty, license_number |
| `services` ★ | الأسعار | code, name_ar/en, default_price |
| `invoices` ★ | الفواتير | invoice_number, invoice_type, total/paid_amount, status |
| `invoice_items` ★ | بنود الفاتورة | description, quantity, unit_price, total |
| `invoice_payments` ★ | المدفوعات | amount, method, received_by |
| `attendances` | الحضور | user_id, start, end |
| `wards` / `inpatients` | الرقود | ward_no/beds + patient/ward_id, discharged |
| `clinics` / `noticeboards` / `activity_log` | عيادات/إعلانات/تدقيق | — |

(★ = جداول صممناها في المراحل 3/4/6)

### 5.2 Relationships (ERD نصي)

```
users ──hasMany──► attendances
users ──1:1──► doctors
patients ──hasMany──► appointments ──hasOne──► prescriptions ──◄belongsToMany►── medicines
patients ──hasMany──► invoices ──hasMany──► invoice_items / invoice_payments
medicines ──hasMany──► medicine_stocks
wards ──hasMany──► inpatients ◄── patients
```

### 5.3 Migrations + Seeders
24 migration (6 جديدة) + 14 seeder. الترتيب حرج: `Users` أولاً (الحضور يحتاج users 1–5)، و`Services` قبل ما يعتمد الأسعار. `updateOrInsert` للـ seeders الآمنة التكرار.

### 5.4 Soft Deletes
`patients` (استعادة من الحذف) — والوصفات محفوظة تاريخياً للملف العلاجي.

---

## 6. الفصل السادس: الاختبارات — كيف نضمن الجودة

### 6.1 لماذا نختبر؟
لأن اكتشاف الكسر بعد التسليم يكلف أضعاف اكتشافه في دقيقة. كل مرحلة تُغلق بـ `test` أخضر.

### 6.2 PHPUnit 10
إطار الاختبار مع `<source>`-style config و`RefreshDatabase` لكل test.

### 6.3 RefreshDatabase + DB معزولة
`phpunit.xml` يضبط `DB_DATABASE=test` — قاعدة `laravel` التطويرية لا تُمسّ أبداً (تحققنا: 11 مستخدماً قبل وبعد).

### 6.4 27 اختباراً

| الملف | العدد | يغطي |
|---|---|---|
| PatientControllerTest | 6 | تسجيل/بحث/حذف/مواعيد |
| MedicineControllerTest | 4 | صلاحيات الصيدلية والمخزون |
| AttendanceControllerTest | 4 | صفحات الحضور + الكشك |
| InvoiceControllerTest | 8 | إنشاء/دفع/PDF/أنواع الفواتير |
| AnalyticsControllerTest | 3 | صلاحيات الإحصائيات |
| Example ×2 | 2 | دخان عام |

### 6.5 قصة اكتشاف خطأ الاختبار
اختبار البصمة أُرسل POST فسقط بـ **405** — فاكتشفنا أن المسار GET-only، وعدّلنا **الاختبار** لا الإنتاج. الاختبار الذي يفشل لسبب صحيح خير من اختبار ينجح دائماً.

---

## 7. الفصل السابع: التطوير — كيف بُني المشروع

### 7.1 المنهجية (AI + بشري)
مساعد برمجي يعرض **diffs أولاً** → مراجعة بشرية سطراً بسطر → "نفّذ" → تحقق (`php -l` + `test` + حي) → commit. التفاصيل في `docs/AI_Log.md`.

### 7.2 Git Workflow
مواصفة → فحص → diff → تنفيذ → تحقق → commit برسالة مرحلة → (push بإذن). لا دمج بدون مراجعة.

### 7.3 رسائل Commit (الـ 21 commit)

```
0c32970 Initial commit
c2cc997 Update hospital management system
098c2bc Merge GitHub repository
dd65ed8 stage1: install laravel 10 deps and fix 5.8 bootstrap code
d509298 upgrade: prepare legacy laravel 5.8 project (baseline)
1152d7f stage2: laravel 10 app code, migrations, seeders, phpunit10
19db5a0 Phase 1+2: quick wins + security hardening
b8da20b Phase 3: real medicine stock (FEFO, batch/expiry)
1cce5fe Phase 4: doctors table, scheduled_at, UI field
3be5691 Phase 6: invoice system (absorbed Phase 5 PDF work)
3032418 Phase 6-Rev2: independent invoice per operation
276803a Phase 6-Rev3: PDF error handling + smooth appointment flow
f788ab2 Phase 7: feature tests (27 passed) with isolated test DB
b00e141 Phase 7: dynamic statistics years
9a86236 docs: AGENTS.md
a2a8be4 docs: Mini-SRS
4bc8845 seed: team members as admin users
d1f023b seed: register TeamMembersSeeder
8b1f582 / b4621f4 / 1e742f7 docs: README + AI_Log + maintainer
```

### 7.4 المراحل الثمانية (كاملة)

#### Phase 1: Quick Wins
- **المشاكل:** typo `Admssin`، شهر مثبت `2`، مسار `/wardlist` بلا حماية.
- **الحل:** 3 إصلاحات + حذف علاقة ميتة.
- **Commit:** `19db5a0` (مدمج مع 2).

#### Phase 2: Security Hardening
- **المشاكل:** 9 حقن SQL + 3 فجوات middleware.
- **الأخطر:** `POST /register` → تصعيد صلاحيات (أي مستخدم → admin).
- **Commit:** `19db5a0` (مدمج).

#### Phase 3: Medicine Stock
- **المشكلة:** `qty` عدّاد صرف لا مخزون.
- **الحل:** جدول `medicine_stocks` + FEFO + `lockForUpdate` + صفحة تنبيهات.
- **Commit:** `b8da20b`.

#### Phase 4: Doctors + Scheduling
- **ما أُضيف:** جدول `doctors` + عمود `scheduled_at` + حقل datetime في الواجهة.
- **Commit:** `1cce5fe`.

#### Phase 5: PDF Reports
- **ما أُضيف:** 4 تقارير dompdf + دوال controller (بلا commit مستقل — دُمجت في `3be5691`).
- **Commit:** (ضمن `3be5691`).

#### Phase 6: Invoices (Rev1+Rev2+Rev3)
- **القصة:** فاتورة مجمعة → مستقلة لكل عملية → معالجة أخطاء وتدفق سلس.
- **Commits:** `3be5691`، `3032418`، `276803a`.

#### Phase 7: Tests
- **ما أُضيف:** 25 اختبار Feature جديد (27 مع القديمة) + قاعدة `test` معزولة + سنوات ديناميكية.
- **Commits:** `f788ab2`، `b00e141`.

#### Phase 8: RTL
- **ما أُصلح:** `rtl.css` شامل + اكتشاف عرض Sidebar الفعلي 250px + محاذاة Header.
- **Commit:** (قيد الإغلاق — الملف في working tree).

---

## 8. الفصل الثامن: المشاكل الهندسية وحلولها

### 8.1 SQL Injection في whereRaw
**اكتشاف:** 9 مواضع بمدخلات مستخدم مباشرة (`'$num'`, `$request->year`).
**الحل:** Parameter binding + `whereYear/whereMonth/whereDate`.
**الدرس:** لا تثق بأي مدخل مستخدم — أبداً.

### 8.2 تصعيد الصلاحيات
**اكتشاف:** `POST /register` بلا `admin` — أي مسجل دخول يصنع مديراً.
**الحل:** `middleware('auth','admin')` + إثبات حي (302 + ثبات العدد).
**الدرس:** كل route يحتاج تحقق صلاحية صريحاً — الافتراض الآمن مرفوض.

### 8.3 المخزون الوهمي
**اكتشاف:** `qty` تزيد عند الصرف (عدّاد) بينما DemoDataSeeder عاملها رصيداً.
**الحل:** جدول `medicine_stocks` منفصل + إبقاء العدّاد للتاريخ.
**الدرس:** فرّق بين العدّاد التراكمي والرصيد الحي — لا تدمجهما في عمود.

### 8.4 الفاتورة الشاملة
**اكتشاف:** فاتورة يومية واحدة تخفي تكلفة كل عملية.
**الحل:** `invoice_type` + فاتورة مستقلة لكل عملية (Rev2).
**الدرس:** اسأل عن المتطلب قبل التنفيذ — Rev1 أُعيدت كتابتها.

### 8.5 RTL مكسور
**اكتشاف:** Sidebar يطفو فوق المحتوى رغم قواعد صحيحة ظاهرياً.
**الحل:** قراءة CSS الفعلي: العرض 250px لا 230px المفترضة.
**الدرس:** اقرأ الملف المورّد فعلياً — لا تفترض قيم المكتبات.

### 8.6 اختبار خاطئ
**اكتشاف:** POST لمسار GET-only أعطى 405.
**الحل:** عدّلنا الاختبار (GET query) لا الإنتاج.
**الدرس:** فشل الاختبار الصحيح ميزة — كشف عقد الكشك الحقيقي.

---

## 9. الفصل التاسع: ربط المشروع بمحاضرات د. ساهر

### 9.1 المحاضرة الأولى — هندسة المتطلبات
**المستودع:** `Qaidsaher/Software-Engineering-`
**ما درّسه:** SRS، User Stories، Edge Cases، Kanban، Git، AI_Log.
**ما طبّقناه:** `docs/SRS.md` (8 FRs + 5 NFRs + 10 edge cases + 5 stories)، سجل commits مرحلي، `docs/AI_Log.md`.

### 9.2 المحاضرة الثانية — MVC + CRUD
**المستودع:** `Qaidsaher/software-engineering-lab1`
**ما درّسه:** MVC، Routes، Migrations، Form Requests، Pest.
**ما طبّقناه:** MVC كامل (16 controller)، ~90 route، 24 migration، validation في controllers (Form Requests خطوة مقترحة)، PHPUnit بدل Pest (المتاح في المشروع).

### 9.3 محاضرة OOP
**المستودع:** `Qaidsaher/software-engineering-lab2`
**ما درّسه:** Class، Interface، Abstract، Trait، DI.
**ما طبّقناه:** Models/Controllers كـ classes، scopes، accessors، علاقات Eloquent — والـ Interfaces/DI في خطة Service Layer (الفصل 10).

### 9.4 محاضرة REST API
**المستودع:** `Qaidsaher/laravel-api-from-zero-to-testing`
**ما درّسه:** API Resources، Testing.
**ما طبّقناه:** `routes/api.php` stub فقط (النظام ويب خالص) + ثقافة testing كاملة (27 اختباراً) — ونقترح API حقيقياً مستقبلاً.

### 9.5 المشروع المرجعي IBBDev
**المستودع:** `Qaidsaher/IBBDev`
**ما يحتويه:** Service Layer + Interface + DI + SOLID.
**ما يجب أن نضيفه:** `InvoiceService`، `StockService`، `ReportService` (التفاصيل في الفصل 10).

---

## 10. الفصل العاشر: المعمارية المستهدفة (Service Layer)

### 10.1 الوضع الحالي
MVC كامل ومنظم — لكن منطق العمل (الفوترة، FEFO، التقارير) يعيش داخل الـ Controllers مباشرة. يعمل، لكنه يصعّب إعادة الاستخدام والاختبار المعزول.

### 10.2 المستهدف (مثل IBBDev)
```php
// مثال: InvoiceServiceInterface + InvoiceService + Binding
interface InvoiceServiceInterface {
    public function createForPatient($patientId, $appointmentId, $type);
}
class InvoiceService implements InvoiceServiceInterface { /* المنطق من Controller */ }
// في AppServiceProvider: bind(InvoiceServiceInterface::class, InvoiceService::class)
```

### 10.3 الخدمات المقترحة
| Service | Interface | المسؤولية |
|---|---|---|
| InvoiceService | InvoiceServiceInterface | إنشاء الفواتير + البنود + إعادة الحساب |
| StockService | StockServiceInterface | السحب FEFO + التنبيهات |
| ReportService | ReportServiceInterface | بناء datasets التقارير + PDF |
| AttendanceService | AttendanceServiceInterface | فتح/إغلاق سجلات الحضور |

### 10.4 الفائدة
SOLID (واجهة واحدة لكل خدمة)، اختبار وحدة بلا HTTP، controllers نحيفة (تحقق + استدعاء)، وإمكانية تبديل التنفيذ (DB/Cache/API) دون لمس الواجهات.

---

## 11. الفصل الحادي عشر: كيف تشغّل المشروع

### 11.1 المتطلبات
PHP 8.1+ (مجرّب 8.2.12) + Composer + MySQL/MariaDB + (اختياري: Node للبناء فقط).

### 11.2 الخطوات (8 خطوات bash)
```bat
copy .env.example .env
:: عدّل DB_DATABASE / DB_USERNAME / DB_PASSWORD
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
:: افتح http://localhost:8000
```
(ملاحظة Windows: `copy` لا `cp`. ملاحظة المنفذ: `APP_URL` يجب أن يطابق المنفذ الفعلي وإلا سقطت الأصول — درس موثق.)

### 11.3 حسابات الاختبار
4 seed (كلمة المرور `12345678`): admin `shakthisachintha@...`، doctor `ssakunchamikara@...`، pharmacist `sachinthaindu95@...`، general `sanduniiresha1029@...` — + 4 أعضاء الفريق (admin، كلمات مرور شخصية).

### 11.4 استكشاف الأخطاء
| العرض | السبب المحتمل | الحل |
|---|---|---|
| صفحة بلا تنسيق | `APP_URL` بلا منفذ / سيرفران على نفس المنفذ | طابق المنفذ + سيرفر واحد |
| 404 الأصول | docroot خاطئ | شغّل من جذر المشروع |
| migrate يفشل FK | signed/unsigned | لا تعدّل الأعمدة — راجع الـ migrations |
| test يمسح البيانات | `DB_DATABASE` غير مضبوط | تأكد من `test` في `phpunit.xml` |

---

## 12. الفصل الثاني عشر: الإحصاءات النهائية

### 12.1 الأرقام (مفحوصة من Git والكود بتاريخ 2026-09-27)
- **21 commit** (3 تأسيسية + 9 مراحل + seeders + توثيق).
- **27 اختباراً** (43 assertions) — كلها خضراء.
- **17 model**، **16 controller**، **~90 route**.
- **24 migration**، **14 seeder**، 3 لغات.
- **6 جداول جديدة** + عمودان (`scheduled_at`، `invoice_type`).

### 12.2 الميزات المُنجزة
تسجيل + مواعيد + فحص + صرف FEFO + مخزون + فواتير مستقلة + دفع + 4 تقارير PDF + حضور بصمة + إحصائيات + 3 لغات RTL + 4 أدوار + تدقيق نشاطات.

### 12.3 ما يمكن إضافته مستقبلاً
1. Service Layer (الفصل 10). 2. REST API حقيقي. 3. Form Requests. 4. تطبيق موبايل. 5. بوابة دفع. 6. إشعارات SMS/بريد. 7. تعدد الفروع. 8. لوحة CEO تنفيذية. 9. نسخ احتياطي سحابي. 10. تكامل أجهزة طبية.

---

## 13. الفصل الثالث عشر: الدروس المستفادة

### 13.1 دروس تقنية
1. اقرأ ملفات المورّدين فعلياً (250px لا 230px).
2. MySQL-only: دوال مثل `CURDATE()` تمنع sqlite في الاختبارات.
3. `?->` و`??` يحميان الـ views من البيانات الناقصة.
4. `lockForUpdate` ضروري لأي رصيد يُسحب تزامنياً.
5. خطوط PDF يجب تضمينها (DejaVu) لا افتراضها.
6. أعمدة `patients.id` اليدوية تتطلب FKs متوافقة يدوياً.
7. `redirect()->back()` ليس 403 — اختبر السلوك الفعلي لا المتوقع.
8. `view:cache` يكشف أخطاء Blade قبل المتصفح.

### 13.2 دروس هندسية
1. فرّق العدّاد عن الرصيد (درس `qty`).
2. الفاتورة المستقلة أوضح من المجمعة للتدقيق.
3. المواصفة الناقصة تُوقف التنفيذ — التخمين دين تقني.
4. الـ commit الصغير الواضح خير من الكبير المبهم.
5. التوثيق أثناء العمل لا بعده (وإلا نُسيت الأسباب).

### 13.3 دروس من التعاون مع AI
1. "اعرض الـ diff أولاً" أهم قاعدة — منعت كوارث.
2. AI سريع في الكتابة، بطيء في السياق — زوّده بالملفات.
3. الأخطاء اللغوية (403/302، Admin/admin) يكشفها الفحص لا الثقة.
4. قسّم العمل (بنداً بنداً) بدل الدفعات العمياء.
5. سجّل القرارات فوراً (AI_Log) وإلا تبخرت الأسباب.

---

## 14. الفصل الرابع عشر: شكر وتقدير

### 14.1 شكر د. ساهر الهمداني
شكراً دكتور ساهر — لم تعلمنا البرمجة فقط، بل **الهندسة**: أن المواصفة قبل الكود، وأن الاختبار جزء من البناء لا زينة بعده، وأن Git سجل قرارات لا مخزن ملفات. مستودعاتك (Software-Engineering وlabs وIBBDev) كانت خارطة الطريق العملية لهذا المشروع، من SRS إلى Service Layer. أي نضج في هذا العمل سببه منهجك.

### 14.2 شكر الفريق
- **معتز** (Backend/DB/Security): المعمارية، الأمان، قاعدة البيانات، وإدارة المراحل الثماني.
- **صقر** (Frontend/RTL/UI): الواجهات، العكس العربي الكامل، تجربة المستخدم.
- **محمد** (Invoices/Reports/PDF): نظام الفواتير بمراجعاته الثلاث وتقارير dompdf.
- **أنس** (Tests/Docs/QA): 27 اختباراً والتوثيق الأكاديمي وضبط الجودة.

### 14.3 شكر المجتمع
شكراً لمجتمعات Laravel وPHP مفتوحة المصدر: الإطار، الحزم (spatie، dompdf، barcode)، وكل من وثّق مشكلة واجهناها وحللناها بفضلهم.

---

## 15. الفصل الخامس عشر: المراجع

### 15.1 محاضرات د. ساهر (5 مستودعات)
1. `Qaidsaher/Software-Engineering-` — المتطلبات والمنهجية.
2. `Qaidsaher/software-engineering-lab1` — MVC وCRUD.
3. `Qaidsaher/software-engineering-lab2` — OOP.
4. `Qaidsaher/laravel-api-from-zero-to-testing` — API والاختبار.
5. `Qaidsaher/IBBDev` — المشروع المرجعي (Service Layer).

### 15.2 Laravel Docs
laravel.com/docs/10.x (Eloquent، Testing، Blade، Migrations).

### 15.3 كتب SOLID
"Clean Architecture" — Robert C. Martin (مبادئ SOLID المطبقة في الفصل 10).

### 15.4 أدوات AI
مساعد برمجي (agent) بمنهجية diffs-أولاً — التفاصيل الكاملة في `docs/AI_Log.md`.

---

## 16. الفصل السادس عشر: المعلومات الفنية

### 16.1 روابط
- المستودع: `Smart-Hospitals` (GitHub).
- المواصفات: `docs/SRS.md` — سجل AI: `docs/AI_Log.md` — تعليمات المطورين: `AGENTS.md`.

### 16.2 الترخيص
MIT — مشروع أكاديمي لمقرر البرمجيات (عملي).

### 16.3 الاتصال
| العضو | البريد |
|---|---|
| معتز بشير محمد مصلح | mtz360926@gmail.com |
| صقر نبيل قاسم أحمد | engsaqrnabil@gmail.com |
| محمد سيف عبده مسعد قايد | mohammed.developer.pr@gmail.com |
| أنس محمد أمين طه الإدريسي | anas.idrisi@shifa-hospital.com |

---

## 17. الخاتمة

تعلمنا في هذا المشروع أن البرمجيات الحقيقية ليست كوداً يعمل فحسب، بل **قرارات موثقة**: لماذا جدول منفصل للمخزون؟ لماذا فاتورة مستقلة؟ لماذا 250px؟ كل إجابة في هذا الملف كلفَتنا فحصاً وخطأً وتصحيحاً — وهذا هو التعليم الهندسي بعينه.

نطمح أن يتحول النظام من مشروع تخرج إلى نظام يعمل فعلاً في مستشفى يمني، وأن نكمل الطريق: Service Layer، ثم API، ثم موبايل. دكتور ساهر: شكراً لأنك علمتنا أن نسأل "لماذا" قبل "كيف" — هذه الوثيقة كلها إجابات عن "لماذا".

---

**آخر تحديث:** 2026-09-27
**الإصدار:** 1.0
**الفريق:** معتز، صقر، محمد، أنس
