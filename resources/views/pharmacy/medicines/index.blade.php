@extends('layouts.app')

@section('title', 'دليل الأدوية والمخزون الصيدلي - Clinc')
@section('header', 'إدارة الصيدلية ومخزون الأدوية (Pharmacy & Inventory)')

@section('content')
<div class="space-y-6" x-data="{ showAddModal: false, showEditModal: false, showAdjustModal: false, activeMed: {} }">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <h2 class="text-xl font-black text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-pills text-teal-600"></i>
                صيدلية العيادة والمستودع الدوائي
            </h2>
            <p class="text-xs text-gray-500 font-bold mt-1">تتبع الأرصدة، تنبيهات نفاد المخزون وتواريخ الصلاحية وحركات الصرف</p>
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <a href="{{ route('prescriptions.index') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-700 text-xs font-bold transition">
                <i class="fa-solid fa-receipt"></i>
                شاشة صرف الوصفات
            </a>
            <button @click="showAddModal = true" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-md shadow-teal-600/20 transition">
                <i class="fa-solid fa-plus"></i>
                إضافة دواء جديد
            </button>
        </div>
    </div>

    <!-- Inventory Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-capsules"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500">إجمالي الأدوية المسجلة</p>
                <h4 class="text-2xl font-black text-gray-800">{{ $stats['total_medicines'] }}</h4>
            </div>
        </div>

        <a href="{{ route('medicines.index', ['stock_status' => 'low_stock']) }}" class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4 hover:border-amber-300 transition">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500">أدوية قاربت على النفاد</p>
                <h4 class="text-2xl font-black text-amber-600">{{ $stats['low_stock'] }}</h4>
            </div>
        </a>

        <a href="{{ route('medicines.index', ['stock_status' => 'expiring_soon']) }}" class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4 hover:border-rose-300 transition">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-calendar-xmark"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500">قريبة الانتهاء (60 يوم)</p>
                <h4 class="text-2xl font-black text-rose-600">{{ $stats['expiring_soon'] }}</h4>
            </div>
        </a>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500">القيمة التقديرية للمخزون</p>
                <h4 class="text-xl font-black text-emerald-600 font-mono">{{ number_format($stats['inventory_value'], 2) }} <span class="text-xs font-sans">ر.س</span></h4>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <form method="GET" action="{{ route('medicines.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1">بحث بالاسم / الكود / الاسم العلمي</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم الدواء أو الباركود..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1">التصنيف الدوائي</label>
                <select name="category" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                    <option value="all">جميع التصنيفات</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1">حالة المخزون</label>
                <select name="stock_status" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                    <option value="">جميع الأدوية</option>
                    <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>أوشكت على النفاد (Low Stock)</option>
                    <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>نفدت من المخزن (Out of Stock)</option>
                    <option value="expiring_soon" {{ request('stock_status') == 'expiring_soon' ? 'selected' : '' }}>قريبة الانتهاء (Expiring Soon)</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white py-2 rounded-xl text-xs font-bold transition">
                    <i class="fa-solid fa-filter ml-1"></i> تصفية
                </button>
                @if(request()->hasAny(['search', 'category', 'stock_status']))
                    <a href="{{ route('medicines.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-bold transition">إلغاء</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Medicines Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-gray-50/80 text-gray-500 font-bold border-b border-gray-100">
                    <tr>
                        <th class="py-3 px-4">الكود</th>
                        <th class="py-3 px-4">الدواء (الاسم التجاري والعلمي)</th>
                        <th class="py-3 px-4">الشكل والتركيز</th>
                        <th class="py-3 px-4">التصنيف</th>
                        <th class="py-3 px-4">الكمية المتوفرة</th>
                        <th class="py-3 px-4">سعر البيع</th>
                        <th class="py-3 px-4">الصلاحية</th>
                        <th class="py-3 px-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @forelse($medicines as $med)
                    <tr class="hover:bg-gray-50/60 transition {{ $med->stock_quantity <= $med->reorder_level ? 'bg-amber-50/30' : '' }}">
                        <td class="py-3.5 px-4 font-mono font-bold text-teal-700 whitespace-nowrap">
                            {{ $med->code }}
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-gray-800 block text-xs">{{ $med->name }}</span>
                            @if($med->generic_name)
                                <span class="text-[10px] text-gray-400 font-mono">{{ $med->generic_name }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="text-gray-700 font-bold">{{ $med->dosage_form }}</span>
                            @if($med->strength)
                                <span class="text-[10px] text-gray-400 font-mono block">{{ $med->strength }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 text-[10px] font-bold">
                                {{ $med->category }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            @if($med->stock_quantity <= 0)
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-black bg-rose-100 text-rose-700">
                                    نفد (0)
                                </span>
                            @elseif($med->stock_quantity <= $med->reorder_level)
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-100 text-amber-800 font-mono" title="الحد الأدنى: {{ $med->reorder_level }}">
                                    <i class="fa-solid fa-triangle-exclamation text-[10px] ml-0.5"></i> {{ $med->stock_quantity }}
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-green-50 text-green-700 font-mono">
                                    {{ $med->stock_quantity }}
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 font-mono font-bold text-gray-800 whitespace-nowrap">
                            {{ number_format($med->unit_price, 2) }} ر.س
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap font-mono text-[11px]">
                            @if($med->expiry_date)
                                @php
                                    $isExpiring = $med->expiry_date->isPast() || $med->expiry_date->diffInDays(now()) <= 60;
                                @endphp
                                <span class="{{ $isExpiring ? 'text-rose-600 font-bold' : 'text-gray-600' }}">
                                    {{ $med->expiry_date->format('Y-m-d') }}
                                    @if($isExpiring)
                                        <i class="fa-solid fa-clock-rotate-left mr-1 text-[10px]"></i>
                                    @endif
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button" @click="activeMed = {{ json_encode($med) }}; showAdjustModal = true" class="p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 transition" title="تعديل المخزون / إضافة رصيد">
                                    <i class="fa-solid fa-boxes-stacked text-sm"></i>
                                </button>
                                <button type="button" @click="activeMed = {{ json_encode($med) }}; showEditModal = true" class="p-1.5 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-700 transition" title="تعديل البيانات">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </button>
                                <form action="{{ route('medicines.destroy', $med->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدواء؟')" class="inline">
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
                        <td colspan="8" class="text-center py-12 text-gray-400">
                            <i class="fa-solid fa-prescription-bottle-medical text-4xl mb-3 text-gray-300"></i>
                            <p class="font-bold text-sm">لا توجد أدوية مطابقة لمعايير البحث</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($medicines->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $medicines->links() }}
        </div>
        @endif
    </div>

    <!-- Add Medicine Modal -->
    <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-900/50 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="showAddModal = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-xl overflow-hidden relative transform transition-all max-h-[90vh] flex flex-col">
            <div class="bg-teal-600 p-5 text-white flex justify-between items-center shrink-0">
                <h3 class="font-black text-base flex items-center gap-2"><i class="fa-solid fa-pills"></i> إضافة دواء جديد للمستودع</h3>
                <button @click="showAddModal = false" class="text-white/70 hover:text-white transition"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form action="{{ route('medicines.store') }}" method="POST" class="p-6 overflow-y-auto space-y-4 flex-1">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">اسم الدواء التجاري <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" required placeholder="مثال: Panadol Extra" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">كود الدواء / الباركود <span class="text-rose-500">*</span></label>
                        <input type="text" name="code" required placeholder="مثال: MED-PAN-01" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500 uppercase font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">الاسم العلمي (Generic Name)</label>
                        <input type="text" name="generic_name" placeholder="مثال: Paracetamol" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">التصنيف الدوائي <span class="text-rose-500">*</span></label>
                        <input type="text" name="category" required placeholder="مسكنات، مضادات حيوية..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">شكل الجرعة <span class="text-rose-500">*</span></label>
                        <select name="dosage_form" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                            <option value="أقراص">أقراص (Tablets)</option>
                            <option value="كبسولات">كبسولات (Capsules)</option>
                            <option value="شراب">شراب (Syrup)</option>
                            <option value="حقن">حقن (Injection)</option>
                            <option value="مرهم/جل">مرهم / جل (Ointment)</option>
                            <option value="بخاخ">بخاخ (Inhaler/Spray)</option>
                            <option value="قطرات">قطرات (Drops)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">التركيز</label>
                        <input type="text" name="strength" placeholder="500mg, 10ml" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-teal-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">سعر البيع (ر.س) <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" min="0" name="unit_price" required placeholder="0.00" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">سعر التكلفة (الشراء)</label>
                        <input type="number" step="0.01" min="0" name="cost_price" placeholder="0.00" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-teal-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">الكمية الافتتاحية <span class="text-rose-500">*</span></label>
                        <input type="number" min="0" name="stock_quantity" value="0" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">حد إعادة الطلب (تنبيه)</label>
                        <input type="number" min="1" name="reorder_level" value="10" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">تاريخ الصلاحية</label>
                        <input type="date" name="expiry_date" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-teal-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">رقم التشغيلة (Batch Number)</label>
                        <input type="text" name="batch_number" placeholder="BCH-..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-teal-500 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">وصف أو تعليمات خاصة</label>
                    <textarea name="description" rows="2" placeholder="دواعي الاستعمال أو شروط الحفظ..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-teal-500 resize-none"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                    <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white py-2.5 rounded-xl text-xs font-bold transition">حفظ الدواء</button>
                    <button type="button" @click="showAddModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Medicine Modal -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-900/50 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="showEditModal = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-xl overflow-hidden relative transform transition-all max-h-[90vh] flex flex-col">
            <div class="bg-teal-700 p-5 text-white flex justify-between items-center shrink-0">
                <h3 class="font-black text-base flex items-center gap-2"><i class="fa-solid fa-pen-to-square"></i> تعديل بيانات الدواء</h3>
                <button @click="showEditModal = false" class="text-white/70 hover:text-white transition"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form :action="'{{ url('medicines') }}/' + activeMed.id" method="POST" class="p-6 overflow-y-auto space-y-4 flex-1">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">اسم الدواء التجاري</label>
                        <input type="text" name="name" x-model="activeMed.name" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">كود الدواء</label>
                        <input type="text" name="code" x-model="activeMed.code" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500 uppercase font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">الاسم العلمي</label>
                        <input type="text" name="generic_name" x-model="activeMed.generic_name" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">التصنيف الدوائي</label>
                        <input type="text" name="category" x-model="activeMed.category" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">شكل الجرعة</label>
                        <input type="text" name="dosage_form" x-model="activeMed.dosage_form" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">التركيز</label>
                        <input type="text" name="strength" x-model="activeMed.strength" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-teal-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">سعر البيع (ر.س)</label>
                        <input type="number" step="0.01" min="0" name="unit_price" x-model="activeMed.unit_price" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">سعر التكلفة</label>
                        <input type="number" step="0.01" min="0" name="cost_price" x-model="activeMed.cost_price" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-teal-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">حد إعادة الطلب</label>
                        <input type="number" min="1" name="reorder_level" x-model="activeMed.reorder_level" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">تاريخ الصلاحية</label>
                        <input type="date" name="expiry_date" x-model="activeMed.expiry_date" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-teal-500 font-mono">
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" :checked="activeMed.is_active" id="is_active_med" class="rounded text-teal-600 focus:ring-teal-500">
                    <label for="is_active_med" class="text-xs font-bold text-gray-700 cursor-pointer">دواء مفعل ومتاح للأطباء</label>
                </div>

                <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                    <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white py-2.5 rounded-xl text-xs font-bold transition">حفظ التعديلات</button>
                    <button type="button" @click="showEditModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Stock Adjustment Modal -->
    <div x-show="showAdjustModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-900/50 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="showAdjustModal = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden relative transform transition-all">
            <div class="bg-emerald-600 p-5 text-white flex justify-between items-center">
                <div>
                    <h3 class="font-black text-base flex items-center gap-2"><i class="fa-solid fa-boxes-stacked"></i> حركة مخزون دواء</h3>
                    <p class="text-xs text-emerald-100 font-bold mt-0.5" x-text="activeMed.name"></p>
                </div>
                <button @click="showAdjustModal = false" class="text-white/70 hover:text-white transition"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form :action="'{{ url('medicines') }}/' + activeMed.id + '/adjust-stock'" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="p-3 rounded-xl bg-gray-50 border border-gray-100 flex justify-between items-center text-xs">
                    <span class="text-gray-500 font-bold">الرصيد الحالي بالمستودع:</span>
                    <span class="font-black text-gray-800 font-mono text-sm" x-text="activeMed.stock_quantity"></span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">نوع العملية <span class="text-rose-500">*</span></label>
                    <select name="type" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-emerald-500">
                        <option value="إضافة رصيد">إضافة رصيد / شحنة جديدة (+)</option>
                        <option value="تسوية جردية">تسوية جردية (+ أو -)</option>
                        <option value="إتلاف/منتهي">إتلاف أو منتهي الصلاحية (-)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">الكمية <span class="text-rose-500">*</span></label>
                    <input type="number" name="quantity" required placeholder="مثال: 50" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-emerald-500 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">البيان أو الملاحظات</label>
                    <input type="text" name="notes" placeholder="رقم فاتورة الشراء أو سبب التسوية..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-emerald-500">
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 rounded-xl text-xs font-bold transition">تأكيد حركة المخزون</button>
                    <button type="button" @click="showAdjustModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
