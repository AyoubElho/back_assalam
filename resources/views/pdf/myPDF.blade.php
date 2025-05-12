@php use SimpleSoftwareIO\QrCode\Facades\QrCode; @endphp
    <!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>

        body {
            font-family: 'Tajawal', sans-serif;
            font-size: 13px;
            direction: rtl;
            margin: 0;
            padding: 5px;
            color: #333;
            background-color: #fff;
            line-height: 1.6;
            zoom: 0.95;
        }

        .document {
            max-width: 800px;
            margin: 0 auto;
            padding: 15px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #4a89dc;
        }

        .logo {
            height: 90px;
            width: auto;
        }

        .document-title {
            text-align: center;
            color: #4a89dc;
            font-size: 20px;
            font-weight: 700;
            margin: 20px 0;
            padding-bottom: 8px;
            border-bottom: 1px dashed #e0e0e0;
        }

        .notification-box {
            background-color: #f8f9fa;
            border-right: 4px solid #4a89dc;
            padding: 12px;
            margin: 10px 0;
            border-radius: 0 4px 4px 0;
        }

        .section-title {
            color: #4a89dc;
            font-size: 15px;
            font-weight: 700;
            margin: 10px 0 8px 0;
            padding-bottom: 4px;
            border-bottom: 1px solid #eee;
        }

        .instructions {
            background-color: #f5f7fa;
            padding: 12px;
            border-radius: 4px;
            margin: 10px 0;
        }

        .instructions ol {
            padding-right: 18px;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 13px;
        }

        th {
            background-color: #4a89dc;
            color: white;
            padding: 7px;
            text-align: right;
            font-weight: 500;
        }

        td {
            padding: 7px;
            border: 1px solid #e0e0e0;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .qr-container {
            text-align: center;
            margin: 15px 0;
            padding: 5px;
        }

        .qr-code {
            display: inline-block;
            padding: 5px;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #e0e0e0;
            font-size: 11px;
            color: #666;
        }

        .date {
            background-color: #f5f7fa;
            padding: 6px 10px;
            margin-top: 20px;
            text-align: left;
            border-radius: 4px;
            font-size: 13px;
        }
    </style>
</head>

<body>
<div class="document">
    <div class="header">
        <div style="text-align: center">
            <img src="{{ public_path('images/download.png') }}" alt="شعار الجمعية" class="logo">
        </div>
        <div class="date">التاريخ:<span style="width: fit-content">
            {{ date('Y-m-d / H:i') }}
        </span></div>
    </div>

    <div class="document-title">إشعار قبول الطلب</div>

    <div class="notification-box">
        نود إبلاغكم بأن طلب الانضمام إلى برنامج رعاية الأرامل قد تم قبوله من قبل اللجنة المختصة، ونهنئكم بهذا القبول.
    </div>

    <div class="instructions">
        <div class="section-title">تعليمات للمستفيدة:</div>
        <ol>
            <li>يجب تقديم هذا المستند إلى مقر الجمعية خلال 15 يوم</li>
            <li>إحضار الوثائق المطلوبة والصور الشخصية</li>
        </ol>
    </div>

    <div class="section-title">معلومات الطلب</div>
    <table>
        <tr>
            <th width="30%">نوع الطلب</th>
            <td>{{ $request->application_type }}</td>
        </tr>
        <tr>
            <th>رقم الطلب</th>
            <td>{{ $request->id }}</td>
        </tr>
        <tr>
            <th>تاريخ القبول</th>
            <td>{{ date('Y-m-d / H:i') }}</td>
        </tr>
    </table>

    <div class="section-title">معلومات المستفيدة</div>
    <table>
        <tr>
            <th width="30%">الاسم الكامل</th>
            <td>
                @if($request->application_type == 'أسرة_معوزة')
                    {{ $request->destitute->name }}
                @else
                    {{ $request->widow->name }}
                @endif
            </td>
        </tr>
        <tr>
            <th>رقم البطاقة الوطنية</th>
            <td>
                @if($request->application_type == 'أسرة_معوزة')
                    {{ $request->destitute->cin }}
                @else
                    {{ $request->widow->cin }}
                @endif
            </td>
        </tr>
        <tr>
            <th>رقم الهاتف</th>
            <td>
                @if($request->application_type == 'أسرة_معوزة')
                    {{ $request->destitute->phone }}
                @else
                    {{ $request->widow->tel }}
                @endif
            </td>
        </tr>
        @if($request->application_type == 'أسرة_معوزة')
            <tr>
                <th>اسم الزوج</th>
                <td>{{ $request->destitute->husband->name }}</td>
            </tr>
            <tr>
                <th>رقم بطاقة الزوج الوطنية</th>
                <td>{{ $request->destitute->husband->cin }}</td>
            </tr>
        @else
            <tr>
                <th>عدد الأبناء</th>
                <td>{{ count($request->widow->orphans) }}</td>
            </tr>
        @endif
    </table>

    @php
        $qr = QrCode::size(90)->generate(env('FRONTEND_URL').'/verify/' . $request->id);
        $qr = preg_replace('/<\?xml.*?\?>/', '', $qr);
        $qr = preg_replace('/<!DOCTYPE.*?>/', '', $qr);
    @endphp

    <div class="qr-container">
        <p>مسح رمز الاستجابة السريعة للتحقق من المستند:</p>
        <div class="qr-code">
            {!! $qr !!}
        </div>
    </div>

    <div class="footer">
        <div>هاتف الجمعية: 0530000000 | البريد الإلكتروني: info@alihsan.org</div>
        <div>عنوان الجمعية: شارع الخير، المدينة، المملكة المغربية</div>
    </div>
</div>
</body>
</html>
