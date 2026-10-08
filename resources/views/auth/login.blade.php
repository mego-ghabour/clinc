<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام العيادة الذكي</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'teal-custom': '#0b4a60',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f8fafc; }
        .bg-teal-custom { background-color: #0b4a60; }
        .text-teal-custom { color: #0b4a60; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50 p-4">

    <div class="max-w-4xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row">
        
        <!-- Left Side (Branding/Image) -->
        <div class="w-full md:w-1/2 bg-teal-custom text-white p-12 flex flex-col justify-between relative overflow-hidden hidden md:flex">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
            <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-teal-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
            <div class="absolute -top-32 -right-32 w-96 h-96 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
            
            <div class="relative z-10">
                <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center text-3xl font-bold mb-6 backdrop-blur-sm border border-white/20">
                    <i class="fa-solid fa-hospital"></i>
                </div>
                <h1 class="text-4xl font-black mb-4 leading-tight">نظام إدارة<br>العيادات الذكي</h1>
                <p class="text-teal-100 text-lg leading-relaxed max-w-sm">
                    النظام الأفضل والأكثر أماناً لإدارة مرضاك، مواعيدك، وطاقمك الطبي بكل سهولة واحترافية.
                </p>
            </div>
            
            <div class="relative z-10 mt-12">
                <div class="flex -space-x-4 space-x-reverse mb-4">
                    <img class="w-10 h-10 rounded-full border-2 border-teal-custom object-cover" src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=100&q=80" alt="">
                    <img class="w-10 h-10 rounded-full border-2 border-teal-custom object-cover" src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&w=100&q=80" alt="">
                    <img class="w-10 h-10 rounded-full border-2 border-teal-custom object-cover" src="https://images.unsplash.com/photo-1594824432258-000c0f8f3073?auto=format&fit=crop&w=100&q=80" alt="">
                </div>
                <p class="text-sm text-teal-100 font-medium">موثوق من آلاف الأطباء</p>
            </div>
        </div>

        <!-- Right Side (Login Form) -->
        <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center bg-white relative">
            
            <div class="text-center md:text-right mb-10">
                <h2 class="text-3xl font-black text-gray-800 mb-2">مرحباً بعودتك! 👋</h2>
                <p class="text-gray-500 font-medium">يرجى إدخال بيانات حسابك للدخول إلى النظام</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm font-bold border border-red-100 flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation mt-1"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
                @csrf
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">معرف الدخول (Username)</label>
                    <div class="relative">
                        <input type="text" name="username" value="{{ old('username') }}" required autofocus dir="ltr" class="w-full pl-4 pr-12 py-3 bg-gray-50 border border-gray-200 focus:border-teal-custom focus:bg-white rounded-xl text-left focus:outline-none focus:ring-4 focus:ring-teal-custom/10 transition font-mono font-bold text-gray-800">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400">
                            <i class="fa-solid fa-user"></i>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-bold text-gray-700">كلمة المرور</label>
                    </div>
                    <div class="relative">
                        <input type="password" name="password" required dir="ltr" class="w-full pl-4 pr-12 py-3 bg-gray-50 border border-gray-200 focus:border-teal-custom focus:bg-white rounded-xl text-left focus:outline-none focus:ring-4 focus:ring-teal-custom/10 transition font-mono">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-teal-600 rounded border-gray-300 focus:ring-teal-500">
                        <span class="text-sm font-bold text-gray-600 group-hover:text-gray-800 transition">تذكرني على هذا الجهاز</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-teal-custom hover:bg-teal-700 text-white py-3.5 rounded-xl text-base font-bold shadow-lg shadow-teal-custom/30 hover:shadow-xl hover:shadow-teal-custom/40 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    تسجيل الدخول <i class="fa-solid fa-arrow-left-long"></i>
                </button>
            </form>
            
            <!-- Patient Booking Button -->
            <div class="mt-6 pt-6 border-t border-gray-100">
                <a href="{{ route('booking.index') }}" class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 py-3.5 rounded-xl text-base font-bold transition flex items-center justify-center gap-2">
                    <i class="fa-regular fa-calendar-check text-lg"></i> حجز موعد أونلاين للمرضى
                </a>
            </div>

            <div class="mt-8 text-center">
                <p class="text-xs font-bold text-gray-400">النظام محمي ومراقب <i class="fa-solid fa-shield-check text-green-500 ml-1"></i></p>
            </div>
        </div>
    </div>

</body>
</html>
