@extends('layouts.app')

@section('title', 'الملف الشخصي - د. ' . $doctor->name)
@section('header', 'الملف الشخصي للطبيب')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden relative">
        <div class="h-32 bg-gradient-to-r from-teal-800 to-teal-500 relative">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
        </div>
        
        <div class="px-8 pb-8 flex flex-col md:flex-row gap-6 items-start md:items-end -mt-12 relative z-10">
            <div class="w-32 h-32 rounded-2xl bg-white p-2 shadow-lg shrink-0">
                <div class="w-full h-full rounded-xl bg-teal-50 flex items-center justify-center text-teal-600 text-4xl font-black overflow-hidden relative border border-teal-100">
                    @if($doctor->avatar)
                        <img src="{{ asset('storage/' . $doctor->avatar) }}" class="w-full h-full object-cover">
                    @else
                        {{ mb_substr($doctor->name, 0, 1) }}
                    @endif
                    
                    @if($doctor->is_present)
                        <div class="absolute bottom-1 right-1 w-4 h-4 rounded-full border-2 border-white bg-green-500 shadow-sm tooltip" title="حاضر في العيادة"></div>
                    @else
                        <div class="absolute bottom-1 right-1 w-4 h-4 rounded-full border-2 border-white bg-orange-400 shadow-sm tooltip" title="في إجازة"></div>
                    @endif
                </div>
            </div>
            
            <div class="flex-1">
                <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 mb-2">
                    <h1 class="text-2xl font-black text-gray-800">{{ $doctor->name }}</h1>
                    @if($doctor->status !== 'نشط')
                        <span class="bg-red-50 text-red-600 px-3 py-1 rounded-full text-xs font-bold border border-red-100">حساب موقوف</span>
                    @endif
                </div>
                <p class="text-teal-600 font-bold mb-1">{{ $doctor->specialization }}</p>
                <div class="flex items-center gap-4 text-sm text-gray-500">
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-clinic-medical text-gray-400"></i> العيادة: {{ $doctor->clinic->name ?? 'غير محدد' }}</span>
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-id-card text-gray-400"></i> ترخيص: <span class="font-mono">{{ $doctor->license_number ?? 'غير مسجل' }}</span></span>
                </div>
            </div>
            
            <div class="flex gap-2 w-full md:w-auto">
                <a href="{{ route('doctors.index') }}" class="flex-1 md:flex-none text-center bg-gray-50 hover:bg-gray-100 text-gray-700 px-6 py-2.5 rounded-xl font-bold transition shadow-sm border border-gray-200">
                    <i class="fa-solid fa-arrow-right ml-2"></i> رجوع
                </a>
                <button onclick="window.print()" class="flex-1 md:flex-none text-center bg-teal-custom hover:bg-teal-700 text-white px-6 py-2.5 rounded-xl font-bold transition shadow-sm">
                    <i class="fa-solid fa-print ml-2"></i> طباعة الملف
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Contact Info -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-black text-gray-800 mb-5 flex items-center gap-2 border-b border-gray-50 pb-3">
                    <i class="fa-solid fa-address-book text-teal-500"></i> بيانات التواصل
                </h3>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 shrink-0 border border-gray-100">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 mb-0.5 uppercase">رقم الجوال</p>
                            <p class="text-sm font-bold text-gray-700" dir="ltr">{{ $doctor->phone ?? 'غير متوفر' }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 shrink-0 border border-gray-100">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 mb-0.5 uppercase">البريد الإلكتروني</p>
                            <p class="text-sm font-bold text-gray-700 truncate" dir="ltr" title="{{ $doctor->email }}">{{ $doctor->email }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 shrink-0 border border-gray-100">
                            <i class="fa-solid fa-id-badge"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 mb-0.5 uppercase">معرف الدخول للمستشفى</p>
                            <p class="text-sm font-bold text-gray-700 font-mono" dir="ltr">{{ $doctor->username }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings / Limits -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-black text-gray-800 mb-5 flex items-center gap-2 border-b border-gray-50 pb-3">
                    <i class="fa-solid fa-sliders text-blue-500"></i> إعدادات المواعيد
                </h3>
                
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100 mb-3">
                    <span class="text-xs font-bold text-gray-600"><i class="fa-solid fa-clock text-gray-400 ml-1"></i> مدة الكشف الافتراضية</span>
                    <span class="text-sm font-black text-teal-600">{{ $doctor->consultation_duration }} دقيقة</span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Schedule / Calendar Placeholder -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-6 border-b border-gray-50 pb-3">
                    <h3 class="font-black text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-calendar-week text-teal-500"></i> جدول المواعيد والعمل
                    </h3>
                    <a href="#" class="text-xs font-bold text-teal-600 hover:underline">عرض الجدول الكامل</a>
                </div>
                
                <div class="bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 p-8 text-center flex flex-col items-center justify-center">
                    <i class="fa-solid fa-calendar-day text-5xl text-gray-300 mb-4"></i>
                    <h4 class="font-bold text-gray-700 mb-1">لم يتم إعداد جدول أسبوعي بعد</h4>
                    <p class="text-sm text-gray-500 max-w-sm mb-4">قم بإضافة أيام وساعات عمل الطبيب ليتمكن الموظفون من حجز مواعيد للمرضى لديه.</p>
                    <button class="bg-teal-50 text-teal-600 hover:bg-teal-100 px-5 py-2 rounded-xl text-xs font-bold transition border border-teal-100">
                        <i class="fa-solid fa-plus ml-1"></i> إعداد الجدول الأسبوعي (قريباً)
                    </button>
                </div>
            </div>

            <!-- Recent Appointments Placeholder -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-black text-gray-800 mb-5 flex items-center gap-2 border-b border-gray-50 pb-3">
                    <i class="fa-solid fa-notes-medical text-teal-500"></i> أحدث المواعيد (الحجوزات)
                </h3>
                
                <div class="text-center py-10 bg-gray-50 rounded-2xl border border-gray-100">
                    <i class="fa-solid fa-folder-open text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500 text-sm font-bold">لا توجد أي حجوزات مسجلة لهذا الطبيب بعد.</p>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    @media print {
        body { background: white; }
        header, aside, .btn-print, a { display: none !important; }
        main { padding: 0 !important; margin: 0 !important; }
        .shadow-sm { box-shadow: none !important; border-color: #e5e7eb !important; }
    }
</style>
@endsection
