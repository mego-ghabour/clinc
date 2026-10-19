<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'نظام العيادات الذكي')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'teal-sidebar': '#0a3a4c',
                        'teal-custom': '#0b4a60',
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Dynamic Font from Settings -->
    @php
        $appFont = \App\Models\Setting::where('key', 'app_font')->value('value') ?? 'Tajawal';
        $fontFamily = in_array($appFont, ['Cairo', 'Tajawal', 'Alexandria', 'Almarai']) ? $appFont : 'Tajawal';
    @endphp
    <link href="https://fonts.googleapis.com/css2?family={{ $fontFamily }}:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: '{{ $fontFamily }}', sans-serif; background-color: #f8fafc; }
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(156, 163, 175, 0.5); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(156, 163, 175, 0.8); }
    </style>
    @stack('styles')
</head>
<body class="text-gray-800 antialiased overflow-hidden" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden relative">

        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'" class="fixed lg:static inset-y-0 right-0 w-64 bg-teal-sidebar text-white flex flex-col transition-transform duration-300 shadow-2xl lg:shadow-xl z-50 flex-shrink-0 print:hidden h-full">
            <!-- Logo -->
            <div class="h-16 flex items-center justify-center border-b border-white/10 bg-black/10">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    @php
                        $appLogo = \App\Models\Setting::where('key', 'app_logo')->value('value');
                        $appName = \App\Models\Setting::where('key', 'app_name')->value('value') ?? 'نظام عياداتي';
                    @endphp
                    @if($appLogo)
                        <img src="{{ asset('storage/' . $appLogo) }}" class="h-10 w-auto rounded object-contain bg-white/10 p-1" alt="Logo">
                    @else
                        <div class="w-8 h-8 rounded-lg bg-white text-teal-sidebar flex items-center justify-center text-xl shadow-md">
                            <i class="fa-solid fa-heart-pulse"></i>
                        </div>
                    @endif
                    <span class="text-xl font-black tracking-tight truncate max-w-[120px]">{{ $appName }}</span>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto custom-scrollbar py-6 px-3 space-y-1.5">
                <p class="text-[10px] uppercase font-bold text-teal-200/50 px-4 mb-2 tracking-wider">الرئيسية</p>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('dashboard') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                    <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                    <span class="text-sm">لوحة التحكم</span>
                </a>

                <a href="{{ route('guide.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('guide*') ? 'bg-amber-500/20 text-amber-300 border-r-4 border-amber-400 font-bold' : 'text-amber-200/90 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                    <i class="fa-solid fa-book-open-reader w-5 text-center text-amber-400"></i>
                    <span class="text-sm font-semibold">دليل وشرح النظام</span>
                    <span class="mr-auto text-[9px] bg-amber-500/30 text-amber-200 px-2 py-0.5 rounded-full font-bold">شامل</span>
                </a>
                
                <p class="text-[10px] uppercase font-bold text-teal-200/50 px-4 mt-6 mb-2 tracking-wider">الاستقبال والعيادات</p>
                
                <a href="{{ route('patients.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('patients*') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                    <i class="fa-solid fa-users w-5 text-center"></i>
                    <span class="text-sm">المرضى</span>
                </a>

                <a href="{{ route('visits.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('visits*') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                    <i class="fa-solid fa-notes-medical w-5 text-center"></i>
                    <span class="text-sm">الزيارات والملفات الطبية</span>
                </a>

                <a href="{{ route('doctors.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('doctors*') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                    <i class="fa-solid fa-user-doctor w-5 text-center"></i>
                    <span class="text-sm">الأطباء والفريق الطبي</span>
                </a>

                <a href="{{ route('lab-requests.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('lab-requests*') || request()->is('lab-tests*') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                    <i class="fa-solid fa-flask-vial w-5 text-center"></i>
                    <span class="text-sm">المختبر والتحاليل الطبية</span>
                </a>

                <a href="{{ route('radiology.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('radiology*') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                    <i class="fa-solid fa-x-ray w-5 text-center"></i>
                    <span class="text-sm">قسم الأشعة والتصوير</span>
                </a>

                <a href="{{ route('prescriptions.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('prescriptions*') || request()->is('medicines*') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                    <i class="fa-solid fa-pills w-5 text-center"></i>
                    <span class="text-sm">الصيدلية والمخزون الدوائي</span>
                </a>

                <a href="{{ route('appointments.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('appointments*') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                    <i class="fa-regular fa-calendar-check w-5 text-center"></i>
                    <span class="text-sm">المواعيد</span>
                </a>

                <a href="{{ route('queues.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('queues*') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                    <i class="fa-solid fa-users-rays w-5 text-center"></i>
                    <span class="text-sm">الطابور والاستقبال (LIVE)</span>
                </a>
                
                <p class="text-[10px] uppercase font-bold text-teal-200/50 px-4 mt-6 mb-2 tracking-wider">المالية والمحاسبة</p>

                <a href="{{ route('invoices.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('invoices*') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                    <i class="fa-solid fa-file-invoice-dollar w-5 text-center"></i>
                    <span class="text-sm">الفواتير والمدفوعات</span>
                </a>

                <a href="{{ route('insurance-claims.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('insurance*') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                    <i class="fa-solid fa-building-shield w-5 text-center"></i>
                    <span class="text-sm">التأمين الطبي والمطالبات</span>
                </a>

                <a href="{{ route('services.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('services*') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                    <i class="fa-solid fa-tags w-5 text-center"></i>
                    <span class="text-sm">الخدمات الطبية والأسعار</span>
                </a>
                
                <p class="text-[10px] uppercase font-bold text-teal-200/50 px-4 mt-6 mb-2 tracking-wider">إدارة المستشفى</p>
                
                <a href="{{ route('system-users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('system-users*') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                    <i class="fa-solid fa-user-tie w-5 text-center"></i>
                    <span class="text-sm">المستخدمين والموظفين</span>
                </a>

                <a href="{{ route('roles.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('roles*') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                    <i class="fa-solid fa-user-shield w-5 text-center"></i>
                    <span class="text-sm">الأدوار والصلاحيات</span>
                </a>

                <a href="{{ route('doctor-schedules.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('doctor-schedules*') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                    <i class="fa-solid fa-calendar-week w-5 text-center"></i>
                    <span class="text-sm">جداول عمل الأطباء</span>
                </a>

                <a href="{{ route('clinics.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('clinics*') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                    <i class="fa-solid fa-door-open w-5 text-center"></i>
                    <span class="text-sm">الأقسام والعيادات</span>
                </a>

                <a href="{{ route('audit_logs.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('audit-logs*') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                    <i class="fa-solid fa-shield-halved w-5 text-center"></i>
                    <span class="text-sm">سجل التدقيق والرقابة</span>
                </a>

                <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->is('settings*') ? 'bg-white/10 text-white border-r-4 border-white' : 'text-gray-300 hover:bg-white/5 hover:text-white border-r-4 border-transparent' }}">
                    <i class="fa-solid fa-gear w-5 text-center"></i>
                    <span class="text-sm">إعدادات النظام</span>
                </a>
            </div>

            <!-- User Profile (Bottom) -->
            <div class="p-4 border-t border-white/10 bg-black/10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-white/20 border-2 border-white/30 flex items-center justify-center font-bold text-white overflow-hidden shrink-0">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="w-full h-full object-cover">
                        @else
                            {{ mb_substr(auth()->user()->name, 0, 1) }}
                        @endif
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-bold truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-teal-200 truncate">{{ auth()->user()->roles->first()->name ?? 'مستخدم' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full bg-red-500/20 hover:bg-red-500/40 text-red-200 py-2 rounded-lg text-xs font-bold transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-power-off"></i> تسجيل الخروج
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50/50">
            
            <!-- Top Navbar -->
            <header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-4 lg:px-8 shadow-sm shrink-0 print:hidden z-30">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500 hover:text-gray-800 focus:outline-none">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    <div>
                        <h2 class="text-lg font-black text-gray-800 hidden sm:block">@yield('header', 'نظام إدارة العيادات')</h2>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('guide.index') }}" class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-800 text-xs font-bold transition border border-teal-200/80 shadow-sm" title="دليل الاستخدام والتشغيل الشامل">
                        <i class="fa-solid fa-circle-question text-teal-600"></i>
                        <span>دليل النظام</span>
                    </a>

                    <div class="relative" x-data="{ notificationsOpen: false }">
                        <button @click="notificationsOpen = !notificationsOpen" class="w-10 h-10 rounded-full bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-600 transition flex items-center justify-center relative">
                            <i class="fa-regular fa-bell"></i>
                            <span class="absolute top-0 right-0 w-3 h-3 bg-red-500 border-2 border-white rounded-full"></span>
                        </button>
                    </div>
                    
                    @impersonating
                        <a href="{{ route('impersonate.leave') }}" class="bg-red-100 text-red-600 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-2 border border-red-200">
                            <i class="fa-solid fa-user-secret"></i> إنهاء التقمص
                        </a>
                    @endImpersonating
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 lg:p-8 custom-scrollbar relative">
                @yield('content')
            </main>

        </div>

    </div>

    <!-- Flash Messages (Toast) -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="fixed bottom-5 left-5 bg-gray-900 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 z-50 print:hidden" x-transition>
            <i class="fa-solid fa-circle-check text-green-400 text-xl"></i>
            <p class="font-bold text-sm">{{ session('success') }}</p>
        </div>
    @endif
    @if($errors->any())
        <div x-data="{ show: true }" x-show="show" class="fixed bottom-5 left-5 bg-red-50 text-red-700 px-6 py-4 rounded-xl shadow-2xl border border-red-200 flex items-start gap-3 z-50 max-w-sm print:hidden" x-transition>
            <i class="fa-solid fa-circle-exclamation text-xl mt-0.5"></i>
            <div>
                <p class="font-black text-sm mb-1">تنبيه!</p>
                <ul class="text-xs font-bold space-y-1">
                    @foreach($errors->all() as $error)
                        <li>- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button @click="show = false" class="text-red-400 hover:text-red-800 mr-auto"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    @stack('scripts')
    @yield('scripts')
</body>
</html>
