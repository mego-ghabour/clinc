@extends('layouts.app')

@section('title', 'طلب فحص مخبري ' . $labRequest->request_number)
@section('header', 'تفاصيل الفحص المخبري وإدخال النتائج')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('lab-requests.index') }}" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition">
                <i class="fa-solid fa-arrow-right"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-black text-gray-800 font-mono">{{ $labRequest->request_number }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $labRequest->priority === 'حرج' ? 'bg-rose-100 text-rose-700 animate-pulse' : ($labRequest->priority === 'عاجل' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700') }}">
                        {{ $labRequest->priority }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $labRequest->status === 'مكتمل' ? 'bg-green-100 text-green-700' : ($labRequest->status === 'قيد التحليل' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700') }}">
                        {{ $labRequest->status }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 font-bold mt-0.5">تاريخ الإنشاء: {{ $labRequest->created_at->format('Y-m-d H:i') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            @if($labRequest->status === 'مكتمل')
                <a href="{{ route('lab-requests.print', $labRequest->id) }}" target="_blank" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-sm transition">
                    <i class="fa-solid fa-print"></i>
                    طباعة التقرير الطبي
                </a>
            @else
                <a href="{{ route('lab-requests.print', $labRequest->id) }}" target="_blank" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition">
                    <i class="fa-solid fa-print"></i>
                    معاينة الطباعة
                </a>
            @endif

            @if($labRequest->visit_id)
                <a href="{{ route('visits.show', $labRequest->visit_id) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs font-bold transition">
                    <i class="fa-solid fa-notes-medical"></i>
                    الملف الطبي للزيارة
                </a>
            @endif
        </div>
    </div>

    <!-- Patient & Workflow Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Patient Info Card -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm space-y-4">
            <h3 class="text-sm font-black text-gray-800 border-b border-gray-100 pb-3 flex items-center gap-2">
                <i class="fa-solid fa-user-injured text-teal-600"></i>
                بيانات المريض
            </h3>

            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-700 font-bold text-lg flex items-center justify-center shrink-0">
                    {{ mb_substr($labRequest->patient->name ?? 'م', 0, 1) }}
                </div>
                <div>
                    <h4 class="font-black text-gray-800 text-sm">
                        <a href="{{ route('patients.show', $labRequest->patient_id) }}" class="hover:text-teal-600">
                            {{ $labRequest->patient->name }}
                        </a>
                    </h4>
                    <p class="text-xs text-gray-400 font-mono">MRN: {{ $labRequest->patient->mrn }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs pt-2">
                <div class="p-2.5 rounded-xl bg-gray-50">
                    <span class="block text-gray-400 text-[10px] font-bold">العمر</span>
                    <span class="font-bold text-gray-700">{{ \Carbon\Carbon::parse($labRequest->patient->date_of_birth)->age }} سنة</span>
                </div>
                <div class="p-2.5 rounded-xl bg-gray-50">
                    <span class="block text-gray-400 text-[10px] font-bold">الجنس</span>
                    <span class="font-bold text-gray-700">{{ $labRequest->patient->gender == 'M' || $labRequest->patient->gender == 'ذكر' ? 'ذكر' : 'أنثى' }}</span>
                </div>
                <div class="p-2.5 rounded-xl bg-gray-50">
                    <span class="block text-gray-400 text-[10px] font-bold">رقم الهاتف</span>
                    <span class="font-bold text-gray-700 font-mono" dir="ltr">{{ $labRequest->patient->phone ?? '—' }}</span>
                </div>
                <div class="p-2.5 rounded-xl bg-gray-50">
                    <span class="block text-gray-400 text-[10px] font-bold">فصيلة الدم</span>
                    <span class="font-bold text-rose-600 font-mono">{{ $labRequest->patient->blood_group ?? 'غير مسجل' }}</span>
                </div>
            </div>

            @if($labRequest->clinical_notes)
            <div class="pt-2 border-t border-gray-100">
                <span class="block text-[11px] font-bold text-gray-500 mb-1">ملاحظات سريرية من الطبيب:</span>
                <p class="text-xs text-gray-700 bg-amber-50/50 p-2.5 rounded-xl border border-amber-100 leading-relaxed">
                    {{ $labRequest->clinical_notes }}
                </p>
            </div>
            @endif
        </div>

        <!-- Request Details & Workflow Card -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-5 shadow-sm space-y-5">
            <h3 class="text-sm font-black text-gray-800 border-b border-gray-100 pb-3 flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-timeline text-teal-600"></i>
                    مسار العمل والحالة المخبرية
                </span>
                <span class="text-xs font-mono text-gray-400">#{{ $labRequest->request_number }}</span>
            </h3>

            <!-- Status Stepper -->
            <div class="grid grid-cols-4 gap-2">
                @php
                    $steps = [
                        'معلق' => ['label' => 'معلق', 'icon' => 'fa-clock'],
                        'تم سحب العينة' => ['label' => 'سحب العينة', 'icon' => 'fa-vial'],
                        'قيد التحليل' => ['label' => 'قيد التحليل', 'icon' => 'fa-microscope'],
                        'مكتمل' => ['label' => 'مكتمل', 'icon' => 'fa-circle-check'],
                    ];
                    $statusOrder = ['معلق' => 1, 'تم سحب العينة' => 2, 'قيد التحليل' => 3, 'مكتمل' => 4];
                    $currentRank = $statusOrder[$labRequest->status] ?? 1;
                @endphp

                @foreach($steps as $key => $step)
                    @php $stepRank = $statusOrder[$key]; @endphp
                    <div class="text-center p-3 rounded-xl border {{ $stepRank <= $currentRank ? 'bg-teal-50 border-teal-200 text-teal-800' : 'bg-gray-50 border-gray-100 text-gray-400' }}">
                        <i class="fa-solid {{ $step['icon'] }} text-lg mb-1 block"></i>
                        <span class="text-[11px] font-bold block">{{ $step['label'] }}</span>
                    </div>
                @endforeach
            </div>

            <!-- Workflow Meta -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
                    <span class="block text-gray-400 text-[10px] font-bold">الطبيب المحول</span>
                    <span class="font-bold text-gray-800">د. {{ $labRequest->doctor->name ?? 'مختبر مباشر' }}</span>
                </div>
                <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
                    <span class="block text-gray-400 text-[10px] font-bold">وقت سحب العينة</span>
                    <span class="font-bold text-gray-800 font-mono">{{ $labRequest->sample_collected_at ? $labRequest->sample_collected_at->format('Y-m-d H:i') : 'لم تسحب بعد' }}</span>
                </div>
                <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
                    <span class="block text-gray-400 text-[10px] font-bold">أخصائي / فني المختبر</span>
                    <span class="font-bold text-gray-800">{{ $labRequest->technician->name ?? 'قيد الإجراء' }}</span>
                </div>
            </div>

            <!-- Quick Status Change Actions -->
            <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-gray-100">
                <span class="text-xs font-bold text-gray-500 ml-2">تحديث الحالة سريعاً:</span>
                
                @if($labRequest->status !== 'تم سحب العينة' && $labRequest->status !== 'قيد التحليل' && $labRequest->status !== 'مكتمل')
                <form action="{{ route('lab-requests.update-status', $labRequest->id) }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="status" value="تم سحب العينة">
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold transition">
                        <i class="fa-solid fa-vial ml-1"></i> تسجيل سحب العينة
                    </button>
                </form>
                @endif

                @if($labRequest->status !== 'قيد التحليل' && $labRequest->status !== 'مكتمل')
                <form action="{{ route('lab-requests.update-status', $labRequest->id) }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="status" value="قيد التحليل">
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 text-xs font-bold transition">
                        <i class="fa-solid fa-microscope ml-1"></i> بدء التحليل المخبري
                    </button>
                </form>
                @endif

                @if($labRequest->status !== 'ملغي')
                <form action="{{ route('lab-requests.update-status', $labRequest->id) }}" method="POST" class="inline" onsubmit="return confirm('هل تريد إلغاء هذا الطلب؟')">
                    @csrf
                    <input type="hidden" name="status" value="ملغي">
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold transition">
                        إلغاء الطلب
                    </button>
                </form>
                @endif
            </div>
        </div>

    </div>

    <!-- Results Entry Form -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <div>
                <h3 class="font-black text-gray-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-check text-teal-600"></i>
                    تسجيل نتائج الفحوصات المخبرية (Results Entry)
                </h3>
                <p class="text-[11px] text-gray-400 font-bold mt-0.5">أدخل قيم النتائج وحدد المؤشرات غير الطبيعية إن وجدت</p>
            </div>
            <span class="text-xs font-bold bg-teal-50 text-teal-700 px-3 py-1 rounded-lg border border-teal-100">
                عدد الفحوصات: {{ $labRequest->items->count() }}
            </span>
        </div>

        <form action="{{ route('lab-requests.update-results', $labRequest->id) }}" method="POST" class="p-5 space-y-6">
            @csrf

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-gray-100/70 text-gray-600 font-bold">
                        <tr>
                            <th class="py-3 px-4 w-1/4">اسم الفحص</th>
                            <th class="py-3 px-4 w-1/5">النتيجة (Result)</th>
                            <th class="py-3 px-4">الوحدة (Unit)</th>
                            <th class="py-3 px-4">المعدل الطبيعي (Reference Range)</th>
                            <th class="py-3 px-4 text-center">غير طبيعي؟ (Abnormal)</th>
                            <th class="py-3 px-4">ملاحظات الفحص</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($labRequest->items as $item)
                        <tr class="hover:bg-gray-50/50 transition {{ $item->is_abnormal ? 'bg-rose-50/30' : '' }}">
                            <td class="py-3 px-4">
                                <span class="font-bold text-gray-800 block text-xs">{{ $item->test_name }}</span>
                                @if($item->labTest && $item->labTest->sample_type)
                                    <span class="text-[10px] text-gray-400">العينة: {{ $item->labTest->sample_type }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <input type="text" name="items[{{ $item->id }}][result_value]" value="{{ old('items.'.$item->id.'.result_value', $item->result_value) }}" placeholder="القيمة..." class="w-full px-3 py-2 bg-gray-50 border {{ $item->is_abnormal ? 'border-rose-300 font-bold text-rose-700 bg-rose-50/50' : 'border-gray-200 text-gray-800' }} rounded-xl text-xs focus:border-teal-500 font-mono">
                            </td>
                            <td class="py-3 px-4 font-mono text-gray-600">
                                {{ $item->unit ?? ($item->labTest->unit ?? '—') }}
                            </td>
                            <td class="py-3 px-4 font-mono text-gray-600">
                                {{ $item->normal_range ?? ($item->labTest->normal_range ?? '—') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="items[{{ $item->id }}][is_abnormal]" value="1" {{ old('items.'.$item->id.'.is_abnormal', $item->is_abnormal) ? 'checked' : '' }} class="rounded text-rose-600 focus:ring-rose-500 w-4 h-4">
                                    <span class="mr-1 text-[11px] font-bold text-rose-600">شاذ / مرتفع</span>
                                </label>
                            </td>
                            <td class="py-3 px-4">
                                <input type="text" name="items[{{ $item->id }}][notes]" value="{{ old('items.'.$item->id.'.notes', $item->notes) }}" placeholder="ملاحظة خاصة..." class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-teal-500">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Submit Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-gray-100">
                <div class="text-xs text-gray-500">
                    <i class="fa-solid fa-circle-info text-teal-600 ml-1"></i>
                    عند اعتماد النتائج سيتم وسم الطلب بـ "مكتمل" وإتاحة التقرير المطبوع للطبيب والمريض.
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <button type="submit" name="action" value="save_draft" class="flex-1 sm:flex-none px-5 py-2.5 rounded-xl border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-700 text-xs font-bold transition">
                        <i class="fa-solid fa-floppy-disk ml-1"></i> حفظ كمسودة
                    </button>
                    <button type="submit" name="action" value="finalize" class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-md shadow-teal-600/20 transition">
                        <i class="fa-solid fa-check-double ml-1"></i> اعتماد النتائج وإغلاق الطلب
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection
