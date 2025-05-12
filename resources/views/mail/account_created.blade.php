<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تفاصيل حسابك الجديد</title>
    <style>
        body {
            direction: rtl;
            text-align: right;
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }

        .email-container {
            background-color: #ffffff;
            border: 1px solid #ddd;
            padding: 30px;
            border-radius: 10px;
            max-width: 600px;
            margin: auto;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            width: 120px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="email-container" dir="rtl">

    <p>مرحباً {{ $user->name }}،</p>

    <p>تم إنشاء حسابك بنجاح على منصتنا. إليك معلومات الدخول الخاصة بك:</p>

    <p><strong>البريد الإلكتروني:</strong> {{ $user->email }}</p>
    <p><strong>كلمة المرور:</strong> {{ $plainPassword }}</p>

    <p>ننصحك بتغيير كلمة المرور بعد تسجيل الدخول لأول مرة حفاظاً على أمان حسابك.</p>

    <p>
        <a href="{{ env('FRONTEND_URL') . '/login' }}">...</a>  {{-- ❌ This won't work reliably --}}
    </p>

    <div class="footer">
        © {{ date('Y') }} جميع الحقوق محفوظة
    </div>
</div>
</body>
</html>
