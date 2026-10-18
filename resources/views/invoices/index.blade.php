@extends('layouts.app')

@section('title', 'الفواتير والمدفوعات - نظام العيادة')
@section('header', 'إدارة الفواتير والمدفوعات')

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-white p-6 rounded-3xl shadow-sm border border-gray-100 gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center text-2xl shadow-sm">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-gray-800">قائمة الفواتير الطبية</h2>
                <p class="text-sm text-gray-500 font-bold mt-1">إصدار ومتابعة الفواتير وتحصيل المدفوعات من المرضى.</p>
            </div>
        </div>
        <a href="{{ route('invoices.create') }}" class="w-full md:w-auto bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl text-sm font-bold shadow-sm transition flex items-center justify-center gap-2">
            <i class="fa-solid fa-plus"></i> إصدار فاتورة جديدة
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-r-4 border-green-500 text-green-700 p-4 rounded-xl flex items-center gap-3 font-bold shadow-sm">
            <i class="fa-solid fa-circle-check text-xl"></i>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Invoices List -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-right whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase">رقم الفاتورة</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase">المريض</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase">الطبيب</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase">التاريخ</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase">الإجمالي</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase">المدفوع / المتبقي</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase">الحالة</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($invoices as $invoice)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4">
                            <span class="font-mono font-bold text-gray-800 bg-gray-100 px-2 py-1 rounded text-xs">#{{ $invoice->invoice_number }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800 text-sm">{{ $invoice->patient->name ?? 'غير محدد' }}</div>
                            <div class="text-[10px] text-gray-500">{{ $invoice->patient->phone ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-700 text-sm"><i class="fa-solid fa-user-md text-blue-400 ml-1 text-xs"></i> {{ $invoice->doctor->name ?? 'غير محدد' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600 font-mono" dir="ltr">{{ \Carbon\Carbon::parse($invoice->issue_date)->format('Y-m-d') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-black text-gray-800 font-mono" dir="ltr">{{ number_format($invoice->total, 2) }} <span class="text-[10px] text-gray-400">{{ $global_settings['currency'] ?? 'SAR' }}</span></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 font-mono text-sm">
                                <span class="text-green-600 font-bold" title="المدفوع" dir="ltr">{{ number_format($invoice->paid_amount, 2) }}</span>
                                <span class="text-gray-300">/</span>
                                <span class="text-red-500 font-bold" title="المتبقي" dir="ltr">{{ number_format($invoice->balance, 2) }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($invoice->status === 'paid')
                                <span class="bg-green-50 text-green-700 border border-green-200 px-3 py-1 rounded-lg text-xs font-black">مدفوعة بالكامل</span>
                            @elseif($invoice->status === 'partially_paid')
                                <span class="bg-amber-50 text-amber-700 border border-amber-200 px-3 py-1 rounded-lg text-xs font-black">مدفوعة جزئياً</span>
                            @else
                                <span class="bg-red-50 text-red-700 border border-red-200 px-3 py-1 rounded-lg text-xs font-black">غير مدفوعة</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('invoices.show', $invoice->id) }}" class="w-8 h-8 rounded-lg bg-gray-50 hover:bg-blue-50 text-gray-500 hover:text-blue-600 border border-gray-200 transition flex items-center justify-center" title="عرض الفاتورة ودفعها">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>
                                @if($invoice->status !== 'paid')
                                <a href="{{ route('invoices.show', $invoice->id) }}?pay=true" class="w-8 h-8 rounded-lg bg-green-50 hover:bg-green-100 text-green-600 border border-green-200 transition flex items-center justify-center" title="إضافة دفعة">
                                    <i class="fa-solid fa-money-bill-wave text-xs"></i>
                                </a>
                                @endif
                                <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من إلغاء وحذف هذه الفاتورة؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-600 border border-gray-200 transition flex items-center justify-center">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <i class="fa-solid fa-file-invoice-dollar text-5xl text-gray-200 mb-4 block"></i>
                            <h3 class="text-lg font-black text-gray-700 mb-1">لا توجد فواتير</h3>
                            <p class="text-sm text-gray-500 font-bold mb-4">لم يتم إصدار أي فواتير للمرضى حتى الآن.</p>
                            <a href="{{ route('invoices.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-sm transition inline-flex items-center gap-2">
                                <i class="fa-solid fa-plus"></i> إصدار فاتورة جديدة
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($invoices->hasPages())
        <div class="p-4 border-t border-gray-100 bg-gray-50/50">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
