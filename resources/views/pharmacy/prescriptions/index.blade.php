@extends('layouts.app')

@section('title', 'الوصفات الطبية وصرف الأدوية - Clinc')
@section('header', 'إدارة الوصفات وصرف الأدوية (Prescriptions & Dispensing)')

@section('content')
<div class="space-y-6" x-data="{ showNewModal: false }">

    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <h2 class="text-xl font-black text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-prescription text-teal-600"></i>
                قائمة الوصفات وصرف الأدوية
            </h2>
            <p class="text-xs text-gray-500 font-bold mt-1">متابعة الوصفات الطبية الصادرة من العيادات، تدقيق الجرعات، وصرف الأدوية للمرضى</p>
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <a href="{{ route('medicines.index') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-700 text-xs font-bold transition">
                <i class="fa-solid fa-boxes-stacked"></i>
                دليل ومستودع الأدوية
            </a>
            <button @click="showNewModal = true" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-md shadow-teal-600/20 transition">
                <i class="fa-solid fa-plus"></i>
                إصدار وصفة جديدة
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-calendar-day"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500">وصفات اليوم الإجمالية</p>
                <h4 class="text-2xl font-black text-gray-800">{{ $stats['total_today'] }}</h4>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500">بانتظار الصرف (معلقة)</p>
                <h4 class="text-2xl font-black text-amber-600">{{ $stats['pending'] }}</h4>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-check-double"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500">صُرفت اليوم</p>
                <h4 class="text-2xl font-black text-green-600">{{ $stats['dispensed_today'] }}</h4>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <form method="GET" action="{{ route('prescriptions.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1">بحث برقم الوصفة أو المريض</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم الوصفة أو اسم المريض..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1">حالة الوصفة</label>
                <select name="status" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                    <option value="all">جميع الحالات</option>
                    <option value="معلقة" {{ request('status') == 'معلقة' ? 'selected' : '' }}>معلقة (بانتظار الصرف)</option>
                    <option value="مصروفة" {{ request('status') == 'مصروفة' ? 'selected' : '' }}>مصروفة بالكامل</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white py-2 rounded-xl text-xs font-bold transition">
                    <i class="fa-solid fa-filter ml-1"></i> تصفية
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('prescriptions.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-bold transition">إلغاء</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Prescriptions Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-gray-50/80 text-gray-500 font-bold border-b border-gray-100">
                    <tr>
                        <th class="py-3 px-4">رقم الوصفة</th>
                        <th class="py-3 px-4">المريض</th>
                        <th class="py-3 px-4">الطبيب المعالج</th>
                        <th class="py-3 px-4">الأدوية الموصوفة</th>
                        <th class="py-3 px-4">الحالة</th>
                        <th class="py-3 px-4">تاريخ الإصدار</th>
                        <th class="py-3 px-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @forelse($prescriptions as $rx)
                    <tr class="hover:bg-gray-50/60 transition">
                        <td class="py-3.5 px-4 font-mono font-bold text-gray-800 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-teal-50 text-teal-700 border border-teal-100">
                                <i class="fa-solid fa-prescription text-[11px]"></i>
                                {{ $rx->prescription_number }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4">
                            <a href="{{ route('patients.show', $rx->patient_id) }}" class="font-bold text-gray-800 hover:text-teal-600 block">
                                {{ $rx->patient->name ?? '—' }}
                            </a>
                            <span class="text-[10px] text-gray-400 font-mono">MRN: {{ $rx->patient->mrn ?? '—' }}</span>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-gray-700">د. {{ $rx->doctor->name ?? 'طبيب عام' }}</span>
                            @if($rx->visit_id)
                                <a href="{{ route('visits.show', $rx->visit_id) }}" class="block text-[10px] text-teal-600 hover:underline">زيارة #{{ $rx->visit_id }}</a>
                            @endif
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex flex-wrap gap-1 max-w-xs">
                                @foreach($rx->items->take(3) as $item)
                                    <span class="inline-block px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 text-[10px] font-bold">
                                        {{ $item->medicine_name }}
                                    </span>
                                @endforeach
                                @if($rx->items->count() > 3)
                                    <span class="inline-block px-1.5 py-0.5 rounded-md bg-teal-50 text-teal-600 text-[10px] font-bold">
                                        +{{ $rx->items->count() - 3 }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            @if($rx->status === 'مصروفة')
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-green-50 text-green-700 border border-green-200">
                                    <i class="fa-solid fa-check ml-0.5"></i> مصروفة
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                    <i class="fa-solid fa-clock ml-0.5"></i> بانتظار الصرف
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap font-mono text-gray-500 text-[11px]">
                            {{ $rx->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('prescriptions.show', $rx->id) }}" class="p-1.5 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-700 transition" title="فحص وصرف الوصفة">
                                    <i class="fa-solid fa-hand-holding-medical text-sm"></i>
                                </a>
                                <a href="{{ route('prescriptions.print', $rx->id) }}" target="_blank" class="p-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 transition" title="طباعة الوصفة (Rx)">
                                    <i class="fa-solid fa-print text-sm"></i>
                                </a>
                                <form action="{{ route('prescriptions.destroy', $rx->id) }}" method="POST" onsubmit="return confirm('هل تريد حذف هذه الوصفة؟')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition" title="حذف">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-12 text-gray-400">
                            <i class="fa-solid fa-prescription text-4xl mb-3 text-gray-300"></i>
                            <p class="font-bold text-sm">لا توجد وصفات طبية مطابقة لمعايير البحث</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($prescriptions->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $prescriptions->links() }}
        </div>
        @endif
    </div>

    <!-- New Prescription Modal -->
    <div x-show="showNewModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-900/50 backdrop-blur-sm p-4" style="display: none;"
         x-data="{
             rows: [{ medicine_id: '', medicine_name: '', dosage: '', frequency: '', duration: '', quantity: 1 }],
             addRow() {
                 this.rows.push({ medicine_id: '', medicine_name: '', dosage: '', frequency: '', duration: '', quantity: 1 });
             },
             removeRow(index) {
                 if (this.rows.length > 1) {
                     this.rows.splice(index, 1);
                 }
             },
             updateName(index, event) {
                 let select = event.target;
                 let text = select.options[select.selectedIndex].text;
                 this.rows[index].medicine_name = text;
             }
         }">
        <div @click.away="showNewModal = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden relative transform transition-all max-h-[90vh] flex flex-col">
            <div class="bg-teal-600 p-5 text-white flex justify-between items-center shrink-0">
                <h3 class="font-black text-base flex items-center gap-2"><i class="fa-solid fa-prescription"></i> إصدار وصفة طبية جديدة</h3>
                <button @click="showNewModal = false" class="text-white/70 hover:text-white transition"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="{{ route('prescriptions.store') }}" method="POST" class="p-6 overflow-y-auto space-y-4 flex-1">
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
                        <label class="block text-xs font-bold text-gray-700 mb-1">الطبيب المعالج</label>
                        <select name="doctor_id" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                            <option value="">-- طبيب العيادة --</option>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}" {{ auth()->id() == $doc->id ? 'selected' : '' }}>د. {{ $doc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Prescription Items -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-xs font-bold text-gray-700">الأدوية الموصوفة والجرعات <span class="text-rose-500">*</span></label>
                        <button type="button" @click="addRow()" class="text-xs font-bold text-teal-600 hover:text-teal-700 flex items-center gap-1">
                            <i class="fa-solid fa-plus-circle"></i> إضافة دواء آخر
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(row, index) in rows" :key="index">
                            <div class="p-3 rounded-2xl bg-gray-50 border border-gray-200 space-y-2 relative">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-teal-700" x-text="'دواء #' + (index + 1)"></span>
                                    <button type="button" @click="removeRow(index)" x-show="rows.length > 1" class="text-rose-500 hover:text-rose-700 text-xs">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <div>
                                        <select :name="'items[' + index + '][medicine_id]'" @change="updateName(index, $event)" class="w-full px-2.5 py-1.5 bg-white border border-gray-200 rounded-xl text-xs font-bold">
                                            <option value="">-- اختر من أدوية الصيدلية --</option>
                                            @foreach($availableMedicines as $med)
                                                <option value="{{ $med->id }}">{{ $med->name }} ({{ $med->strength ?? $med->dosage_form }}) - متوفر: {{ $med->stock_quantity }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <input type="text" :name="'items[' + index + '][medicine_name]'" x-model="row.medicine_name" required placeholder="أو اكتب اسم الدواء يدوياً..." class="w-full px-2.5 py-1.5 bg-white border border-gray-200 rounded-xl text-xs font-bold">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    <div>
                                        <input type="text" :name="'items[' + index + '][dosage]'" x-model="row.dosage" placeholder="الجرعة (قرص واحد)" class="w-full px-2 py-1.5 bg-white border border-gray-200 rounded-lg text-xs">
                                    </div>
                                    <div>
                                        <input type="text" :name="'items[' + index + '][frequency]'" x-model="row.frequency" placeholder="التكرار (كل 8 ساعات)" class="w-full px-2 py-1.5 bg-white border border-gray-200 rounded-lg text-xs">
                                    </div>
                                    <div>
                                        <input type="text" :name="'items[' + index + '][duration]'" x-model="row.duration" placeholder="المدة (5 أيام)" class="w-full px-2 py-1.5 bg-white border border-gray-200 rounded-lg text-xs">
                                    </div>
                                    <div>
                                        <input type="number" min="1" :name="'items[' + index + '][quantity]'" x-model="row.quantity" placeholder="الكمية" class="w-full px-2 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-mono font-bold">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">تعليمات أو ملاحظات عامة للمريض</label>
                    <textarea name="notes" rows="2" placeholder="تناول الدواء بعد الأكل، تجنب القيادة..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-teal-500 resize-none"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                    <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white py-2.5 rounded-xl text-xs font-bold transition">إصدار الوصفة الطبية</button>
                    <button type="button" @click="showNewModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
