@extends('layouts.app')

@section('title', 'مطالبة تأمين ' . $insuranceClaim->claim_number)
@section('header', 'متابعة وتدقيق مطالبة التأمين الطبي')

@section('content')
<div class="space-y-6" x-data="{ showRejectModal: false }">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('insurance-claims.index') }}" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition">
                <i class="fa-solid fa-arrow-right"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-black text-gray-800 font-mono">{{ $insuranceClaim->claim_number }}</h2>
                    @php
                        $statusStyles = [
                            'معلقة' => 'bg-amber-100 text-amber-700',
                            'موافقة مبدئية' => 'bg-blue-100 text-blue-700',
                            'مقبولة' => 'bg-emerald-100 text-emerald-700',
                            'مسددة' => 'bg-green-100 text-green-800 font-black',
                            'مرفوضة' => 'bg-rose-100 text-rose-700',
                        ];
                    @endphp
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $statusStyles[$insuranceClaim->status] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ $insuranceClaim->status }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 font-bold mt-0.5">تاريخ المطالبة: {{ $insuranceClaim->claim_date->format('Y-m-d') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <a href="{{ route('insurance-claims.print', $insuranceClaim->id) }}" target="_blank" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-teal-50 border border-teal-200 text-teal-700 text-xs font-bold hover:bg-teal-100 transition">
                <i class="fa-solid fa-print"></i>
                طباعة نموذج المطالبة
            </a>

            <!-- Status Buttons -->
            @if($insuranceClaim->status === 'معلقة')
            <form action="{{ route('insurance-claims.update-status', $insuranceClaim->id) }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="status" value="موافقة مبدئية">
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition">
                    موافقة مبدئية
                </button>
            </form>
            @endif

            @if($insuranceClaim->status !== 'مقبولة' && $insuranceClaim->status !== 'مسددة')
            <form action="{{ route('insurance-claims.update-status', $insuranceClaim->id) }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="status" value="مقبولة">
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition">
                    قبول المطالبة
                </button>
            </form>
            @endif

            @if($insuranceClaim->status === 'مقبولة')
            <form action="{{ route('insurance-claims.update-status', $insuranceClaim->id) }}" method="POST" class="inline" onsubmit="return confirm('تأكيد تسجيل استلام وتحصيل مبلغ المطالبة من شركة التأمين؟')">
                @csrf
                <input type="hidden" name="status" value="مسددة">
                <button type="submit" class="px-4 py-2 rounded-xl bg-green-600 hover:bg-green-700 text-white text-xs font-bold transition shadow-sm">
                    <i class="fa-solid fa-check-double ml-1"></i> تم التحصيل والسداد
                </button>
            </form>
            @endif

            @if($insuranceClaim->status !== 'مرفوضة')
            <button type="button" @click="showRejectModal = true" class="px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition">
                رفض المطالبة
            </button>
            @endif
        </div>
    </div>

    @if($insuranceClaim->status === 'مرفوضة' && $insuranceClaim->rejection_reason)
    <div class="p-4 bg-rose-50 rounded-2xl border border-rose-200 text-xs text-rose-800">
        <strong class="block font-bold mb-1"><i class="fa-solid fa-circle-xmark ml-1"></i> سبب رفض شركة التأمين للمطالبة:</strong>
        <p>{{ $insuranceClaim->rejection_reason }}</p>
    </div>
    @endif

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Patient & Insurance Policy Card -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm space-y-4">
            <h3 class="text-sm font-black text-gray-800 border-b border-gray-100 pb-3 flex items-center gap-2">
                <i class="fa-solid fa-id-card text-teal-600"></i>
                بيانات المريض والتأمين
            </h3>

            <div>
                <h4 class="font-black text-gray-800 text-sm">
                    <a href="{{ route('patients.show', $insuranceClaim->patient_id) }}" class="hover:text-teal-600">
                        {{ $insuranceClaim->patient->name }}
                    </a>
                </h4>
                <p class="text-xs text-gray-400 font-mono">MRN: {{ $insuranceClaim->patient->mrn }}</p>
            </div>

            <div class="p-3.5 bg-teal-50/50 rounded-xl border border-teal-100 space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-gray-500 font-bold">شركة التأمين:</span>
                    <strong class="text-teal-800">{{ $insuranceClaim->insuranceCompany->name }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 font-bold">رمز الشركة:</span>
                    <span class="font-mono text-gray-700">{{ $insuranceClaim->insuranceCompany->code }}</span>
                </div>
                @if($insuranceClaim->patient->activeInsurance)
                <div class="flex justify-between">
                    <span class="text-gray-500 font-bold">رقم البطاقة:</span>
                    <span class="font-mono text-gray-700">{{ $insuranceClaim->patient->activeInsurance->card_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 font-bold">فئة التغطية:</span>
                    <span class="font-bold text-gray-700">{{ $insuranceClaim->patient->activeInsurance->class }}</span>
                </div>
                @endif
            </div>

            <div class="text-xs space-y-1 text-gray-500 pt-1">
                <p>الطبيب المعالج: <strong class="text-gray-700">د. {{ $insuranceClaim->doctor->name ?? 'طبيب عام' }}</strong></p>
                @if($insuranceClaim->invoice_id)
                <p>الفاتورة المرتبطة: <a href="{{ route('invoices.show', $insuranceClaim->invoice_id) }}" class="text-teal-600 hover:underline font-mono">#{{ $insuranceClaim->invoice->invoice_number ?? $insuranceClaim->invoice_id }}</a></p>
                @endif
            </div>
        </div>

        <!-- Financial Breakdown Card -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-5 shadow-sm space-y-5">
            <h3 class="text-sm font-black text-gray-800 border-b border-gray-100 pb-3 flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-calculator text-teal-600"></i>
                    التوزيع والتدفق المالي للمطالبة
                </span>
                <span class="text-xs font-mono text-gray-400">#{{ $insuranceClaim->claim_number }}</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 text-center">
                    <span class="block text-gray-400 text-xs font-bold mb-1">إجمالي الفاتورة الطبية</span>
                    <h4 class="text-xl font-black text-gray-800 font-mono">{{ number_format($insuranceClaim->total_amount, 2) }} <span class="text-xs font-sans">ر.س</span></h4>
                </div>

                <div class="p-4 rounded-2xl bg-amber-50/50 border border-amber-100 text-center">
                    <span class="block text-amber-700 text-xs font-bold mb-1">تحمل المريض (Copay)</span>
                    <h4 class="text-xl font-black text-amber-800 font-mono">{{ number_format($insuranceClaim->patient_copay, 2) }} <span class="text-xs font-sans">ر.س</span></h4>
                </div>

                <div class="p-4 rounded-2xl bg-teal-50 border border-teal-200 text-center">
                    <span class="block text-teal-700 text-xs font-bold mb-1">المطالب به من شركة التأمين</span>
                    <h4 class="text-2xl font-black text-teal-800 font-mono">{{ number_format($insuranceClaim->insurance_share, 2) }} <span class="text-xs font-sans">ر.س</span></h4>
                </div>
            </div>

            @if($insuranceClaim->notes)
            <div class="p-3 bg-gray-50 rounded-xl border border-gray-100 text-xs">
                <span class="font-bold text-gray-600 block mb-1">ملاحظات المطالبة:</span>
                <p class="text-gray-700 leading-relaxed">{{ $insuranceClaim->notes }}</p>
            </div>
            @endif

            @if($insuranceClaim->settled_at)
            <div class="p-3 bg-green-50 rounded-xl border border-green-200 text-xs text-green-800 flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-base"></i>
                <span>تم تحصيل وتسوية المطالبة في: <strong class="font-mono">{{ $insuranceClaim->settled_at->format('Y-m-d H:i') }}</strong></span>
            </div>
            @endif
        </div>

    </div>

    <!-- Reject Reason Modal -->
    <div x-show="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-900/50 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="showRejectModal = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden relative transform transition-all">
            <div class="bg-rose-600 p-5 text-white flex justify-between items-center">
                <h3 class="font-black text-base flex items-center gap-2"><i class="fa-solid fa-circle-xmark"></i> رفض مطالبة التأمين</h3>
                <button @click="showRejectModal = false" class="text-white/70 hover:text-white transition"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form action="{{ route('insurance-claims.update-status', $insuranceClaim->id) }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="status" value="مرفوضة">

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">سبب الرفض الصادر من شركة التأمين <span class="text-rose-500">*</span></label>
                    <textarea name="rejection_reason" required rows="3" placeholder="مثال: الخدمة غير مشمولة بالوثيقة، تجاوز الحد الائتماني..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-rose-500 resize-none"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <button type="submit" class="flex-1 bg-rose-600 hover:bg-rose-700 text-white py-2.5 rounded-xl text-xs font-bold transition">تأكيد الرفض</button>
                    <button type="button" @click="showRejectModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
