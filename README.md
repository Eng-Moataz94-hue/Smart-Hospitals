# Smart Hospitals — مستشفى الشفاء
## Hospital Management System (Laravel 10)

![Laravel](https://img.shields.io/badge/Laravel-10.50-red)
![PHP](https://img.shields.io/badge/PHP-8.2-blue)
![MySQL](https://img.shields.io/badge/MySQL-MariaDB-orange)
![Tests](https://img.shields.io/badge/Tests-27%20passed-success)

نظام إدارة مستشفيات متكامل: تسجيل مرضى (خارجي + رقود) ببطاقة باركود، مواعيد يومية بترقيم تلقائي، فحص طبي ووصفات، مخزون أدوية بنظام الدفعات (FEFO)، فواتير مستقلة لكل عملية، تقارير PDF بالعربية، حضور موظفين، إحصائيات — بـ 4 أدوار و3 لغات (عربي RTL + إنجليزي + سنهالي).

**المقرر:** البرمجيات (عملي) — د. ساهر الهمداني
**الحالة:** مكتمل — 8 مراحل تطويرية، 27 اختباراً ناجحاً.

---

## الفريق

| # | الاسم | البريد | المسؤولية |
|---|---|---|---|
| 1 | معتز بشير محمد مصلح | mtz360926@gmail.com | Backend + Database + Security |
| 2 | صقر نبيل قاسم أحمد | engsaqrnabil@gmail.com | Frontend + RTL + UI/UX |
| 3 | محمد سيف عبده مسعد قايد | mohammed.developer.pr@gmail.com | Invoices + Reports + PDF |
| 4 | أنس محمد أمين طه الإدريسي | anas.idrisi@shifa-hospital.com | Tests + Documentation + QA |

---

## التقنيات

| التقنية | الإصدار | الاستخدام |
|---|---|---|
| Laravel | 10.50.3 | Backend (MVC) |
| PHP | 8.2 | لغة البرمجة |
| MySQL / MariaDB | 5.7+ | قاعدة البيانات |
| Blade | مدمج | القوالب |
| AdminLTE | 2.4.12 | لوحة التحكم |
| Bootstrap | 3.4.1 | التنسيق |
| jQuery / Chart.js / DataTables | مدمجة | الواجهة والرسوم |
| barryvdh/laravel-dompdf | 3.x | تقارير PDF |
| spatie/laravel-activitylog | 4.x | سجل النشاطات |
| milon/barcode | 10.x | باركود بطاقة المريض |
| PHPUnit | 10 | الاختبارات (27 ناجحة، 43 assertions) |

---

## الميزات

- **المرضى:** تسجيل خارجي/رقود برقم يدوي `YYMMDD+seq`، بطاقة باركود، بحث، تاريخ علاجي، حذف ناعم.
- **المواعيد:** ترقيم يومي + وقت جدولة `scheduled_at` + فاتورة حجز تلقائية (1000 YER).
- **الفحص:** تشخيص + ضغط/سكر/كولسترول (JSON) + وصفة + فاتورة كشف تلقائية (5000 YER).
- **الصيدلية:** صرف من المخزون بنظام FEFO مع `lockForUpdate`، منع الصرف عند النفاد/الانتهاء، صفحة مخزون بتنبيهات (نافد/منخفض/قارب الانتهاء).
- **الفواتير:** مستقلة لكل عملية (`appointment/consultation/medicine/ward`)، دفع كلي/جزئي، حالات (`unpaid/partial/paid`)، PDF، تقرير شهري.
- **التقارير:** 4 تقارير PDF بالعربية (عيادة/حضور/منوّمون/شهري) بخط DejaVu.
- **الحضور:** كشك بصمة (`GET /attendance?finger=&time=`) + صفحات متابعة + تقارير.
- **الصلاحيات:** `admin/doctor/pharmacist/general` عبر role middleware على `users.user_type`.
- **اللغات:** عربي (RTL كامل) + إنجليزي + سنهالي.

---

## التثبيت (Windows)

```bat
copy .env.example .env
:: عدّل .env: DB_DATABASE / DB_USERNAME / DB_PASSWORD
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

افتح `http://localhost:8000` وسجّل الدخول (كلمة المرور الافتراضية للseed: `12345678`):

| البريد | الدور |
|---|---|
| shakthisachintha@gmail.com | admin |
| ssakunchamikara@gmail.com | doctor |
| sachinthaindu95@gmail.com | pharmacist |
| sanduniiresha1029@gmail.com | general |

> لا ترفع `.env` إلى Git أبداً. `npm` اختياري (الأصول مبنية مسبقاً في `public/`).

---

## الاختبارات

```bat
php artisan test
```

27 اختبار Feature/Unit على قاعدة `test` معزولة (`DB_DATABASE=test` في `phpunit.xml`) — بيانات التطوير محمية من `RefreshDatabase`.

---

## البنية باختصار

```
Request → routes/web.php (~90 مساراً، لا API حقيقي)
  → middleware (auth + role + SetLanguage)
  → Controller → Eloquent/DB (17 model في app/ مباشرة)
  → Blade (AdminLTE) → Response
```

ملاحظات معمارية: لا جدول `doctors` منفصل قديماً (أُضيف في المرحلة 4)؛ `patients.id` يدوي؛ `medicines.qty` عدّاد صرف تراكمي بينما المخزون الحقيقي في `medicine_stocks`؛ الأسعار من جدول `services`.

---

## سجل المراحل

| Commit | المرحلة |
|---|---|
| `19db5a0` | 1+2: إصلاحات سريعة + تقوية أمنية (bindings، ثغرات middleware) |
| `b8da20b` | 3: مخزون أدوية حقيقي (FEFO، دفعات، انتهاء) |
| `1cce5fe` | 4: جدول أطباء + `scheduled_at` + حقل الواجهة |
| `3be5691` | 6: نظام الفواتير (واستوعب عمل PDF المرحلة 5) |
| `3032418` | 6-Rev2: فاتورة مستقلة لكل عملية |
| `276803a` | 6-Rev3: معالجة أخطاء PDF + تدفق حجز سلس |
| `f788ab2` | 7: اختبارات Feature (27 ناجحة) |
| `b00e141` | 7: سنوات ديناميكية في الإحصائيات |
| `9a86236` | توثيق AGENTS.md |

التفاصيل الكاملة: `docs/SRS.md` (مواصفات أكاديمية) و`AGENTS.md` (تعليمات المطورين).

---

## الرخصة

MIT — مشروع أكاديمي (مقرر البرمجيات العملي).
