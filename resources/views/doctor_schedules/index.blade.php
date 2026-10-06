@extends('layouts.app')

@section('title', 'مواعيد الأطباء المتاحة - فكرتك الرقمية')

@section('content')
<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-teal-custom mb-1">مواعيد الأطباء المتاحة</h1>
        <p class="text-gray-500 text-sm">من هنا تتحكم في أيام وساعات كل طبيب الأيام المغلقة أو الإجازات تختفي تلقائياً من حجز الموظفين ومن الحجز الأونلاين</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-6 items-start">
        
        <!-- Left Panel: Content -->
        <div class="w-full lg:flex-1 order-2 lg:order-1">
            @if(!$selectedDoctor)
                <!-- Empty State -->
                <div class="bg-white rounded-xl border border-dashed border-gray-300 p-16 text-center min-h-[400px] flex flex-col items-center justify-center">
                    <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center text-teal-custom mx-auto mb-4 text-xl">
                        <i class="fa-solid fa-hand-pointer"></i>
                    </div>
                    <h3 class="text-xl font-bold text-teal-custom mb-2">اختر طبيباً من القائمة</h3>
                    <p class="text-gray-400 text-sm">بعد الاختيار قم بفتح أو إغلاق أيام الأسبوع وحدد من-إلى لكل فترة، وتسجيل إجازة.</p>
                </div>
            @else
                <!-- Doctor Selected State -->
                <!-- Top Section: Weekly Schedule -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    
                    <div class="flex justify-between items-start mb-6">
                        <h2 class="text-lg font-bold text-gray-800">الدوام الأسبوعي</h2>
                        <div class="text-right">
                            <h3 class="font-bold text-teal-custom">{{ $selectedDoctor->name }}</h3>
                            <p class="text-xs text-gray-500">{{ $selectedDoctor->specialization ?? 'عام' }} - {{ $selectedDoctor->clinic->name ?? '' }}</p>
                        </div>
                    </div>

                    <div class="text-xs text-gray-400 mb-4 bg-gray-50 p-2 rounded inline-block">الحد الأعلى 3 فترات لليوم الواحد</div>

                    <!-- Days Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-7 gap-4 mb-6">
                        @php
                            $days = ['السبت' => false, 'الأحد' => true, 'الاثنين' => true, 'الثلاثاء' => true, 'الأربعاء' => true, 'الخميس' => true, 'الجمعة' => false];
                        @endphp
                        
                        @foreach($days as $day => $isActive)
                        <div class="border border-gray-100 rounded-lg p-3" x-data="{ isActive: {{ $isActive ? 'true' : 'false' }} }">
                            
                            <div class="flex justify-between items-center mb-3 border-b border-gray-50 pb-2">
                                <h4 class="font-bold text-gray-700 text-sm">{{ $day }}</h4>
                                <button @click="isActive = !isActive" :class="isActive ? 'bg-teal-custom' : 'bg-gray-200'" class="relative inline-flex h-4 w-8 items-center rounded-full transition-colors focus:outline-none">
                                    <span :class="isActive ? '-translate-x-4 bg-white' : 'translate-x-0 bg-white'" class="inline-block h-3 w-3 transform rounded-full transition-transform"></span>
                                </button>
                            </div>

                            <div x-show="isActive" class="space-y-2">
                                <!-- Shift 1 -->
                                <div class="bg-gray-50 border border-teal-50 rounded p-2">
                                    <div class="text-[9px] text-teal-custom font-bold mb-1 text-center">فترة 1</div>
                                    <div class="flex items-center justify-between text-[10px]">
                                        <span class="text-gray-400">من</span>
                                        <input type="time" value="09:00" class="bg-transparent border-none p-0 text-gray-700 text-center font-mono focus:ring-0 w-16">
                                    </div>
                                    <div class="flex items-center justify-between text-[10px] mt-1 border-t border-gray-200 pt-1">
                                        <span class="text-gray-400">إلى</span>
                                        <input type="time" value="13:00" class="bg-transparent border-none p-0 text-gray-700 text-center font-mono focus:ring-0 w-16">
                                    </div>
                                </div>
                                <!-- Shift 2 -->
                                <div class="bg-gray-50 border border-teal-50 rounded p-2">
                                    <div class="text-[9px] text-teal-custom font-bold mb-1 text-center">فترة 2</div>
                                    <div class="flex items-center justify-between text-[10px]">
                                        <span class="text-gray-400">من</span>
                                        <input type="time" value="17:00" class="bg-transparent border-none p-0 text-gray-700 text-center font-mono focus:ring-0 w-16">
                                    </div>
                                    <div class="flex items-center justify-between text-[10px] mt-1 border-t border-gray-200 pt-1">
                                        <span class="text-gray-400">إلى</span>
                                        <input type="time" value="21:00" class="bg-transparent border-none p-0 text-gray-700 text-center font-mono focus:ring-0 w-16">
                                    </div>
                                </div>
                                <button class="w-full text-[10px] text-gray-400 border border-dashed border-gray-200 rounded py-1 hover:bg-gray-50 hover:text-teal-custom transition">
                                    + فترة
                                </button>
                            </div>

                            <div x-show="!isActive" class="py-4 text-center text-xs text-gray-300" style="display: none;">
                                مغلق
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <button class="bg-teal-custom hover:bg-teal-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                        حفظ المواعيد المتاحة
                    </button>
                </div>

                <!-- Bottom Section: Leaves -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-6 text-teal-custom">إجازة / إغلاق أيام محددة</h2>
                    
                    <form class="flex flex-wrap md:flex-nowrap gap-4 items-end mb-6">
                        <div class="flex-1 w-full md:w-auto">
                            <label class="block text-xs text-gray-500 mb-1">السبب</label>
                            <input type="text" value="إجازة" class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-teal-custom text-right">
                        </div>
                        <div class="w-full md:w-48">
                            <label class="block text-xs text-gray-500 mb-1">من</label>
                            <input type="date" class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-teal-custom text-right text-gray-600">
                        </div>
                        <div class="w-full md:w-48">
                            <label class="block text-xs text-gray-500 mb-1">إلى</label>
                            <input type="date" class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-teal-custom text-right text-gray-600">
                        </div>
                        <div class="w-full md:w-auto">
                            <button type="button" class="w-full border border-teal-200 text-teal-custom hover:bg-teal-50 px-6 py-2 rounded-lg text-sm font-medium transition">
                                تسجيل إجازة
                            </button>
                        </div>
                    </form>

                    <div class="border border-gray-100 rounded-lg overflow-x-auto">
                        <table class="w-full text-right text-sm whitespace-nowrap">
                            <thead class="bg-gray-50 text-gray-500 border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-3 font-medium">السبب</th>
                                    <th class="px-6 py-3 font-medium text-center">من</th>
                                    <th class="px-6 py-3 font-medium text-center">إلى</th>
                                    <th class="px-6 py-3 font-medium text-center"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700">
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-3 font-bold text-gray-800">إجازة تدريبية — تشاور، نيو عيد</td>
                                    <td class="px-6 py-3 text-center font-mono text-gray-500">2026-09-30</td>
                                    <td class="px-6 py-3 text-center font-mono text-gray-500">2026-10-31</td>
                                    <td class="px-6 py-3 text-center">
                                        <button class="text-red-400 hover:text-red-600 text-xs transition">حذف</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Panel: Doctors Sidebar -->
        <div class="w-full lg:w-72 shrink-0 order-1 lg:order-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <h3 class="text-sm font-bold text-gray-800 mb-4 text-center">اختر طبيباً</h3>
                
                <div class="space-y-2 h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($doctors as $doctor)
                    @php
                        $isSelected = $selectedDoctor && $selectedDoctor->id === $doctor->id;
                    @endphp
                    <a href="{{ route('doctor-schedules.show', $doctor->id) }}" class="flex items-center justify-between p-3 rounded-lg transition border {{ $isSelected ? 'border-teal-custom bg-teal-50' : 'border-transparent hover:bg-gray-50' }}">
                        <div class="text-right">
                            <h4 class="font-bold text-sm {{ $isSelected ? 'text-teal-custom' : 'text-gray-800' }}">{{ $doctor->name }}</h4>
                            <p class="text-xs {{ $isSelected ? 'text-teal-600/70' : 'text-gray-400' }}">{{ $doctor->specialization ?? 'عام' }}</p>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-teal-custom text-xs font-bold shrink-0">
                            {{ mb_substr(str_replace('د. ', '', $doctor->name), 0, 2) }}
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endsection
