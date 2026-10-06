@extends('layouts.app')

@section('title', 'ملف المريض - ' . $patient->full_name)

@section('content')
<div x-data="{ activeTab: 'profile' }">
    <!-- Patient Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-2xl font-bold text-teal-custom mb-1">{{ $patient->full_name }}</h1>
            <p class="text-gray-500 text-sm font-mono" dir="rtl">
                {{ $patient->mrn }} - {{ $patient->gender ?? 'غير محدد' }} - 
                {{ $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->age . ' سنة' : '-- سنة' }}
            </p>
        </div>
        
        <div class="flex gap-2">
            <button class="bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition">
                ملخص للطباعة
            </button>
            <button class="bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition">
                حضور مباشر
            </button>
            <button class="bg-teal-custom hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                حجز موعد
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex gap-2 mb-6 border-b border-gray-200 pb-2 overflow-x-auto">
        <a href="#" @click.prevent="activeTab = 'profile'" 
           :class="activeTab === 'profile' ? 'bg-teal-custom text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50'"
           class="px-5 py-2 rounded-full text-sm font-medium transition whitespace-nowrap">
            الملف
        </a>
        <a href="#" @click.prevent="activeTab = 'allergies'" 
           :class="activeTab === 'allergies' ? 'bg-teal-custom text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50'"
           class="px-5 py-2 rounded-full text-sm font-medium transition whitespace-nowrap">
            الحساسية والأمراض
        </a>
        <a href="#" @click.prevent="activeTab = 'appointments'" 
           :class="activeTab === 'appointments' ? 'bg-teal-custom text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50'"
           class="px-5 py-2 rounded-full text-sm font-medium transition whitespace-nowrap">
            المواعيد
        </a>
        <a href="#" @click.prevent="activeTab = 'visits'" 
           :class="activeTab === 'visits' ? 'bg-teal-custom text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50'"
           class="px-5 py-2 rounded-full text-sm font-medium transition whitespace-nowrap">
            الزيارات
        </a>
        <a href="#" @click.prevent="activeTab = 'lab'" 
           :class="activeTab === 'lab' ? 'bg-teal-custom text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50'"
           class="px-5 py-2 rounded-full text-sm font-medium transition whitespace-nowrap">
            المختبر والأشعة
        </a>
        <a href="#" @click.prevent="activeTab = 'invoices'" 
           :class="activeTab === 'invoices' ? 'bg-teal-custom text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50'"
           class="px-5 py-2 rounded-full text-sm font-medium transition whitespace-nowrap">
            الفواتير
        </a>
    </div>

    <!-- Tab Contents -->
    <div>
        <!-- Profile Tab -->
        <div x-show="activeTab === 'profile'" x-transition.opacity class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Main Details (Right in RTL, spans 2 cols) -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-4">
                    
                    <!-- Column 1 -->
                    <div class="space-y-6">
                        <div>
                            <div class="text-xs text-gray-400 mb-1">الهوية</div>
                            <div class="font-medium text-gray-800 font-mono">{{ $patient->id_number ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 mb-1">الجوال</div>
                            <div class="font-medium text-gray-800 font-mono" dir="ltr">{{ $patient->mobile ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 mb-1">المدينة</div>
                            <div class="font-medium text-gray-800">{{ $patient->city ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 mb-1">فصيلة الدم</div>
                            <div class="font-medium text-gray-800 font-mono" dir="ltr">{{ $patient->blood_type ?? '—' }}</div>
                        </div>
                    </div>

                    <!-- Column 2 -->
                    <div class="space-y-6">
                        <div>
                            <div class="text-xs text-gray-400 mb-1">النوع</div>
                            <div class="font-medium text-gray-800">{{ $patient->id_type ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 mb-1">الجنسية</div>
                            <div class="font-medium text-gray-800">{{ $patient->nationality ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 mb-1">الحي</div>
                            <div class="font-medium text-gray-800">{{ $patient->district ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 mb-1">البريد الإلكتروني</div>
                            <div class="font-medium text-gray-800 font-mono">—</div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Side Panel (Left in RTL, spans 1 col) -->
            <div class="space-y-6">
                
                <!-- Insurance Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-teal-custom mb-3">التأمين</h3>
                    <div class="flex items-center gap-3 text-gray-400 bg-gray-50 p-4 rounded-lg">
                        <i class="fa-solid fa-shield-halved text-gray-300 text-xl"></i>
                        <span class="text-sm">لا يوجد تأمين نشط</span>
                    </div>
                </div>

                <!-- Emergency Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-teal-custom mb-3">جهات الطوارئ</h3>
                    @if($patient->emergency_contact)
                        <div class="bg-gray-50 p-4 rounded-lg font-mono text-gray-700" dir="ltr">
                            {{ $patient->emergency_contact }}
                        </div>
                    @else
                        <div class="text-sm text-gray-400 bg-gray-50 p-4 rounded-lg">
                            لم يتم تسجيل جهة اتصال للطوارئ
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Allergies Tab -->
        <div x-show="activeTab === 'allergies'" x-transition.opacity style="display: none;">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 mx-auto mb-4 text-2xl">
                    <i class="fa-solid fa-notes-medical"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-700 mb-2">لا توجد سجلات للأمراض المزمنة أو الحساسية</h3>
                <p class="text-gray-500 text-sm mb-6">يمكنك إضافة بيانات الحساسية أو الأمراض المزمنة الخاصة بالمريض هنا.</p>
                <button class="bg-teal-custom hover:bg-teal-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                    <i class="fa-solid fa-plus ml-1"></i> إضافة سجل جديد
                </button>
            </div>
        </div>

        <!-- Appointments Tab -->
        <div x-show="activeTab === 'appointments'" x-transition.opacity style="display: none;">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 mx-auto mb-4 text-2xl">
                    <i class="fa-regular fa-calendar-check"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-700 mb-2">لا توجد مواعيد سابقة</h3>
                <p class="text-gray-500 text-sm mb-6">سجل المواعيد الخاصة بهذا المريض فارغ حالياً.</p>
                <button class="bg-teal-custom hover:bg-teal-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                    <i class="fa-solid fa-calendar-plus ml-1"></i> حجز موعد جديد
                </button>
            </div>
        </div>

        <!-- Visits Tab -->
        <div x-show="activeTab === 'visits'" x-transition.opacity style="display: none;">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 mx-auto mb-4 text-2xl">
                    <i class="fa-solid fa-stethoscope"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-700 mb-2">سجل الزيارات فارغ</h3>
                <p class="text-gray-500 text-sm">لم يقم المريض بأي زيارة للعيادات حتى الآن.</p>
            </div>
        </div>

        <!-- Lab & Radiology Tab -->
        <div x-show="activeTab === 'lab'" x-transition.opacity style="display: none;">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 mx-auto mb-4 text-2xl">
                    <i class="fa-solid fa-flask"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-700 mb-2">لا توجد نتائج فحوصات</h3>
                <p class="text-gray-500 text-sm">ستظهر نتائج التحاليل المخبرية والأشعة هنا فور صدورها.</p>
            </div>
        </div>

        <!-- Invoices Tab -->
        <div x-show="activeTab === 'invoices'" x-transition.opacity style="display: none;">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 mx-auto mb-4 text-2xl">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-700 mb-2">لا توجد فواتير</h3>
                <p class="text-gray-500 text-sm">لم يتم إصدار أي فواتير مالية أو مطالبات لهذا المريض.</p>
            </div>
        </div>

    </div>
</div>
@endsection
