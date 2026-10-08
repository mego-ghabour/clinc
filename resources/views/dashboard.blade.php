@extends('layouts.app')

@section('title', 'لوحة التحكم - Clinc')
@section('header', 'لوحة التحكم والإحصائيات')

@section('content')
<div class="space-y-6">

    <!-- Welcome Banner -->
    <div class="bg-gradient-to-l from-teal-700 to-teal-500 rounded-3xl p-6 md:p-8 text-white flex flex-col md:flex-row justify-between items-center shadow-lg relative overflow-hidden gap-6">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-teal-400 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
        
        <div class="relative z-10 text-center md:text-right">
            <h1 class="text-2xl md:text-3xl font-black mb-2">مرحباً بك مجدداً، {{ auth()->user()->name }}! 👋</h1>
            <p class="text-teal-100 text-sm md:text-base font-bold">إليك نظرة سريعة على أداء العيادة لهذا اليوم.</p>
        </div>
        <div class="relative z-10 text-center md:text-left bg-white/10 px-6 py-4 rounded-2xl backdrop-blur-sm border border-white/20 w-full md:w-auto">
            <p class="text-xs text-teal-100 mb-1 font-bold">تاريخ اليوم</p>
            <p class="text-xl md:text-2xl font-black font-mono" dir="ltr">{{ now()->format('Y-m-d') }}</p>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <!-- KPI 1 -->
        <div class="bg-white p-5 md:p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 md:gap-5 group hover:border-blue-200 transition">
            <div class="w-12 h-12 md:w-14 md:h-14 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xl md:text-2xl group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <p class="text-[10px] md:text-xs font-bold text-gray-500 mb-1">إجمالي المرضى المسجلين</p>
                <h4 class="text-xl md:text-2xl font-black text-gray-800">{{ $patientsCount ?? 0 }}</h4>
            </div>
        </div>
        
        <!-- KPI 2 -->
        <div class="bg-white p-5 md:p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 md:gap-5 group hover:border-orange-200 transition">
            <div class="w-12 h-12 md:w-14 md:h-14 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center text-xl md:text-2xl group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div>
                <p class="text-[10px] md:text-xs font-bold text-gray-500 mb-1">مواعيد اليوم (قريباً)</p>
                <h4 class="text-xl md:text-2xl font-black text-gray-800">{{ $todayAppointments ?? 0 }}</h4>
            </div>
        </div>

        <!-- KPI 3 -->
        <div class="bg-white p-5 md:p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 md:gap-5 group hover:border-teal-200 transition">
            <div class="w-12 h-12 md:w-14 md:h-14 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-xl md:text-2xl group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-user-doctor"></i>
            </div>
            <div>
                <p class="text-[10px] md:text-xs font-bold text-gray-500 mb-1">الأطباء المتاحين (نشط)</p>
                <h4 class="text-xl md:text-2xl font-black text-gray-800">{{ $doctorsCount ?? 0 }}</h4>
            </div>
        </div>

        <!-- KPI 4 -->
        <div class="bg-white p-5 md:p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 md:gap-5 group hover:border-green-200 transition">
            <div class="w-12 h-12 md:w-14 md:h-14 rounded-full bg-green-50 text-green-500 flex items-center justify-center text-xl md:text-2xl group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <div>
                <p class="text-[10px] md:text-xs font-bold text-gray-500 mb-1">إيرادات اليوم (قريباً)</p>
                <h4 class="text-xl md:text-2xl font-black text-gray-800">{{ $todayRevenue ?? 0 }}</h4>
            </div>
        </div>
    </div>

    <!-- Charts & Tables Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Chart Placeholder -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
                <h3 class="font-black text-gray-800 text-lg">معدل إيرادات العيادة (قريباً)</h3>
                <select class="bg-gray-50 border-none text-xs font-bold rounded-lg text-gray-600 focus:ring-0 cursor-pointer w-full sm:w-auto">
                    <option>هذا الأسبوع</option>
                    <option>هذا الشهر</option>
                </select>
            </div>
            <div class="h-48 md:h-64 flex flex-col items-center justify-center bg-gray-50 rounded-xl border border-dashed border-gray-200 p-4 text-center">
                <i class="fa-solid fa-chart-area text-4xl text-gray-300 mb-3"></i>
                <p class="text-sm font-bold text-gray-400">سيتم تفعيل الرسوم البيانية عند اكتمال قسم المواعيد والمالية</p>
            </div>
        </div>

        <!-- Recent Activities (Real-time Audit Log Preview) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-black text-gray-800 text-lg">أحدث النشاطات</h3>
                <a href="{{ route('audit_logs.index') }}" class="text-xs font-bold text-teal-600 hover:underline">السجل الكامل</a>
            </div>
            
            <div class="space-y-4 max-h-[300px] overflow-y-auto custom-scrollbar pr-2">
                @forelse($recentActivities ?? [] as $log)
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 shrink-0 text-xs font-bold overflow-hidden border border-gray-200">
                        @if($log->causer && $log->causer->avatar)
                            <img src="{{ asset('storage/' . $log->causer->avatar) }}" class="w-full h-full object-cover">
                        @else
                            {{ mb_substr($log->causer->name ?? 'م', 0, 1) }}
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            <span class="font-bold">{{ $log->causer->name ?? 'النظام' }}</span>
                            @if(in_array($log->event, ['login', 'logout']))
                                <span class="font-bold text-teal-600">{{ $log->event === 'login' ? 'سجل دخول' : 'سجل خروج' }}</span> للنظام
                            @else
                                <span class="text-gray-500">قام بـ</span>
                                <span class="font-bold text-teal-600">
                                    {{ match($log->event) { 'created' => 'إضافة', 'updated' => 'تعديل', 'deleted' => 'حذف', 'rollback' => 'استرجاع', default => $log->event } }}
                                </span>
                                سجل في 
                                <span class="font-bold text-gray-600">
                                    {{ match(class_basename($log->subject_type)) {
                                        'Setting' => 'الإعدادات',
                                        'User' => 'المستخدمين',
                                        'Role' => 'الصلاحيات',
                                        default => class_basename($log->subject_type)
                                    } }}
                                </span>
                            @endif
                        </p>
                        <p class="text-[10px] text-gray-400 mt-1 font-mono" dir="ltr">{{ $log->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <p class="text-sm text-gray-400 font-bold">لا توجد نشاطات مسجلة بعد</p>
                </div>
                @endforelse
            </div>
        </div>
        
    </div>
</div>
@endsection
