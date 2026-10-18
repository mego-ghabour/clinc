<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة الحجز مغلقة - {{ $global_settings['app_name'] ?? 'العيادات' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-sm border border-gray-100 p-8 text-center">
        <div class="w-20 h-20 bg-amber-50 text-amber-500 rounded-3xl flex items-center justify-center text-3xl mx-auto mb-6 shadow-inner">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
        <h1 class="text-2xl font-black text-gray-800 mb-2">بوابة الحجز مغلقة مؤقتاً</h1>
        <p class="text-gray-500 text-sm font-bold mb-6">نعتذر، تم إيقاف استقبال طلبات الحجز عبر الموقع الإلكتروني في الوقت الحالي بناءً على تعليمات إدارة المركز.</p>

        @if(!empty($global_settings['clinic_phone']))
            <div class="bg-teal-50 border border-teal-100 rounded-2xl p-4 mb-6">
                <p class="text-xs font-bold text-teal-700 mb-1">لحجز موعد أو للاستفسارات، يرجى الاتصال بنا مباشرة:</p>
                <a href="tel:{{ $global_settings['clinic_phone'] }}" class="text-xl font-black text-teal-800 font-mono tracking-wider hover:underline" dir="ltr">
                    <i class="fa-solid fa-phone ml-2"></i> {{ $global_settings['clinic_phone'] }}
                </a>
            </div>
        @endif

        <a href="{{ route('login') }}" class="text-xs font-bold text-gray-400 hover:text-gray-600 transition">
            دخول الكادر الطبي والإداري &larr;
        </a>
    </div>
</body>
</html>
