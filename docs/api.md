# API Documentation — توثيق الـ API
## Smart Hospitals — نظام إدارة مستشفى الشفاء

**الفريق:** معتز بشير محمد مصلح، صقر نبيل قاسم أحمد، محمد سيف عبده مسعد قايد، أنس محمد أمين طه الإدريسي
**المقرر:** البرمجيات (عملي) — د. ساهر الهمداني
**التاريخ:** 2026-09-27

---

## مقدمة

Smart Hospitals نظام **ويب أولاً** — جميع الميزات عبر `routes/web.php` (~90 route).
الـ API **مقترح** كتوسعة مستقبلية (يتبع منهج `laravel-api-from-zero-to-testing`).

---

## 1. الوضع الحالي — API Stub

`routes/api.php` يحتوي مساراً تجريبياً واحداً (متحقق منه حرفياً):

```php
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
```

**غير مستخدم في الإنتاج** — كل التدفقات الحية (حجز/فحص/صرف/فواتير) تستخدم مسارات الويب مع `auth` session وCSRF.

---

## 2. API المقترح (مستقبلي)

### 2.1 Authentication
```
POST /api/v1/auth/login
POST /api/v1/auth/logout
GET  /api/v1/auth/me
```

### 2.2 Patients
```
GET    /api/v1/patients
GET    /api/v1/patients/{id}
POST   /api/v1/patients
PUT    /api/v1/patients/{id}
DELETE /api/v1/patients/{id}
```

### 2.3 Appointments
```
GET    /api/v1/appointments?date=YYYY-MM-DD
POST   /api/v1/appointments
POST   /api/v1/appointments/{id}/complete
```

### 2.4 Prescriptions
```
GET    /api/v1/patients/{id}/prescriptions
POST   /api/v1/checkups
```

### 2.5 Medicines (Stock)
```
GET    /api/v1/medicines/stocks
POST   /api/v1/medicines/{id}/issue
```

### 2.6 Invoices
```
GET    /api/v1/invoices
GET    /api/v1/invoices/{id}
POST   /api/v1/invoices
POST   /api/v1/invoices/{id}/payment
GET    /api/v1/invoices/{id}/pdf
```

### 2.7 Reports
```
GET    /api/v1/reports/monthly?year=&month=
GET    /api/v1/reports/attendance?from=&to=
```

---

## 3. نمط الردود

### نجاح
```json
{
    "success": true,
    "data": {...},
    "message": "..."
}
```

### خطأ
```json
{
    "success": false,
    "error": {
        "code": "OUT_OF_STOCK",
        "message": "نفد المخزون"
    }
}
```

(رموز الأخطاء تطابق `reason` الحالية في JSONs الويب: `OUT_OF_STOCK`، `EXPIRED_ONLY`.)

---

## 4. المصادقة

- **الطريقة المقترحة:** Laravel Sanctum (بدل auth:api التقليدي).
- **الرأس:** `Authorization: Bearer {token}`

---

## 5. Rate Limiting

```php
Route::middleware('throttle:60,1')->group(...); // 60 طلب/دقيقة
```

---

## 6. API Resources

```php
class PatientResource extends JsonResource {
    public function toArray($request) {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'telephone' => $this->telephone,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
```

---

## 7. الاختبارات المقترحة

```php
public function test_api_returns_patient_list() {
    $user = User::factory()->create(['user_type' => 'admin']);
    $this->actingAs($user, 'sanctum')
         ->getJson('/api/v1/patients')
         ->assertStatus(200)
         ->assertJsonStructure(['data' => [['id', 'name']]]);
}
```

---

## 8. الأدوات الموصى بها

- **Postman Collection** — لاختبار API.
- **OpenAPI/Swagger** — للتوثيق التفاعلي.
- **Swagger UI** — عرض مرئي.

---

## 9. الفرق Web vs API

| الميزة | Web | API |
|---|---|---|
| المصادقة | Session | Token |
| الرد | HTML | JSON |
| CSRF | نعم | لا (Token) |
| Rate limit | لا | نعم |
| Caching | عرض | HTTP Cache |

---

## 10. الخلاصة

- **الويب:** ~90 route (يعمل بالكامل).
- **API:** مقترح (Sanctum + Resources + Tests).
- **الوقت المقدر:** 3-5 أيام لتطوير API كامل.
