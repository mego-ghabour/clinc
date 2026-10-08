@extends('layouts.app')

@section('title', 'إصدار فاتورة جديدة - نظام العيادة')
@section('header', 'إصدار فاتورة طبية جديدة')

@section('content')
<div class="space-y-6" x-data="invoiceCreator()">

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 flex justify-between items-center relative overflow-hidden">
        <div class="absolute top-0 left-0 w-24 h-24 bg-green-50 rounded-full -ml-12 -mt-12 opacity-50"></div>
        <div class="flex items-center gap-4 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-xl shadow-sm border border-green-100">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-gray-800 mb-1">فاتورة طبية جديدة</h2>
                <p class="text-xs text-gray-500 font-bold">يرجى تعبئة بيانات المريض والخدمات المقدمة لإصدار الفاتورة.</p>
            </div>
        </div>
        <a href="{{ route('invoices.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2">
            رجوع
        </a>
    </div>

    <form action="{{ route('invoices.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf
        
        <!-- Right Column: Services (Items) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-black text-gray-800">الخدمات الطبية (عناصر الفاتورة)</h3>
                    <button type="button" @click="addItem()" class="text-sm font-bold text-green-600 bg-green-50 px-3 py-1.5 rounded-lg hover:bg-green-100 transition">
                        <i class="fa-solid fa-plus ml-1"></i> إضافة خدمة
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex flex-wrap md:flex-nowrap items-start gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-200 relative">
                            
                            <button type="button" @click="removeItem(index)" class="absolute top-2 left-2 w-6 h-6 bg-red-100 hover:bg-red-200 text-red-600 rounded-full flex items-center justify-center text-xs transition">
                                <i class="fa-solid fa-xmark"></i>
                            </button>

                            <div class="w-full md:w-1/2">
                                <label class="block text-xs font-bold text-gray-500 mb-1">الخدمة</label>
                                <select :name="`items[${index}][service_id]`" x-model="item.service_id" @change="updateItemPrice(index)" required class="w-full px-3 py-2 bg-white border border-gray-200 focus:border-green-500 rounded-lg text-sm font-bold transition outline-none">
                                    <option value="">اختر الخدمة...</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" data-price="{{ $service->price }}">{{ $service->name }} ({{ number_format($service->price, 2) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="w-1/2 md:w-1/4">
                                <label class="block text-xs font-bold text-gray-500 mb-1">الكمية</label>
                                <input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity" min="1" required class="w-full px-3 py-2 bg-white border border-gray-200 focus:border-green-500 rounded-lg text-sm font-bold transition outline-none text-center">
                            </div>

                            <div class="w-1/2 md:w-1/4">
                                <label class="block text-xs font-bold text-gray-500 mb-1">السعر الفردي</label>
                                <input type="number" step="0.01" :name="`items[${index}][unit_price]`" x-model.number="item.unit_price" required class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm font-bold outline-none text-center" readonly>
                            </div>

                        </div>
                    </template>
                </div>
                
                <p x-show="items.length === 0" class="text-sm font-bold text-red-500 mt-4 text-center">يجب إضافة خدمة طبية واحدة على الأقل!</p>
            </div>
            
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">ملاحظات الفاتورة (تطبع للعميل)</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white rounded-xl text-sm font-bold transition outline-none placeholder-gray-400" placeholder="اكتب أي تعليمات أو ملاحظات حول هذه الفاتورة هنا..."></textarea>
            </div>
        </div>

        <!-- Left Column: Patient & Summary -->
        <div class="space-y-6">
            <!-- Patient Info -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-black text-gray-800 mb-4 border-b border-gray-50 pb-3">بيانات المراجع</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">المريض <span class="text-red-500">*</span></label>
                        <select name="patient_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white rounded-xl text-sm font-bold transition outline-none">
                            <option value="">اختر المريض...</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->name }} - {{ $patient->phone }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">الطبيب المعالج <span class="text-red-500">*</span></label>
                        <select name="doctor_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white rounded-xl text-sm font-bold transition outline-none">
                            <option value="">اختر الطبيب...</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}">د. {{ $doctor->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">تاريخ الإصدار <span class="text-red-500">*</span></label>
                        <input type="date" name="issue_date" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white rounded-xl text-sm font-bold transition outline-none">
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl shadow-lg border border-gray-700 p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-green-500 rounded-bl-full opacity-10"></div>
                
                <h3 class="text-lg font-black text-gray-100 mb-4">ملخص الفاتورة</h3>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-400 font-bold">المجموع الفرعي:</span>
                        <span class="font-mono font-bold" x-text="subTotal.toFixed(2)"></span>
                    </div>
                    
                    @php
                        $taxRate = $global_settings['tax_rate'] ?? 0;
                    @endphp
                    
                    @if($taxRate > 0)
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-400 font-bold">الضريبة ({{ $taxRate }}%):</span>
                        <span class="font-mono font-bold" x-text="taxAmount.toFixed(2)"></span>
                    </div>
                    @endif
                    
                    <div class="border-t border-gray-700 pt-3 flex justify-between items-end mt-2">
                        <span class="text-gray-300 font-bold">الإجمالي الشامل:</span>
                        <div class="text-right">
                            <span class="block text-2xl font-black text-green-400 font-mono" x-text="total.toFixed(2)"></span>
                            <span class="text-[10px] text-gray-400">{{ $global_settings['currency'] ?? 'SAR' }}</span>
                        </div>
                    </div>
                </div>
                
                <input type="hidden" name="sub_total" :value="subTotal">
                <input type="hidden" name="tax_amount" :value="taxAmount">
                <input type="hidden" name="total_amount" :value="total">

                <button type="submit" :disabled="items.length === 0" class="w-full bg-green-500 hover:bg-green-600 disabled:bg-gray-600 disabled:cursor-not-allowed text-white py-3.5 rounded-xl text-sm font-black shadow-lg shadow-green-500/30 transition flex items-center justify-center gap-2 relative z-10">
                    <i class="fa-solid fa-print"></i> حفظ وإصدار الفاتورة
                </button>
            </div>
        </div>
    </form>

</div>

<script>
function invoiceCreator() {
    return {
        taxRate: {{ $global_settings['tax_rate'] ?? 0 }},
        items: [
            { service_id: '', quantity: 1, unit_price: 0 }
        ],
        
        addItem() {
            this.items.push({ service_id: '', quantity: 1, unit_price: 0 });
        },
        
        removeItem(index) {
            this.items.splice(index, 1);
        },
        
        updateItemPrice(index) {
            const selectElement = document.querySelector(`select[name="items[${index}][service_id]"]`);
            if(selectElement && selectElement.selectedIndex > 0) {
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                this.items[index].unit_price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
            } else {
                this.items[index].unit_price = 0;
            }
        },
        
        get subTotal() {
            return this.items.reduce((sum, item) => {
                return sum + (parseFloat(item.unit_price) * parseInt(item.quantity || 1));
            }, 0);
        },
        
        get taxAmount() {
            return this.subTotal * (this.taxRate / 100);
        },
        
        get total() {
            return this.subTotal + this.taxAmount;
        }
    }
}
</script>
@endsection
