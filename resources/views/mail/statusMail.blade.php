<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تحديث حالة الطلب</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body style="background-color: #f8fafc; font-family: 'Tajawal', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1e293b; line-height: 1.6; direction: rtl; margin: 0; padding: 0;">
<table width="100%" cellspacing="0" cellpadding="0" style="max-width: 600px; margin: 0 auto; padding: 20px 10px;">
    <tr>
        <td>
            <div style="background-color: #ffffff; border-radius: 16px; padding: 32px; box-shadow: 0 4px 24px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;">
                <h1 style="font-size: 24px; font-weight: 700; color: #047857; margin-top: 0; margin-bottom: 24px; text-align: right;">
                    مرحباً بك {{ $name }}
                    <span style="color:
                    @if($status == 'مقبول') #10b981
                    @elseif($status == 'مرفوض') #ef4444
                    @elseif($status == 'غير_مكتمل') #f59e0b
                    @elseif($status == 'قيد_مراجعة_المسؤول') #3b82f6
                    @else #64748b
                    @endif">
                    @if($status == 'مقبول') 😊
                        @elseif($status == 'مرفوض') 😔
                        @elseif($status == 'غير_مكتمل') 🤔
                        @elseif($status == 'قيد_مراجعة_المسؤول') ⌛
                        @else ⏳
                        @endif
                    </span>
                </h1>

                <div style="background-color:
                    @if($status == 'مقبول') #f0fdf4
                    @elseif($status == 'مرفوض') #fef2f2
                    @elseif($status == 'غير_مكتمل') #fffbeb
                    @elseif($status == 'قيد_مراجعة_المسؤول') #eff6ff
                    @else #f8fafc
                    @endif;
                    border-right: 4px solid
                    @if($status == 'مقبول') #10b981
                    @elseif($status == 'مرفوض') #ef4444
                    @elseif($status == 'غير_مكتمل') #f59e0b
                    @elseif($status == 'قيد_مراجعة_المسؤول') #3b82f6
                    @else #64748b
                    @endif;
                    padding: 16px; border-radius: 8px; margin-bottom: 24px;">
                    <p style="font-size: 16px; color:
                        @if($status == 'مقبول') #065f46
                        @elseif($status == 'مرفوض') #991b1b
                        @elseif($status == 'غير_مكتمل') #92400e
                        @elseif($status == 'قيد_مراجعة_المسؤول') #1e40af
                        @else #334155
                        @endif;
                        margin: 0; text-align: right; font-weight: 500;">
                        <i class="fas
                            @if($status == 'مقبول') fa-check-circle
                            @elseif($status == 'مرفوض') fa-times-circle
                            @elseif($status == 'غير_مكتمل') fa-exclamation-circle
                            @elseif($status == 'قيد_مراجعة_المسؤول') fa-user-tie
                            @else fa-clock
                            @endif"
                           style="margin-left: 8px;"></i>
                        {{ $customMessage[$status] }}
                    </p>
                </div>

                <!-- Additional details section -->
                <div style="margin-top: 24px; text-align: right;">
                    <p style="font-size: 15px; color: #334155; margin-bottom: 12px;">
                        <strong style="color: #047857;">رقم الطلب:</strong> {{ $orderNumber ?? 'N/A' }}
                    </p>
                    <p style="font-size: 15px; color: #334155; margin-bottom: 12px;">
                        <strong style="color: #047857;">حالة الطلب:</strong>
                        <span style="color:
                            @if($status == 'مقبول') #10b981
                            @elseif($status == 'مرفوض') #ef4444
                            @elseif($status == 'غير_مكتمل') #f59e0b
                            @elseif($status == 'قيد_مراجعة_المسؤول') #3b82f6
                            @else #64748b
                            @endif;
                            font-weight: 500;">
                            {{ str_replace('_', ' ', $status) }}
                        </span>
                    </p>
                    <p style="font-size: 15px; color: #334155; margin-bottom: 0;">
                        <strong style="color: #047857;">التاريخ:</strong> {{ $orderDate ?? now()->format('Y-m-d') }}
                    </p>
                </div>

                <!-- Status-specific instructions -->
                @if($status == 'غير_مكتمل')
                    <div style="background-color: #fff7ed; border: 1px dashed #f59e0b; border-radius: 8px; padding: 12px; margin-top: 20px; text-align: right;">
                        <p style="font-size: 14px; color: #92400e; margin: 0;">
                            <i class="fas fa-info-circle" style="margin-left: 8px;"></i>
                            الرجاء إكمال الطلب من خلال <a href="{{ $completionLink ?? '#' }}" style="color: #d97706; font-weight: 500;">هذا الرابط</a> أو التواصل مع الدعم.
                        </p>
                    </div>
                @elseif($status == 'مرفوض')
                    <div style="background-color: #fef2f2; border: 1px dashed #ef4444; border-radius: 8px; padding: 12px; margin-top: 20px; text-align: right;">
                        <p style="font-size: 14px; color: #991b1b; margin: 0;">
                            <i class="fas fa-question-circle" style="margin-left: 8px;"></i>
                            لمعرفة أسباب الرفض أو تقديم استئناف، يرجى <a href="{{ $contactLink ?? '#' }}" style="color: #dc2626; font-weight: 500;">الاتصال بنا</a>.
                        </p>
                    </div>
                @endif

                <div style="border-top: 1px solid #e2e8f0; margin-top: 32px; padding-top: 24px; text-align: right;">
                    <p style="font-size: 14px; color: #64748b; margin-bottom: 16px;">
                        لمزيد من المعلومات أو الاستفسارات، يمكنك الرد على هذا البريد أو التواصل معنا مباشرة.
                    </p>

                    <p style="font-size: 14px; color: #64748b; margin: 0;">
                        مع أطيب التحيات،<br>
                        <strong style="color: #047857;">فريق خدمة العملاء</strong>
                    </p>
                </div>
            </div>

            <div style="text-align: center; margin-top: 24px;">
                <p style="font-size: 12px; color: #94a3b8;">
                    &copy; {{ date('Y') }} جميع الحقوق محفوظة لشركة [اسم الشركة]
                </p>
                <p style="font-size: 12px; color: #94a3b8; margin-top: 8px;">
                    العنوان: [عنوان الشركة] | الهاتف: [رقم الهاتف] | البريد: [البريد الإلكتروني]
                </p>
            </div>
        </td>
    </tr>
</table>
</body>
</html>
