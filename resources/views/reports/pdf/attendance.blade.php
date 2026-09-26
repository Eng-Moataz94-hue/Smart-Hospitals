<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'تقرير' }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; direction: rtl; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 20px; }
        .header h2 { margin: 5px 0; font-size: 16px; color: #555; }
        .header .meta { font-size: 11px; color: #777; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #333; color: white; padding: 8px; text-align: right; }
        td { border: 1px solid #ddd; padding: 6px; text-align: right; }
        tr:nth-child(even) { background: #f9f9f9; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>مستشفى الشفاء</h1>
        <h2>{{ $title ?? 'تقرير' }}</h2>
        <div class="meta">
            {{ now()->format('Y-m-d H:i') }}
            @if(isset($start_date)) | من: {{ $start_date }} @endif
            @if(isset($end_date)) | إلى: {{ $end_date }} @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>اسم الموظف</th>
                <th>النوع</th>
                <th>الأيام الكاملة</th>
                <th>الأيام الناقصة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data ?? [] as $row)
            <tr>
                <td>{{ $row->name ?? '-' }}</td>
                <td>{{ $row->type ?? '-' }}</td>
                <td>{{ $row->attended ?? '-' }}</td>
                <td>{{ $row->shortleave ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align:center;">لا توجد بيانات</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        تم التوليد بواسطة: {{ Auth::user()->name ?? 'النظام' }} | {{ now()->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
