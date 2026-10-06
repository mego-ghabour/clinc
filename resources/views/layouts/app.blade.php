<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $appName = $global_settings['app_name'] ?? 'فكرتك الرقمية';
        $appFont = $global_settings['app_font'] ?? 'Tajawal';
    @endphp
    <title>@yield('title', 'لوحة التحكم - ' . $appName)</title>
    @if(isset($global_settings['app_favicon']))
    <link rel="icon" href="{{ asset('storage/' . $global_settings['app_favicon']) }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.4/dist/turbo.es2017-esm.js"></script>
    <link href="https://fonts.googleapis.com/css2?family={{ $appFont }}:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: '{{ $appFont }}', sans-serif;
            background-color: #f8fafc;
        }
        .sidebar-bg { background-color: #0b4a60; }
        .text-teal-custom { color: #0b4a60; }
        .bg-teal-custom { background-color: #0b4a60; }
        
        /* Custom scrollbar for sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }
        @yield('styles')
    </style>
</head>
<body class="flex h-screen overflow-hidden bg-gray-50">

    <!-- Sidebar (Right) -->
    <aside class="w-[280px] sidebar-bg text-white flex flex-col h-full flex-shrink-0">
        <!-- Logo -->
        <div class="p-6 flex items-center gap-3 border-b border-white/10">
            @if(isset($global_settings['app_logo']))
                <img src="{{ asset('storage/' . $global_settings['app_logo']) }}" class="max-h-12 w-full object-contain" alt="Logo">
            @else
                <div class="w-10 h-10 bg-white/20 rounded-md flex items-center justify-center font-bold text-xl">{{ mb_substr($appName, 0, 1) }}</div>
                <div>
                    <h2 class="font-bold text-lg leading-tight">{{ $appName }}</h2>
                    <p class="text-xs text-gray-300">منصة إدارة العيادات</p>
                </div>
            @endif
        </div>

        <!-- Menu items -->
        <div class="flex-1 overflow-y-auto sidebar-scroll py-4 px-3 space-y-1">
            <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('dashboard') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                <i class="fa-solid fa-house w-5 text-center"></i>
                <span class="font-medium text-sm">نظرة عامة</span>
            </a>
            <a href="/clinics" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('clinics*') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                <i class="fa-solid fa-hospital w-5 text-center"></i>
                <span class="text-sm">العيادات والأقسام</span>
            </a>
            <a href="/patients" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('patients*') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                <i class="fa-solid fa-users w-5 text-center"></i>
                <span class="text-sm">المرضى</span>
            </a>
            <a href="/appointments" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('appointments*') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                <i class="fa-regular fa-calendar-check w-5 text-center"></i>
                <span class="text-sm">المواعيد</span>
            </a>
            <a href="/doctor-schedules" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('doctor-schedules*') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                <i class="fa-solid fa-clock-rotate-left w-5 text-center"></i>
                <span class="text-sm">مواعيد الأطباء</span>
            </a>
            <a href="#" class="flex items-center gap-3 text-gray-300 hover:bg-white/5 hover:text-white px-4 py-3 rounded-lg border-r-4 border-transparent transition">
                <i class="fa-solid fa-list-ol w-5 text-center"></i>
                <span class="text-sm">الطوابير والاستقبال</span>
            </a>
            <a href="#" class="flex items-center gap-3 text-gray-300 hover:bg-white/5 hover:text-white px-4 py-3 rounded-lg border-r-4 border-transparent transition">
                <i class="fa-solid fa-file-medical w-5 text-center"></i>
                <span class="text-sm">الزيارات والملف الطبي</span>
            </a>
            <a href="/doctors" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('doctors*') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                <i class="fa-solid fa-user-doctor w-5 text-center"></i>
                <span class="text-sm">الأطباء والفريق</span>
            </a>
            <a href="#" class="flex items-center gap-3 text-gray-300 hover:bg-white/5 hover:text-white px-4 py-3 rounded-lg border-r-4 border-transparent transition">
                <i class="fa-solid fa-flask w-5 text-center"></i>
                <span class="text-sm">المختبر</span>
            </a>
            <a href="#" class="flex items-center gap-3 text-gray-300 hover:bg-white/5 hover:text-white px-4 py-3 rounded-lg border-r-4 border-transparent transition">
                <i class="fa-solid fa-x-ray w-5 text-center"></i>
                <span class="text-sm">الأشعة</span>
            </a>
            <a href="#" class="flex items-center gap-3 text-gray-300 hover:bg-white/5 hover:text-white px-4 py-3 rounded-lg border-r-4 border-transparent transition">
                <i class="fa-solid fa-pills w-5 text-center"></i>
                <span class="text-sm">الصيدلية</span>
            </a>
            <a href="#" class="flex items-center gap-3 text-gray-300 hover:bg-white/5 hover:text-white px-4 py-3 rounded-lg border-r-4 border-transparent transition">
                <i class="fa-solid fa-file-invoice-dollar w-5 text-center"></i>
                <span class="text-sm">المالية والفواتير</span>
            </a>
            <a href="#" class="flex items-center gap-3 text-gray-300 hover:bg-white/5 hover:text-white px-4 py-3 rounded-lg border-r-4 border-transparent transition">
                <i class="fa-solid fa-cash-register w-5 text-center"></i>
                <span class="text-sm">الصندوق</span>
            </a>
            <a href="#" class="flex items-center gap-3 text-gray-300 hover:bg-white/5 hover:text-white px-4 py-3 rounded-lg border-r-4 border-transparent transition">
                <i class="fa-solid fa-money-bill-transfer w-5 text-center"></i>
                <span class="text-sm">الاستردادات</span>
            </a>
            <a href="#" class="flex items-center gap-3 text-gray-300 hover:bg-white/5 hover:text-white px-4 py-3 rounded-lg border-r-4 border-transparent transition">
                <i class="fa-solid fa-shield-halved w-5 text-center"></i>
                <span class="text-sm">التأمين</span>
            </a>
            <a href="#" class="flex items-center gap-3 text-gray-300 hover:bg-white/5 hover:text-white px-4 py-3 rounded-lg border-r-4 border-transparent transition">
                <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                <span class="text-sm">التقارير</span>
            </a>
            <a href="/users" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('users*') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                <i class="fa-solid fa-users-gear w-5 text-center"></i>
                <span class="text-sm">المستخدمون</span>
            </a>
            <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('settings.*') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                <i class="fa-solid fa-gear w-5 text-center"></i>
                <span class="text-sm">الإعدادات الأساسية</span>
            </a>
        </div>

        <!-- User Profile -->
        <div class="p-4 border-t border-white/10 flex items-center gap-3 hover:bg-white/5 cursor-pointer transition">
            <div class="w-10 h-10 bg-teal-600 rounded-full flex items-center justify-center font-bold text-sm">ع.س</div>
            <div>
                <h3 class="font-bold text-sm">عبدالله السالم</h3>
                <p class="text-xs text-gray-300">SUPER ADMIN</p>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full overflow-hidden">
        
        <!-- Topbar -->
        <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 flex-shrink-0">
            <div class="text-gray-500 text-sm">
                فكرتك الرقمية - توقيت الرياض
            </div>
            
            <div class="flex items-center gap-4">
                <button class="relative text-gray-600 hover:bg-gray-100 p-2 rounded-full transition border border-gray-200 flex items-center gap-2 px-4 text-sm font-medium">
                    <i class="fa-regular fa-bell"></i>
                    <span>الإشعارات</span>
                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[10px] rounded-full flex items-center justify-center font-bold">2</span>
                </button>
                <a href="/" class="bg-teal-custom hover:bg-teal-700 text-white px-5 py-2 rounded-full text-sm font-medium transition">
                    خروج
                </a>
            </div>
        </header>

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-6 relative">
            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative" role="alert" x-data="{ show: true }" x-show="show">
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <span class="absolute top-0 bottom-0 left-0 px-4 py-3" @click="show = false">
                        <i class="fa-solid fa-xmark cursor-pointer"></i>
                    </span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative" role="alert" x-data="{ show: true }" x-show="show">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <span class="absolute top-0 bottom-0 left-0 px-4 py-3" @click="show = false">
                        <i class="fa-solid fa-xmark cursor-pointer"></i>
                    </span>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    @yield('scripts')
</body>
</html>
