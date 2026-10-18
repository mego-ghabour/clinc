@extends('layouts.app')

@section('title', 'تفاصيل الفاتورة - #' . $invoice->invoice_number)
@section('header', 'عرض الفاتورة والمدفوعات')

@section('content')
<div class="space-y-6" x-data="{ showPaymentModal: {{ request('pay') == 'true' ? 'true' : 'false' }} }">

    <!-- Top Action Bar (Hidden on Print) -->
    <div class="flex justify-between items-center bg-white p-4 lg:p-6 rounded-3xl shadow-sm border border-gray-100 print:hidden">
        <div class="flex items-center gap-4">
            <a href="{{ route('invoices.index') }}" class="w-10 h-10 bg-gray-50 hover:bg-gray-100 text-gray-600 rounded-xl flex items-center justify-center transition">
                <i class="fa-solid fa-arrow-right"></i>
            </a>
            <div>
                <h2 class="text-xl font-black text-gray-800 font-mono">#{{ $invoice->invoice_number }}</h2>
                <div class="flex items-center gap-2 mt-1">
                    @if($invoice->status === 'paid')
                        <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-[10px] font-black">مدفوعة بالكامل</span>
                    @elseif($invoice->status === 'partially_paid')
                        <span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded text-[10px] font-black">مدفوعة جزئياً</span>
                    @else
                        <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded text-[10px] font-black">غير مدفوعة</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($invoice->status !== 'paid')
            <button @click="showPaymentModal = true" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-money-bill-wave"></i> تحصيل دفعة
            </button>
            @endif
            <button onclick="window.print()" class="bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> طباعة A4
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-r-4 border-green-500 text-green-700 p-4 rounded-xl flex items-center gap-3 font-bold shadow-sm print:hidden">
            <i class="fa-solid fa-circle-check text-xl"></i>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Printable A4 Invoice -->
        <div class="lg:col-span-2">
            <!-- A4 Wrapper -->
            <div class="bg-white rounded-none md:rounded-3xl shadow-sm border border-gray-100 p-8 md:p-12 print:p-0 print:border-none print:shadow-none w-full max-w-[210mm] mx-auto min-h-[297mm]">
                
                <!-- Invoice Header -->
                <div class="flex justify-between items-start border-b-2 border-gray-100 pb-8 mb-8">
                    <div>
                        @php $appLogo = \App\Models\Setting::where('key', 'app_logo')->value('value'); @endphp
                        @if($appLogo)
                            <img src="{{ asset('storage/' . $appLogo) }}" class="h-16 w-auto mb-4" alt="Clinic Logo">
                        @else
                            <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center text-2xl shadow-sm mb-4">
                                <i class="fa-solid fa-heart-pulse"></i>
                            </div>
                        @endif
                        <h1 class="text-2xl font-black text-gray-900">{{ $global_settings['app_name'] ?? 'نظام العيادات' }}</h1>
                        <p class="text-sm text-gray-500 font-bold mt-1">{{ $global_settings['hospital_address'] ?? 'العنوان غير مسجل' }}</p>
                        <p class="text-sm text-gray-500 font-bold mt-1" dir="ltr">{{ $global_settings['hospital_phone'] ?? 'الهاتف غير مسجل' }}</p>
                        @if(!empty($global_settings['vat_number']))
                        <p class="text-sm text-gray-500 font-bold mt-1">الرقم الضريبي: <span class="font-mono">{{ $global_settings['vat_number'] }}</span></p>
                        @endif
                    </div>
                    
                    <div class="text-left">
                        <h2 class="text-4xl font-black text-gray-200 uppercase tracking-wider mb-4">INVOICE</h2>
                        <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            <span class="text-gray-400 font-bold">رقم الفاتورة:</span>
                            <span class="font-black text-gray-800 font-mono" dir="ltr">#{{ $invoice->invoice_number }}</span>
                            
                            <span class="text-gray-400 font-bold">تاريخ الإصدار:</span>
                            <span class="font-black text-gray-800 font-mono" dir="ltr">{{ \Carbon\Carbon::parse($invoice->issue_date)->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Patient & Doctor Info -->
                <div class="grid grid-cols-2 gap-8 mb-8 bg-gray-50 p-6 rounded-2xl border border-gray-100">
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-wider mb-2">فاتورة إلى (المريض)</h4>
                        <p class="text-lg font-black text-gray-800">{{ $invoice->patient->name ?? 'غير محدد' }}</p>
                        <p class="text-sm text-gray-600 font-bold mt-1">الهوية: <span class="font-mono" dir="ltr">{{ $invoice->patient->id_number ?? 'غير مسجل' }}</span></p>
                        <p class="text-sm text-gray-600 font-bold mt-1" dir="ltr">{{ $invoice->patient->phone ?? '' }}</p>
                    </div>
                    <div class="text-left">
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-wider mb-2">الطبيب المعالج</h4>
                        <p class="text-lg font-black text-gray-800">د. {{ $invoice->doctor->name ?? 'غير محدد' }}</p>
                        <p class="text-sm text-gray-600 font-bold mt-1">{{ $invoice->doctor->specialization ?? 'عام' }}</p>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="mb-8">
                    <table class="w-full text-right">
                        <thead>
                            <tr class="bg-gray-100 border-b-2 border-gray-200">
                                <th class="px-4 py-3 text-xs font-black text-gray-600 uppercase w-12 text-center">#</th>
                                <th class="px-4 py-3 text-xs font-black text-gray-600 uppercase">وصف الخدمة</th>
                                <th class="px-4 py-3 text-xs font-black text-gray-600 uppercase text-center w-24">الكمية</th>
                                <th class="px-4 py-3 text-xs font-black text-gray-600 uppercase text-center w-32">السعر</th>
                                <th class="px-4 py-3 text-xs font-black text-gray-600 uppercase text-left w-32">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($invoice->items as $index => $item)
                            <tr>
                                <td class="px-4 py-4 text-sm font-bold text-gray-400 text-center">{{ $index + 1 }}</td>
                                <td class="px-4 py-4 text-sm font-black text-gray-800">{{ $item->service->name ?? 'خدمة محذوفة' }}</td>
                                <td class="px-4 py-4 text-sm font-bold text-gray-600 text-center font-mono">{{ $item->quantity }}</td>
                                <td class="px-4 py-4 text-sm font-bold text-gray-600 text-center font-mono" dir="ltr">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-4 py-4 text-sm font-black text-gray-800 text-left font-mono" dir="ltr">{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="flex justify-end mb-8">
                    <div class="w-1/2 md:w-1/3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm font-bold text-gray-500">المجموع الفرعي:</span>
                            <span class="font-mono font-bold text-gray-800" dir="ltr">{{ number_format($invoice->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm font-bold text-gray-500">الضريبة المضافة:</span>
                            <span class="font-mono font-bold text-gray-800" dir="ltr">{{ number_format($invoice->tax, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-4 border-b-2 border-gray-800">
                            <span class="text-base font-black text-gray-900">الإجمالي الشامل:</span>
                            <span class="font-mono font-black text-xl text-green-600" dir="ltr">
                                {{ number_format($invoice->total, 2) }} <span class="text-xs text-gray-400">{{ $global_settings['currency'] ?? 'SAR' }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Notes & QR Code -->
                <div class="flex justify-between items-end border-t border-gray-100 pt-8">
                    <div class="w-2/3">
                        <h4 class="text-xs font-black text-gray-400 uppercase mb-2">ملاحظات / تعليمات:</h4>
                        <p class="text-sm font-bold text-gray-600">{{ $invoice->notes ?: ($global_settings['invoice_terms'] ?? 'نتمنى لكم دوام الصحة والعافية.') }}</p>
                    </div>
                    <!-- Placeholder for ZATCA QR Code -->
                    <div class="w-24 h-24 bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-center p-2">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=Invoice:{{ $invoice->invoice_number }}|Total:{{ $invoice->total }}" class="w-full h-full opacity-50 grayscale mix-blend-multiply">
                    </div>
                </div>

            </div>
        </div>

        <!-- Right Column: Payments Log (Hidden on Print) -->
        <div class="lg:col-span-1 print:hidden">
            <div class="bg-gray-900 rounded-3xl p-6 text-white shadow-xl mb-6 relative overflow-hidden border border-gray-800">
                <div class="absolute -right-12 -top-12 w-32 h-32 bg-green-500 rounded-full blur-3xl opacity-20"></div>
                <h3 class="text-sm font-black text-gray-400 uppercase tracking-wider mb-4">حالة الدفع</h3>
                
                <div class="flex justify-between items-end mb-4 border-b border-gray-700 pb-4">
                    <span class="text-gray-300 font-bold">المدفوع:</span>
                    <span class="text-2xl font-black text-green-400 font-mono" dir="ltr">{{ number_format($invoice->paid_amount, 2) }}</span>
                </div>
                
                <div class="flex justify-between items-end">
                    <span class="text-gray-300 font-bold">المتبقي للدفع:</span>
                    <span class="text-2xl font-black text-red-400 font-mono" dir="ltr">{{ number_format($invoice->balance, 2) }}</span>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-black text-gray-800 mb-6 border-b border-gray-50 pb-3">سجل الدفعات</h3>
                
                @if($invoice->payments->count() > 0)
                <div class="space-y-4">
                    @foreach($invoice->payments as $payment)
                    <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4 flex flex-col gap-3">
                        <div class="flex justify-between items-center">
                            <span class="font-black text-green-600 font-mono text-lg" dir="ltr">+ {{ number_format($payment->amount, 2) }}</span>
                            <span class="text-[10px] font-bold text-gray-400 bg-white px-2 py-1 rounded border border-gray-200">{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs font-bold text-gray-500">
                            <span>طريقة الدفع: {{ $payment->payment_method }}</span>
                            <span>بواسطة: {{ $payment->cashier->name ?? 'مجهول' }}</span>
                        </div>
                        @if($payment->notes)
                        <div class="text-xs text-gray-400 bg-white p-2 rounded border border-gray-100">{{ $payment->notes }}</div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fa-solid fa-receipt text-4xl text-gray-200 mb-3 block"></i>
                    <p class="text-sm text-gray-400 font-bold">لم يتم تسجيل أي دفعات بعد.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add Payment Modal -->
    <div x-show="showPaymentModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900/60 backdrop-blur-sm p-4 print:hidden">
        <div x-show="showPaymentModal" x-transition.opacity class="relative w-full max-w-sm bg-white rounded-3xl shadow-2xl p-6 border border-gray-100" @click.away="showPaymentModal = false">
            <div class="flex justify-between items-center mb-6 border-b border-gray-50 pb-4">
                <h3 class="text-xl font-black text-gray-800">إضافة دفعة نقدية/شبكة</h3>
                <button @click="showPaymentModal = false" class="text-gray-400 hover:text-red-500 transition">
                    <i class="fa-solid fa-xmark text-2xl"></i>
                </button>
            </div>

            <form action="{{ route('payments.store', $invoice->id) }}" method="POST">
                @csrf
                <div class="space-y-5">
                    
                    <div class="bg-red-50 text-red-700 p-3 rounded-xl border border-red-100 flex justify-between items-center">
                        <span class="text-xs font-black">المبلغ المتبقي:</span>
                        <span class="font-mono font-black" dir="ltr">{{ number_format($invoice->balance, 2) }}</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">المبلغ المحصل <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="amount" value="{{ $invoice->balance }}" max="{{ $invoice->balance }}" required dir="ltr" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white rounded-xl text-lg font-black text-green-600 text-center transition outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">طريقة الدفع <span class="text-red-500">*</span></label>
                        <select name="payment_method" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white rounded-xl text-sm font-bold transition outline-none appearance-none">
                            <option value="نقدي">نقدي (Cash)</option>
                            <option value="شبكة (مدى)">شبكة (Mada)</option>
                            <option value="بطاقة ائتمانية (Visa/Mastercard)">بطاقة ائتمانية (Visa/Mastercard)</option>
                            <option value="حوالة بنكية">حوالة بنكية</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">رقم العملية / ملاحظات</label>
                        <input type="text" name="notes" placeholder="اختياري..." class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white rounded-xl text-sm font-bold transition outline-none">
                    </div>

                </div>

                <div class="mt-8">
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-3.5 rounded-xl text-sm font-black shadow-lg shadow-green-500/30 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check"></i> تأكيد تحصيل المبلغ
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
