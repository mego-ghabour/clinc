@extends('layouts.app')

@section('title', 'مطالبات التأمين الطبي - Clinc')
@section('header', 'إدارة ومتابعة مطالبات التأمين الطبي (Insurance Claims)')

@section('content')
<div class="space-y-6" x-data="{ showNewModal: false }">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <h2 class="text-xl font-black text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-file-invoice-dollar text-teal-600"></i>
                لوحة مطالبات التأمين الصحي
            </h2>
            <p class="text-xs text-gray-500 font-bold mt-1">إصدار مطالبات الشركات، تتبع نسب التحمل، الموافقات المبدئية والتحصيل المالي</p>
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <a href="{{ route('insurance-companies.index') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-700 text-xs font-bold transition">
                <i class="fa-solid fa-building-shield"></i>
                شركات التأمين
            </a>
            <button @click="showNewModal = true" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-md shadow-teal-600/20 transition">
                <i class="fa-solid fa-plus"></i>
                تسجيل مطالبة جديدة
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-file-signature"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500">إجمالي المطالبات</p>
                <h4 class="text-2xl font-black text-gray-800">{{ $stats['total_claims'] }}</h4>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500">معلقة بانتظار الموافقة</p>
                <h4 class="text-2xl font-black text-amber-600">{{ $stats['pending_claims'] }}</h4>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500">مجموع مبالغ المطالبات</p>
                <h4 class="text-xl font-black text-purple-600 font-mono">{{ number_format($stats['total_insurance_share'], 2) }} <span class="text-xs font-sans">ر.س</span></h4>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500">المحصل والمُسدد</p>
                <h4 class="text-xl font-black text-green-600 font-mono">{{ number_format($stats['settled_amount'], 2) }} <span class="text-xs font-sans">ر.س</span></h4>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <form method="GET" action="{{ route('insurance-claims.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1">بحث برقم المطالبة أو المريض</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم المطالبة أو اسم المريض..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1">شركة التأمين</label>
                <select name="insurance_company_id" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                    <option value="all">جميع الشركات</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('insurance_company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1">حالة المطالبة</label>
                <select name="status" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                    <option value="all">جميع الحالات</option>
                    <option value="معلقة" {{ request('status') == 'معلقة' ? 'selected' : '' }}>معلقة</option>
                    <option value="موافقة مبدئية" {{ request('status') == 'موافقة مبدئية' ? 'selected' : '' }}>موافقة مبدئية</option>
                    <option value="مقبولة" {{ request('status') == 'مقبولة' ? 'selected' : '' }}>مقبولة</option>
                    <option value="مسددة" {{ request('status') == 'مسددة' ? 'selected' : '' }}>مسددة (تم التحصيل)</option>
                    <option value="مرفوضة" {{ request('status') == 'مرفوضة' ? 'selected' : '' }}>مرفوضة</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white py-2 rounded-xl text-xs font-bold transition">
                    <i class="fa-solid fa-filter ml-1"></i> تصفية
                </button>
                @if(request()->hasAny(['search', 'insurance_company_id', 'status']))
                    <a href="{{ route('insurance-claims.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-bold transition">إلغاء</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Claims Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-gray-50/80 text-gray-500 font-bold border-b border-gray-100">
                    <tr>
                        <th class="py-3 px-4">رقم المطالبة</th>
                        <th class="py-3 px-4">المريض</th>
                        <th class="py-3 px-4">شركة التأمين</th>
                        <th class="py-3 px-4">إجمالي الفاتورة</th>
                        <th class="py-3 px-4">تحمل المريض</th>
                        <th class="py-3 px-4">مطالبة التأمين</th>
                        <th class="py-3 px-4">الحالة</th>
                        <th class="py-3 px-4">التاريخ</th>
                        <th class="py-3 px-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @forelse($claims as $claim)
                    <tr class="hover:bg-gray-50/60 transition">
                        <td class="py-3.5 px-4 font-mono font-bold text-gray-800 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-teal-50 text-teal-700 border border-teal-100">
                                <i class="fa-solid fa-file-invoice text-[10px]"></i>
                                {{ $claim->claim_number }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4">
                            <a href="{{ route('patients.show', $claim->patient_id) }}" class="font-bold text-gray-800 hover:text-teal-600 block">
                                {{ $claim->patient->name ?? '—' }}
                            </a>
                            <span class="text-[10px] text-gray-400 font-mono">MRN: {{ $claim->patient->mrn ?? '—' }}</span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="font-bold text-gray-800 block">{{ $claim->insuranceCompany->name ?? '—' }}</span>
                            <span class="text-[10px] text-gray-400 font-mono">{{ $claim->insuranceCompany->code ?? '' }}</span>
                        </td>
                        <td class="py-3.5 px-4 font-mono font-bold text-gray-700 whitespace-nowrap">
                            {{ number_format($claim->total_amount, 2) }} ر.س
                        </td>
                        <td class="py-3.5 px-4 font-mono text-gray-600 whitespace-nowrap">
                            {{ number_format($claim->patient_copay, 2) }} ر.س
                        </td>
                        <td class="py-3.5 px-4 font-mono font-black text-teal-700 whitespace-nowrap">
                            {{ number_format($claim->insurance_share, 2) }} ر.س
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            @php
                                $statusStyles = [
                                    'معلقة' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'موافقة مبدئية' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'مقبولة' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'مسددة' => 'bg-green-100 text-green-800 border-green-300 font-black',
                                    'مرفوضة' => 'bg-rose-50 text-rose-700 border-rose-200',
                                ];
                            @endphp
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold border {{ $statusStyles[$claim->status] ?? 'bg-gray-50 text-gray-700' }}">
                                {{ $claim->status }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap font-mono text-gray-500 text-[11px]">
                            {{ $claim->claim_date->format('Y-m-d') }}
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('insurance-claims.show', $claim->id) }}" class="p-1.5 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-700 transition" title="عرض ومتابعة المطالبة">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('insurance-claims.print', $claim->id) }}" target="_blank" class="p-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 transition" title="طباعة النموذج الرسمي">
                                    <i class="fa-solid fa-print text-xs"></i>
                                </a>
                                <form action="{{ route('insurance-claims.destroy', $claim->id) }}" method="POST" onsubmit="return confirm('تأكيد حذف المطالبة؟')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition" title="حذف">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-12 text-gray-400">
                            <i class="fa-solid fa-file-shield text-4xl mb-3 text-gray-300"></i>
                            <p class="font-bold text-sm">لا توجد مطالبات تأمين مطابقة لمعايير البحث</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($claims->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $claims->links() }}
        </div>
        @endif
    </div>

    <!-- New Claim Modal -->
    <div x-show="showNewModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-900/50 backdrop-blur-sm p-4" style="display: none;"
         x-data="{
             totalAmount: 0,
             copayRate: 20,
             calcShares() {
                 let total = parseFloat(this.totalAmount) || 0;
                 let rate = parseFloat(this.copayRate) || 0;
                 let copay = (total * (rate / 100));
                 document.getElementById('copay_input').value = copay.toFixed(2);
                 document.getElementById('insurance_share_input').value = (total - copay).toFixed(2);
             }
         }">
        <div @click.away="showNewModal = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-xl overflow-hidden relative transform transition-all max-h-[90vh] flex flex-col">
            <div class="bg-teal-600 p-5 text-white flex justify-between items-center shrink-0">
                <h3 class="font-black text-base flex items-center gap-2"><i class="fa-solid fa-file-circle-plus"></i> تسجيل مطالبة تأمين صحي جديدة</h3>
                <button @click="showNewModal = false" class="text-white/70 hover:text-white transition"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="{{ route('insurance-claims.store') }}" method="POST" class="p-6 overflow-y-auto space-y-4 flex-1">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">المريض <span class="text-rose-500">*</span></label>
                        <select name="patient_id" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                            <option value="">-- اختر المريض --</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->name }} (MRN: {{ $patient->mrn }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">شركة التأمين <span class="text-rose-500">*</span></label>
                        <select name="insurance_company_id" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                            <option value="">-- اختر شركة التأمين --</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">تاريخ المطالبة <span class="text-rose-500">*</span></label>
                        <input type="date" name="claim_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500 font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">الطبيب المعالج</label>
                        <select name="doctor_id" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                            <option value="">-- اختياري --</option>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}" {{ auth()->id() == $doc->id ? 'selected' : '' }}>د. {{ $doc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="p-4 bg-teal-50/50 rounded-2xl border border-teal-100 space-y-3">
                    <h5 class="text-xs font-black text-teal-800">حساب المبالغ ونسبة التحمل</h5>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 mb-1">المبلغ الإجمالي للخدمة/الفاتورة (ر.س) <span class="text-rose-500">*</span></label>
                            <input type="number" step="0.01" min="0" name="total_amount" x-model="totalAmount" @input="calcShares()" required placeholder="0.00" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold font-mono focus:border-teal-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 mb-1">نسبة تحمل المريض (%)</label>
                            <input type="number" step="0.01" min="0" max="100" x-model="copayRate" @input="calcShares()" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold font-mono focus:border-teal-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 mb-1">مبلغ تحمل المريض (Copay)</label>
                            <input type="number" step="0.01" min="0" name="patient_copay" id="copay_input" required class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold font-mono text-gray-700">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 mb-1">المطالب به من شركة التأمين</label>
                            <input type="number" step="0.01" min="0" name="insurance_share" id="insurance_share_input" required class="w-full px-3 py-2 bg-white border border-teal-300 rounded-xl text-xs font-black font-mono text-teal-700">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">ملاحظات المطالبة</label>
                    <textarea name="notes" rows="2" placeholder="رقم الموافقة المسبقة أو تفاصيل التشخيص الطبي..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-teal-500 resize-none"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                    <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white py-2.5 rounded-xl text-xs font-bold transition">حفظ وإصدار المطالبة</button>
                    <button type="button" @click="showNewModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
