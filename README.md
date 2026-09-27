<div align="center">

# 🏥 Smart Hospitals — مستشفى الشفاء

### نظام إدارة مستشفيات متكامل | Complete Hospital Management System

**مبني بـ Laravel 10** • **واجهة عربية RTL** • **4 أدوار** • **3 لغات** • **27 اختبار**

[![Laravel](https://img.shields.io/badge/Laravel-10.50.3-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2.12-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Tests](https://img.shields.io/badge/Tests-27%20Passed-00C853?style=for-the-badge)](#-الاختبارات)
[![Commits](https://img.shields.io/badge/Commits-27+-1976D2?style=for-the-badge&logo=git&logoColor=white)](#-سجل-المراحل)
[![License](https://img.shields.io/badge/License-MIT-yellow?style=for-the-badge)](LICENSE)

[نظرة عامة](#-نظرة-عامة) •
[الميزات](#-الميزات-الرئيسية) •
[التثبيت](#-التثبيت-والبدء) •
[الفريق](#-فريق-التطوير) •
[التوثيق](#-التوثيق-الكامل) •
[المساهمة](#-المساهمة)

</div>

---

## 📖 جدول المحتويات

- [نظرة عامة](#-نظرة-عامة)
- [لقطات الشاشة](#-لقطات-الشاشة)
- [الميزات الرئيسية](#-الميزات-الرئيسية)
- [التقنيات المستخدمة](#-التقنيات-المستخدمة)
- [المعمارية](#-المعمارية)
- [بنية المشروع](#-بنية-المشروع)
- [التثبيت والبدء](#-التثبيت-والبدء)
- [حسابات الاختبار](#-حسابات-الاختبار)
- [الاختبارات](#-الاختبارات)
- [فريق التطوير](#-فريق-التطوير)
- [التوثيق الكامل](#-التوثيق-الكامل)
- [سجل المراحل](#-سجل-المراحل)
- [خارطة الطريق](#-خارطة-الطريق)
- [المساهمة](#-المساهمة)
- [الترخيص](#-الترخيص)

---

## 🎯 نظرة عامة

<div align="right">

**Smart Hospitals** نظام إدارة مستشفيات متكامل يدير دورة العمل الطبية الكاملة:
من تسجيل المريض → حجز الموعد → الفحص الطبي → الوصفة → صرف الدواء → الفاتورة → التقرير — كل ذلك في المتصفح، بالعربية أو الإنجليزية أو السنهالية.

بُني النظام ليحل محل السجلات الورقية في المستشفيات الصغيرة والمتوسطة: بحث فوري عن المرضى، مواعيد مرقمة بعدل، وصفات مؤرشفة، مخزون مراقب بالدفعات، فواتير مستقلة لكل عملية، وتقارير PDF بضغطة واحدة.

**المقرر:** البرمجيات (عملي) — د. ساهر الهمداني
**الحالة:** ✅ مكتمل — 8 مراحل تطويرية + 27 اختباراً ناجحاً

</div>

---

## 📸 لقطات الشاشة

> لقطات حية من النظام (لوحة التحكم، إنشاء موعد، صرف الأدوية، صفحة المخزون، تقرير الفواتير، التقارير PDF).

| الشاشة | الوصف |
|---|---|
| لوحة التحكم `/dash` | إحصاءات (أطباء/موظفون/صيادلة/منوّمون) + لوحة إعلانات |
| إنشاء موعد `/createchannel` | بحث برقم التسجيل + وقت الجدولة + رابط طباعة الفاتورة |
| صرف الأدوية `/issue/{id}` | جدول الوصفة + أزرار صرف + عدّاد الكميات |
| المخزون `/medicine-stocks` | تنبيهات (نافد/منخفض/قارب الانتهاء) + جدول الدفعات |
| الفواتير `/invoices` | فلترة (حالة/نوع/تاريخ) + دفع + PDF |

---

## ✨ الميزات الرئيسية

### 🏥 المرضى والمواعيد
- تسجيل خارجي + رقود برقم يدوي `YYMMDD+seq` وبطاقة باركود (Code39).
- بحث فوري (اسم/وطني/هاتف) يشمل المحذوفين ناعماً + تاريخ علاجي كامل.
- مواعيد يومية بترقيم تلقائي + `scheduled_at` + فاتورة حجز تلقائية (1000 YER).

### 🩺 الفحص والوصفات
- تشخيص + قياسات (ضغط/سكر/كولسترول بصيغة JSON مؤرخة) + أدوية باقتراحات.
- فاتورة كشف تلقائية (5000 YER) + تحديث الموعد `completed=YES`.

### 💊 الصيدلية والمخزون
- صرف FEFO (الأقرب انتهاءً أولاً) مع `lockForUpdate` ضد الصرف المتزامن.
- منع الصرف عند النفاد (`OUT_OF_STOCK`) أو الانتهاء (`EXPIRED_ONLY`).
- صفحة مخزون: 3 تنبيهات + شارات `OK/LOW/OUT`.

### 💰 الفواتير والمدفوعات
- فاتورة **مستقلة لكل عملية** (`appointment/consultation/medicine/ward`).
- دفع كلي/جزئي (`unpaid/partial/paid` يُحسب تلقائياً) + PDF عربي + تقرير شهري.

### 📄 التقارير والإحصائيات
- 4 تقارير PDFRTL (عيادة/حضور/منوّمون/شهري) + رسوم Chart.js + سنوات ديناميكية.

### 🔐 الأمان والصلاحيات
- bcrypt + CSRF + parameter binding (بعد إصلاح 9 حقن `whereRaw`).
- 4 أدوار عبر middleware: `admin/doctor/staff(pharmacist/general)`.

### 🌍 اللغات
- عربي (RTL كامل: `dir` مشروط + `rtl.css`) + إنجليزي + سنهالي.

---

## 🛠 التقنيات المستخدمة

| التقنية | الإصدار | الاستخدام |
|---|---|---|
| Laravel | 10.50.3 | Backend MVC |
| PHP | 8.2.12 | اللغة |
| MySQL / MariaDB | 5.7+ | قاعدة البيانات |
| Blade | مدمج | القوالب |
| AdminLTE | 2.4.12 | لوحة التحكم |
| Bootstrap | 3.4.1 | التنسيق |
| jQuery | 3.x | AJAX والتفاعل |
| Chart.js / DataTables | مدمجة | الرسوم والجداول |
| dompdf | 3.x | PDF عربي (DejaVu) |
| spatie/activitylog | 4.x | سجل التدقيق |
| milon/barcode | 10.x | الباركود |
| PHPUnit | 10 | 27 اختباراً (43 assertions) |

---

## 🏗 المعمارية

```
Browser
  │ HTTP
  ▼
routes/web.php  (~90 مساراً — api.php stub واحد فقط)
  ▼
Middleware: auth → role → SetLanguage
  ▼
Controller (16) → Eloquent/DB (17 model مسطحة في app/)
  ▼
Blade (AdminLTE + RTL مشروط) → HTML
```

**قرارات معمارية موثقة** (التفاصيل في `AGENTS.md`): لا `app/Models`، لا API حقيقي، `patients.id` يدوي، `medicines.qty` عدّاد صرف بينما المخزون في `medicine_stocks`، الأسعار من جدول `services`.

---

## 📁 بنية المشروع

```
Smart-Hospitals/
├── app/                    # 17 model + 16 controller + middleware الأدوار
├── config/                 # app/auth/database/mail/backup
├── database/               # 24 migration + 14 seeder
├── docs/                   # SRS + AI_Log + user-stories + acceptance + edge-cases + api
├── public/                 # AdminLTE + bower_components + css/rtl.css (أصول جاهزة)
├── resources/
│   ├── views/              # Blade: template + patient + invoices + reports/pdf + medicine
│   └── lang/               # ar (RTL) + en + si
├── routes/web.php          # كل المسارات
├── tests/Feature/          # 5 ملفات (25 اختباراً) + Example
├── AGENTS.md               # تعليمات المطورين + سجل المراحل
├── MOATAZ.md               # الدليل الهندسي الكامل (17 فصلاً)
├── composer.json / phpunit.xml
└── README.md               # هذا الملف
```

---

## 🚀 التثبيت والبدء

### المتطلبات
- PHP 8.1+ (مجرّب 8.2.12)
- Composer
- MySQL 5.7+ / MariaDB
- (اختياري) Node.js — للبناء فقط، الأصول جاهزة

### الخطوات (Windows)

```bat
git clone https://github.com/Mohammed-Saif-abdo-musaed-maid/Smart-Hospitals.git
cd Smart-Hospitals
copy .env.example .env
:: عدّل .env: DB_DATABASE / DB_USERNAME / DB_PASSWORD
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

افتح `http://localhost:8000` — **تنبيه:** `APP_URL` في `.env` يجب أن يطابق المنفذ الفعلي وإلا سقطت ملفات CSS/JS.

---

## 🔑 حسابات الاختبار

كلمة مرور حسابات الـ seed: `12345678`

| البريد | الدور |
|---|---|
| shakthisachintha@gmail.com | admin |
| ssakunchamikara@gmail.com | doctor |
| sachinthaindu95@gmail.com | pharmacist |
| sanduniiresha1029@gmail.com | general |

> ⚠️ لا ترفع `.env` إلى Git أبداً (مُتجاهَل في `.gitignore`).

---

## 🧪 الاختبارات

```bat
php artisan test
```

- **27 اختباراً ناجحاً** (43 assertions): مرضى (6) + صيدلية (4) + حضور (4) + فواتير (8) + إحصائيات (3) + دخان (2).
- تعمل على قاعدة **`test` معزولة** (`DB_DATABASE=test` في `phpunit.xml`) — بيانات التطوير في `laravel` لا تُمسّ أبداً.

---

## 👥 فريق التطوير

| # | الاسم | البريد | المسؤولية |
|---|---|---|---|
| 1 | معتز بشير محمد مصلح | mtz360926@gmail.com | Backend + Database + Security (قائد) |
| 2 | صقر نبيل قاسم أحمد | engsaqrnabil@gmail.com | Frontend + RTL + UI/UX |
| 3 | محمد سيف عبده مسعد قايد | mohammed.developer.pr@gmail.com | Invoices + Reports + PDF |
| 4 | أنس محمد أمين طه الإدريسي | anas.idrisi@shifa-hospital.com | Tests + Documentation + QA |
| 5 | الحسين إسماعيل محمد أحمد الخياط | alhussain.ismail@shifa-hospital.com | Database Design + Migrations + ERD |

---

## 📚 التوثيق الكامل

| الملف | المحتوى |
|---|---|
| `docs/SRS.md` | مواصفات المتطلبات (8 FRs + 5 NFRs + 10 edge + 5 stories) |
| `docs/user-stories.md` | 18 قصة مستخدم بالأدوار |
| `docs/acceptance-criteria.md` | معايير Given-When-Then لكل FR |
| `docs/edge-cases.md` | 38 حالة حافة بسلوكها |
| `docs/api.md` | الـ stub الحالي + API مقترح (Sanctum) |
| `docs/AI_Log.md` | سجل التطوير بمساعدة AI والمراجعة البشرية |
| `AGENTS.md` | تعليمات المطورين + quirks + سجل المراحل |
| `MOATAZ.md` | الدليل الهندسي الكامل (17 فصلاً) |

---

## 🗓 سجل المراحل

| Commit | المرحلة |
|---|---|
| `19db5a0` | 1+2: إصلاحات سريعة + تقوية أمنية |
| `b8da20b` | 3: مخزون FEFO بدفعات وانتهاء |
| `1cce5fe` | 4: جدول أطباء + `scheduled_at` |
| `3be5691` | 6: نظام الفواتير (+ عمل PDF المرحلة 5) |
| `3032418` | 6-Rev2: فاتورة مستقلة لكل عملية |
| `276803a` | 6-Rev3: معالجة أخطاء PDF + تدفق سلس |
| `f788ab2` | 7: 27 اختبار Feature |
| `b00e141` | 7: سنوات ديناميكية |
| `dad0013` | 8: إصلاحات RTL العربية |

---

## 🗺 خارطة الطريق

- [ ] Service Layer (`InvoiceService` / `StockService` / `ReportService` + Interfaces + DI)
- [ ] REST API حقيقي (Sanctum + Resources + Tests)
- [ ] Form Requests مخصصة بدل validation المضمن
- [ ] إشعارات SMS/بريد + نسخ سحابي
- [ ] تطبيق موبايل + تعدد الفروع

---

## 🤝 المساهمة

1. انسخ المستودع (Fork) وأنشئ فرعاً باسم الميزة.
2. اعرض الـ diff للمراجعة قبل الدمج (لا دمج مباشر على `main`).
3. حافظ على `php artisan test` أخضر (27/27).
4. لا تُدخل `.env` أو أسراراً في أي commit.

---

## 📄 الترخيص

MIT — مشروع أكاديمي (مقرر البرمجيات العملي — د. ساهر الهمداني).
