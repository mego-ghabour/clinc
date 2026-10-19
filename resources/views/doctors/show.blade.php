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
            
            <!-- Schedule / Calendar Section -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-6 border-b border-gray-50 pb-3">
                    <h3 class="font-black text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-calendar-week text-teal-500"></i> جدول المواعيد والعمل
                    </h3>
                    <a href="{{ route('doctor-schedules.show', $doctor->id) }}" class="text-xs font-bold text-teal-600 hover:underline flex items-center gap-1">
                        <span>إدارة وتعديل الجدول الكامل</span>
                        <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    </a>
                </div>
                
                @if($doctor->doctorSchedules && $doctor->doctorSchedules->where('is_working', true)->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                        @foreach($doctor->doctorSchedules->where('is_working', true) as $sch)
                            <div class="bg-teal-50/50 border border-teal-100 rounded-2xl p-3 flex justify-between items-center">
                                <span class="font-bold text-teal-900 text-xs">{{ $sch->day_of_week }}</span>
                                <div class="text-[11px] text-teal-700 font-mono flex flex-wrap gap-1">
                                    @if(is_array($sch->shifts) && count($sch->shifts) > 0)
                                        @foreach($sch->shifts as $shift)
                                            <span class="bg-white px-2 py-0.5 rounded-lg border border-teal-100 shadow-xs">{{ $shift['start'] ?? '' }} - {{ $shift['end'] ?? '' }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-gray-400">فترة كاملة</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-left pt-2 border-t border-gray-50">
                        <a href="{{ route('doctor-schedules.show', $doctor->id) }}" class="inline-flex items-center gap-1.5 bg-teal-50 text-teal-700 hover:bg-teal-100 px-4 py-2 rounded-xl text-xs font-bold transition border border-teal-100">
                            <i class="fa-solid fa-pen-to-square"></i> تعديل الفترات وساعات الكشف
                        </a>
                    </div>
                @else
                    <div class="bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 p-8 text-center flex flex-col items-center justify-center">
                        <i class="fa-solid fa-calendar-day text-5xl text-gray-300 mb-4"></i>
                        <h4 class="font-bold text-gray-700 mb-1">لم يتم إعداد جدول أسبوعي بعد</h4>
                        <p class="text-sm text-gray-500 max-w-sm mb-4">قم بإضافة أيام وساعات عمل الطبيب ومناوباته ليتمكن الموظفون من حجز مواعيد للمرضى لديه.</p>
                        <a href="{{ route('doctor-schedules.show', $doctor->id) }}" class="bg-teal-600 text-white hover:bg-teal-700 px-5 py-2.5 rounded-xl text-xs font-bold transition shadow-sm inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-plus ml-1"></i> إعداد جدول العمل الأسبوعي
                        </a>
                    </div>
                @endif
            </div>

            <!-- Recent Appointments Section -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-5 border-b border-gray-50 pb-3">
                    <h3 class="font-black text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-notes-medical text-teal-500"></i> أحدث المواعيد (الحجوزات)
                    </h3>
                    <a href="{{ route('appointments.index', ['doctor_id' => $doctor->id]) }}" class="text-xs font-bold text-teal-600 hover:underline">
                        عرض كافة المواعيد
                    </a>
                </div>
                
                @if(isset($recentAppointments) && $recentAppointments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-right text-xs">
                            <thead>
                                <tr class="text-gray-400 border-b border-gray-100 font-bold">
                                    <th class="pb-3">رقم الموعد</th>
                                    <th class="pb-3">المريض</th>
                                    <th class="pb-3">التاريخ والوقت</th>
                                    <th class="pb-3">الحالة</th>
                                    <th class="pb-3 text-left">التفاصيل</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 font-medium text-gray-700">
                                @foreach($recentAppointments as $apt)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="py-3 font-mono font-bold text-teal-700">{{ $apt->appointment_number }}</td>
                                    <td class="py-3 font-bold">{{ $apt->patient->name ?? 'غير محدد' }}</td>
                                    <td class="py-3 font-mono">{{ $apt->appointment_date }} | {{ $apt->appointment_time }}</td>
                                    <td class="py-3">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold 
                                            {{ $apt->status == 'مكتمل' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 
                                              ($apt->status == 'ملغي' ? 'bg-rose-50 text-rose-600 border border-rose-100' : 'bg-amber-50 text-amber-600 border border-amber-100') }}">
                                            {{ $apt->status }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-left">
                                        <a href="{{ route('appointments.index', ['search' => $apt->appointment_number]) }}" class="px-3 py-1 bg-gray-100 hover:bg-teal-50 hover:text-teal-700 rounded-lg text-[11px] font-bold transition">
                                            معاينة
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-10 bg-gray-50 rounded-2xl border border-gray-100">
                        <i class="fa-solid fa-folder-open text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500 text-sm font-bold">لا توجد أي حجوزات مسجلة لهذا الطبيب بعد.</p>
                    </div>
                @endif
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
