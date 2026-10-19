@extends('layouts.app')

@section('title', 'فحص وصرف الوصفة ' . $prescription->prescription_number)
@section('header', 'تفاصيل الوصفة الطبية وصرف الأدوية (Dispensing)')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('prescriptions.index') }}" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition">
                <i class="fa-solid fa-arrow-right"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-black text-gray-800 font-mono">{{ $prescription->prescription_number }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $prescription->status === 'مصروفة' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                        {{ $prescription->status }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 font-bold mt-0.5">تاريخ الإصدار: {{ $prescription->created_at->format('Y-m-d H:i') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <a href="{{ route('prescriptions.print', $prescription->id) }}" target="_blank" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-teal-50 border border-teal-200 text-teal-700 text-xs font-bold hover:bg-teal-100 transition">
                <i class="fa-solid fa-print"></i>
                طباعة الوصفة (Rx)
            </a>

            @if($prescription->status !== 'مصروفة')
            <form action="{{ route('prescriptions.dispense', $prescription->id) }}" method="POST" onsubmit="return confirm('تأكيد صرف هذه الوصفة للمريض وخصم الكميات من المستودع آلياً؟')">
                @csrf
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-md shadow-teal-600/20 transition">
                    <i class="fa-solid fa-check-double"></i>
                    صرف الوصفة وخصم المخزون
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Meta Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Patient Card -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm space-y-4">
            <h3 class="text-sm font-black text-gray-800 border-b border-gray-100 pb-3 flex items-center gap-2">
                <i class="fa-solid fa-user-injured text-teal-600"></i>
                بيانات المريض
            </h3>

            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-700 font-bold text-lg flex items-center justify-center shrink-0">
                    {{ mb_substr($prescription->patient->name ?? 'م', 0, 1) }}
                </div>
                <div>
                    <h4 class="font-black text-gray-800 text-sm">
                        <a href="{{ route('patients.show', $prescription->patient_id) }}" class="hover:text-teal-600">
                            {{ $prescription->patient->name }}
                        </a>
                    </h4>
                    <p class="text-xs text-gray-400 font-mono">MRN: {{ $prescription->patient->mrn }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs pt-2">
                <div class="p-2.5 rounded-xl bg-gray-50">
                    <span class="block text-gray-400 text-[10px] font-bold">العمر</span>
                    <span class="font-bold text-gray-700">{{ \Carbon\Carbon::parse($prescription->patient->date_of_birth)->age }} سنة</span>
                </div>
                <div class="p-2.5 rounded-xl bg-gray-50">
                    <span class="block text-gray-400 text-[10px] font-bold">الجنس</span>
                    <span class="font-bold text-gray-700">{{ $prescription->patient->gender == 'M' || $prescription->patient->gender == 'ذكر' ? 'ذكر' : 'أنثى' }}</span>
                </div>
                <div class="p-2.5 rounded-xl bg-gray-50">
                    <span class="block text-gray-400 text-[10px] font-bold">رقم الهاتف</span>
                    <span class="font-bold text-gray-700 font-mono" dir="ltr">{{ $prescription->patient->phone ?? '—' }}</span>
                </div>
                <div class="p-2.5 rounded-xl bg-gray-50">
                    <span class="block text-gray-400 text-[10px] font-bold">الحساسية المعروفة</span>
                    <span class="font-bold text-rose-600">{{ $prescription->patient->allergies ?? 'لا توجد حساسية مسجلة' }}</span>
                </div>
            </div>
        </div>

        <!-- Prescription Details Card -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-5 shadow-sm space-y-4">
            <h3 class="text-sm font-black text-gray-800 border-b border-gray-100 pb-3 flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-user text-teal-600"></i>
                    بيانات الطبيب ومسار الصرف
                </span>
                <span class="text-xs font-mono text-gray-400">#{{ $prescription->prescription_number }}</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
                    <span class="block text-gray-400 text-[10px] font-bold">الطبيب المعالج</span>
                    <span class="font-bold text-gray-800">د. {{ $prescription->doctor->name ?? 'طبيب عام' }}</span>
                    @if($prescription->visit_id)
                        <a href="{{ route('visits.show', $prescription->visit_id) }}" class="block text-[10px] text-teal-600 hover:underline mt-0.5">زيارة طبية #{{ $prescription->visit_id }}</a>
                    @endif
                </div>

                <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
                    <span class="block text-gray-400 text-[10px] font-bold">الصيدلي المصرح بالصرف</span>
                    <span class="font-bold text-gray-800">{{ $prescription->pharmacist->name ?? 'بانتظار الصيدلي' }}</span>
                </div>

                <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
                    <span class="block text-gray-400 text-[10px] font-bold">وقت وتاريخ الصرف</span>
                    <span class="font-bold text-gray-800 font-mono">{{ $prescription->dispensed_at ? $prescription->dispensed_at->format('Y-m-d H:i') : 'لم تصرف بعد' }}</span>
                </div>
            </div>

            @if($prescription->notes)
            <div class="p-3 bg-amber-50/50 rounded-xl border border-amber-100 text-xs">
                <span class="font-bold text-amber-800 block mb-1">تعليمات الطبيب المعالج:</span>
                <p class="text-gray-700 leading-relaxed">{{ $prescription->notes }}</p>
            </div>
            @endif
        </div>

    </div>

    <!-- Medicines List Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h3 class="font-black text-gray-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-pills text-teal-600"></i>
                الأدوية الموصوفة وتدقيق التوفر بالمستودع
            </h3>
            <span class="text-xs font-bold bg-teal-50 text-teal-700 px-3 py-1 rounded-lg border border-teal-100">
                عدد البنود: {{ $prescription->items->count() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-gray-100/70 text-gray-600 font-bold">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">اسم الدواء الموصوف</th>
                        <th class="py-3 px-4">الجرعة والتكرار</th>
                        <th class="py-3 px-4">المدة</th>
                        <th class="py-3 px-4">الكمية الموصوفة</th>
                        <th class="py-3 px-4">المتوفر بالمستودع</th>
                        <th class="py-3 px-4 text-center">حالة الصرف</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @foreach($prescription->items as $index => $item)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="py-3.5 px-4 font-mono font-bold text-gray-400">{{ $index + 1 }}</td>
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-gray-800 block text-xs">{{ $item->medicine_name }}</span>
                            @if($item->instructions)
                                <span class="text-[10px] text-gray-500 block mt-0.5">{{ $item->instructions }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-gray-700">
                            {{ $item->dosage ?? '—' }} {{ $item->frequency ? '• ' . $item->frequency : '' }}
                        </td>
                        <td class="py-3.5 px-4 text-gray-700">
                            {{ $item->duration ?? '—' }}
                        </td>
                        <td class="py-3.5 px-4 font-mono font-bold text-gray-800">
                            {{ $item->quantity }}
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            @if($item->medicine)
                                @if($item->medicine->stock_quantity >= $item->quantity)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700 font-mono">
                                        متوفر ({{ $item->medicine->stock_quantity }})
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700 font-mono">
                                        غير كافٍ ({{ $item->medicine->stock_quantity }})
                                    </span>
                                @endif
                            @else
                                <span class="text-gray-400 text-[10px]">غير مربوط بالمخزن</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            @if($item->is_dispensed)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700">
                                    <i class="fa-solid fa-check ml-0.5"></i> تم الصرف
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">
                                    معلق
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
