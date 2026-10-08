@extends('layouts.app')

@section('title', 'إدارة الزيارات والملفات الطبية')
@section('header', 'الزيارات الطبية')

@section('content')
<div x-data="visitManager()">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-1">الزيارات الحالية والسابقة</h1>
            <p class="text-gray-500 text-sm">إدارة دخول المرضى، الفرز، والملفات الطبية (EMR)</p>
        </div>
        <button @click="openAddModal()" class="bg-teal-custom hover:bg-teal-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-notes-medical"></i> تسجيل زيارة جديدة
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('visits.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
            
            <div class="flex-1 w-full relative">
                <i class="fa-solid fa-search absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم المريض أو رقم الملف..." class="w-full pr-11 pl-4 py-2.5 bg-gray-50 border border-transparent focus:border-teal-custom/50 focus:bg-white rounded-xl text-sm transition font-bold">
            </div>

            <div class="w-full md:w-64">
                <select name="status" class="w-full px-4 py-2.5 bg-gray-50 border border-transparent focus:border-teal-custom/50 focus:bg-white rounded-xl text-sm transition font-bold text-gray-700 cursor-pointer" onchange="this.form.submit()">
                    <option value="all">كل الحالات</option>
                    <option value="قيد الانتظار" {{ request('status') == 'قيد الانتظار' ? 'selected' : '' }}>قيد الانتظار (في الاستقبال)</option>
                    <option value="جاري الكشف" {{ request('status') == 'جاري الكشف' ? 'selected' : '' }}>جاري الكشف (عند الطبيب)</option>
                    <option value="مكتملة" {{ request('status') == 'مكتملة' ? 'selected' : '' }}>مكتملة (تم الانتهاء)</option>
                    <option value="ملغاة" {{ request('status') == 'ملغاة' ? 'selected' : '' }}>ملغاة</option>
                </select>
            </div>
            
            @if(request()->hasAny(['search', 'status']) && (request('search') != '' || request('status') != 'all'))
            <a href="{{ route('visits.index') }}" class="text-red-500 bg-red-50 hover:bg-red-100 px-4 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2">
                <i class="fa-solid fa-times"></i> مسح
            </a>
            @endif
        </form>
    </div>

    <!-- Visits List -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead class="bg-gray-50 text-gray-600 font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">رقم الزيارة / التاريخ</th>
                        <th class="px-6 py-4">المريض</th>
                        <th class="px-6 py-4">الطبيب المعالج</th>
                        <th class="px-6 py-4">نوع الزيارة</th>
                        <th class="px-6 py-4">حالة الزيارة</th>
                        <th class="px-6 py-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-700">
                    @forelse($visits as $visit)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4">
                            <div class="font-mono font-bold text-teal-600 mb-0.5">#VST-{{ str_pad($visit->id, 5, '0', STR_PAD_LEFT) }}</div>
                            <div class="text-xs text-gray-500 font-bold" dir="ltr">{{ $visit->visit_date->format('Y-m-d h:i A') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-black text-gray-800 text-sm">{{ $visit->patient->name }}</div>
                            <div class="text-xs text-gray-500 font-mono">{{ $visit->patient->mrn }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-700 text-sm">د. {{ $visit->doctor->name }}</div>
                            <div class="text-xs text-gray-500">{{ $visit->doctor->specialization ?? 'عام' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-xs font-bold border border-blue-100">
                                {{ $visit->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'قيد الانتظار' => 'bg-orange-50 text-orange-600 border-orange-100 animate-pulse',
                                    'جاري الكشف' => 'bg-blue-50 text-blue-600 border-blue-100 animate-pulse',
                                    'مكتملة' => 'bg-green-50 text-green-600 border-green-100',
                                    'ملغاة' => 'bg-red-50 text-red-600 border-red-100',
                                ];
                                $statusIcon = [
                                    'قيد الانتظار' => 'fa-clock',
                                    'جاري الكشف' => 'fa-stethoscope',
                                    'مكتملة' => 'fa-check-circle',
                                    'ملغاة' => 'fa-times-circle',
                                ];
                                $color = $statusColors[$visit->status] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                                $icon = $statusIcon[$visit->status] ?? 'fa-circle';
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black border {{ $color }}">
                                <i class="fa-solid {{ $icon }}"></i> {{ $visit->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('visits.show', $visit->id) }}" class="bg-gray-50 text-teal-600 hover:bg-teal-50 px-3 py-2 rounded-lg text-xs font-bold transition flex items-center gap-1.5 border border-gray-100">
                                    <i class="fa-solid fa-notes-medical"></i> عرض الملف الطبي
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-gray-500">
                            <i class="fa-solid fa-clipboard-list text-5xl mb-4 text-gray-300"></i>
                            <p class="font-bold text-lg text-gray-700">لا توجد زيارات</p>
                            <p class="text-sm">لم يتم تسجيل أي زيارات مرضية بعد.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($visits->hasPages())
        <div class="flex justify-center mb-8">
            {{ $visits->links() }}
        </div>
    @endif

    <!-- Add Visit Modal -->
    <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="isModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm transition-opacity" @click="isModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="isModalOpen" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block align-bottom bg-white rounded-3xl text-right overflow-visible shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-gray-100">
                
                <form action="{{ route('visits.store') }}" method="POST">
                    @csrf
                    
                    <div class="bg-white px-6 py-5 border-b border-gray-100 flex justify-between items-center rounded-t-3xl sticky top-0 z-10">
                        <h3 class="text-xl font-black text-gray-800 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-sm shadow-sm">
                                <i class="fa-solid fa-plus"></i>
                            </div>
                            تسجيل دخول زيارة جديدة
                        </h3>
                        <button type="button" @click="isModalOpen = false" class="w-8 h-8 rounded-full bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition flex items-center justify-center">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <div class="px-8 py-6 space-y-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                        
                        <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                            <h4 class="text-sm font-bold text-teal-700 mb-4 flex items-center gap-2 border-b border-gray-200 pb-2">
                                <i class="fa-solid fa-user-injured"></i> بيانات المريض والطبيب
                            </h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">اختر المريض (الملف الطبي) <span class="text-red-500">*</span></label>
                                    <select name="patient_id" required class="w-full px-4 py-3 bg-white border border-gray-200 focus:border-teal-custom rounded-xl text-sm transition font-bold shadow-sm">
                                        <option value="" disabled selected>ابحث وافتر المرضى المسجلين...</option>
                                        @foreach($patients as $patient)
                                            <option value="{{ $patient->id }}">{{ $patient->name }} ({{ $patient->mrn }})</option>
                                        @endforeach
                                    </select>
                                    <p class="text-[10px] text-gray-400 mt-1.5 font-bold">ملاحظة: إذا كان مريضاً جديداً، يجب تسجيله أولاً في قسم المرضى.</p>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">الطبيب المعالج <span class="text-red-500">*</span></label>
                                    <select name="doctor_id" required class="w-full px-4 py-3 bg-white border border-gray-200 focus:border-teal-custom rounded-xl text-sm transition font-bold shadow-sm">
                                        <option value="" disabled selected>اختر الطبيب المتاح...</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->id }}">د. {{ $doctor->name }} - {{ $doctor->specialization ?? 'عام' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                            <h4 class="text-sm font-bold text-teal-700 mb-4 flex items-center gap-2 border-b border-gray-200 pb-2">
                                <i class="fa-solid fa-clipboard-list"></i> تفاصيل الزيارة
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">نوع الزيارة <span class="text-red-500">*</span></label>
                                    <select name="type" required class="w-full px-4 py-3 bg-white border border-gray-200 focus:border-teal-custom rounded-xl text-sm transition font-bold shadow-sm">
                                        <option value="كشف جديد">كشف جديد</option>
                                        <option value="استشارة">استشارة / مراجعة</option>
                                        <option value="متابعة دورية">متابعة دورية</option>
                                        <option value="طوارئ">حالة طوارئ</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">تاريخ ووقت الدخول <span class="text-red-500">*</span></label>
                                    <input type="datetime-local" name="visit_date" value="{{ now()->format('Y-m-d\TH:i') }}" required class="w-full px-4 py-3 bg-white border border-gray-200 focus:border-teal-custom rounded-xl text-sm transition font-bold shadow-sm">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hidden Status (Always starts as Pending when created from Reception) -->
                        <input type="hidden" name="status" value="قيد الانتظار">

                    </div>

                    <div class="bg-white px-6 py-4 border-t border-gray-100 flex justify-end gap-3 rounded-b-3xl">
                        <button type="button" @click="isModalOpen = false" class="bg-gray-50 border border-gray-200 text-gray-700 hover:bg-gray-100 px-6 py-2.5 rounded-xl text-sm font-bold transition shadow-sm">
                            إلغاء
                        </button>
                        <button type="submit" class="bg-teal-custom hover:bg-teal-700 text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-sm transition flex items-center gap-2">
                            <i class="fa-solid fa-notes-medical"></i> تسجيل وحفظ الزيارة
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    window.visitManager = function() {
        return {
            isModalOpen: {{ request('open_modal') ? 'true' : 'false' }},
            openAddModal() {
                this.isModalOpen = true;
            }
        };
    };
</script>
@endsection
