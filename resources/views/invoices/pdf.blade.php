<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{$invoice->invoice_number}}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; direction: rtl; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 20px; }
        .header h2 { margin: 5px 0; font-size: 16px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #333; color: white; padding: 8px; text-align: right; }
        td { border: 1px solid #ddd; padding: 6px; text-align: right; }
        .summary { margin-top: 20px; text-align: left; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>مستشفى الشفاء</h1>
        <h2>فاتورة رقم: {{$invoice->invoice_number}}</h2>
        <div>التاريخ: {{$invoice->created_at->format('Y-m-d H:i')}}</div>
    </div>
    <p><strong>المريض:</strong> {{$invoice->patient->name ?? '-'}} ({{$invoice->patient->id ?? '-'}})</p>
    <p><strong>الهاتف:</strong> {{$invoice->patient->telephone ?? '-'}}</p>
    @if($invoice->appointment)<p><strong>رقم الموعد:</strong> {{$invoice->appointment->number}}</p>@endif

    <table>
        <thead>
            <tr>
                <th>البند</th>
                <th>الكمية</th>
                <th>السعر</th>
                <th>الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
            <tr>
                <td>{{$item->description}}</td>
                <td>{{$item->quantity}}</td>
                <td>{{number_format($item->unit_price, 2)}} YER</td>
                <td>{{number_format($item->total, 2)}} YER</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <h3>الإجمالي: {{number_format($invoice->total_amount, 2)}} YER</h3>
        <h3>المدفوع: {{number_format($invoice->paid_amount, 2)}} YER</h3>
        <h3>المتبقي: {{number_format($invoice->total_amount - $invoice->paid_amount, 2)}} YER</h3>
    </div>

    <div class="footer">
        تم التوليد بواسطة: {{Auth::user()->name ?? 'النظام'}} | {{now()->format('Y-m-d H:i:s')}}
    </div>
</body>
</html>
