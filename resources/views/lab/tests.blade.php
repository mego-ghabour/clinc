@extends('layouts.app')

@section('title', 'دليل الفحوصات والتحاليل المخبرية - Clinc')
@section('header', 'دليل الفحوصات والأسعار والمعدلات المرجعية (Lab Tests Catalog)')

@section('content')
<div class="space-y-6" x-data="{ showAddModal: false, showEditModal: false, activeTest: {} }">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('lab-requests.index') }}" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition">
                <i class="fa-solid fa-arrow-right"></i>
            </a>
            <div>
                <h2 class="text-lg font-black text-gray-800">قائمة الفحوصات والتحاليل الطبية</h2>
                <p class="text-xs text-gray-500 font-bold mt-0.5">تحديد الأسعار، أنواع العينات، الوحدات والمعدلات الطبيعية المرجعية</p>
            </div>
        </div>

        <button @click="showAddModal = true" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-md shadow-teal-600/20 transition">
            <i class="fa-solid fa-plus"></i>
            إضافة فحص مخبري جديد
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <form method="GET" action="{{ route('lab-tests.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1">بحث باسم الفحص أو الكود</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="مثال: CBC, فحص السكر..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1">التصنيف</label>
                <select name="category" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                    <option value="all">جميع التصنيفات</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white py-2 rounded-xl text-xs font-bold transition">
                    <i class="fa-solid fa-filter ml-1"></i> بحث وتصفية
                </button>
                @if(request()->hasAny(['search', 'category']))
                    <a href="{{ route('lab-tests.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-bold transition">إلغاء</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tests Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-gray-50/80 text-gray-500 font-bold border-b border-gray-100">
                    <tr>
                        <th class="py-3 px-4">الكود</th>
                        <th class="py-3 px-4">اسم الفحص</th>
                        <th class="py-3 px-4">التصنيف</th>
                        <th class="py-3 px-4">نوع العينة</th>
                        <th class="py-3 px-4">المعدل الطبيعي</th>
                        <th class="py-3 px-4">الوحدة</th>
                        <th class="py-3 px-4">السعر</th>
                        <th class="py-3 px-4 text-center">الحالة</th>
                        <th class="py-3 px-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @forelse($tests as $test)
                    <tr class="hover:bg-gray-50/60 transition">
                        <td class="py-3.5 px-4 font-mono font-bold text-teal-700 whitespace-nowrap">
                            {{ $test->code }}
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-gray-800 block text-xs">{{ $test->name }}</span>
                            @if($test->description)
                                <span class="text-[10px] text-gray-400 truncate max-w-xs block">{{ $test->description }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 text-[10px] font-bold">
                                {{ $test->category }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-gray-600 whitespace-nowrap">
                            {{ $test->sample_type ?? '—' }}
                        </td>
                        <td class="py-3.5 px-4 font-mono text-gray-600 whitespace-nowrap">
                            {{ $test->normal_range ?? '—' }}
                        </td>
                        <td class="py-3.5 px-4 font-mono text-gray-600 whitespace-nowrap">
                            {{ $test->unit ?? '—' }}
                        </td>
                        <td class="py-3.5 px-4 font-mono font-bold text-gray-800 whitespace-nowrap">
                            {{ number_format($test->price, 2) }} ر.س
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $test->is_active ? 'bg-green-100 text-green-700' : 'bg-rose-100 text-rose-700' }}">
                                {{ $test->is_active ? 'مفعل' : 'معطل' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button" @click="activeTest = {{ json_encode($test) }}; showEditModal = true" class="p-1.5 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-700 transition" title="تعديل">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </button>
                                <form action="{{ route('lab-tests.destroy', $test->id) }}" method="POST" onsubmit="return confirm('تأكيد حذف هذا الفحص من الدليل؟')" class="inline">
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
                        <td colspan="9" class="text-center py-10 text-gray-400">لا توجد فحوصات مطابقة</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tests->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $tests->links() }}
        </div>
        @endif
    </div>

    <!-- Add Test Modal -->
    <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-900/50 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="showAddModal = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden relative transform transition-all">
            <div class="bg-teal-600 p-5 text-white flex justify-between items-center">
                <h3 class="font-black text-base"><i class="fa-solid fa-vial ml-2"></i> إضافة فحص مخبري جديد</h3>
                <button @click="showAddModal = false" class="text-white/70 hover:text-white transition"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('lab-tests.store') }}" method="POST" class="p-5 space-y-3.5">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">اسم الفحص <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" required placeholder="مثال: Glucose Fasting" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">كود الفحص <span class="text-rose-500">*</span></label>
                        <input type="text" name="code" required placeholder="مثال: FBG" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500 uppercase font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">التصنيف <span class="text-rose-500">*</span></label>
                        <input type="text" name="category" required placeholder="مثال: كيمياء الدم" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">السعر (ر.س) <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" min="0" name="price" required placeholder="0.00" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">نوع العينة</label>
                        <input type="text" name="sample_type" placeholder="مصل / دم وريدي" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">الوحدة</label>
                        <input type="text" name="unit" placeholder="mg/dL" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">المعدل الطبيعي</label>
                        <input type="text" name="normal_range" placeholder="70 - 100" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">وصف أو تعليمات خاصة</label>
                    <textarea name="description" rows="2" placeholder="شروط الصيام، تعليمات حفظ العينة..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-teal-500 resize-none"></textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" checked id="is_active_new" class="rounded text-teal-600 focus:ring-teal-500">
                    <label for="is_active_new" class="text-xs font-bold text-gray-700 cursor-pointer">تفعيل الفحص فوراً في النظام</label>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white py-2.5 rounded-xl text-xs font-bold transition">حفظ الفحص</button>
                    <button type="button" @click="showAddModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Test Modal -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-900/50 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="showEditModal = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden relative transform transition-all">
            <div class="bg-teal-700 p-5 text-white flex justify-between items-center">
                <h3 class="font-black text-base"><i class="fa-solid fa-pen-to-square ml-2"></i> تعديل بيانات الفحص المخبري</h3>
                <button @click="showEditModal = false" class="text-white/70 hover:text-white transition"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form :action="'{{ url('lab-tests') }}/' + activeTest.id" method="POST" class="p-5 space-y-3.5">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">اسم الفحص</label>
                        <input type="text" name="name" x-model="activeTest.name" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">كود الفحص</label>
                        <input type="text" name="code" x-model="activeTest.code" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500 uppercase font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">التصنيف</label>
                        <input type="text" name="category" x-model="activeTest.category" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">السعر (ر.س)</label>
                        <input type="number" step="0.01" min="0" name="price" x-model="activeTest.price" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">نوع العينة</label>
                        <input type="text" name="sample_type" x-model="activeTest.sample_type" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">الوحدة</label>
                        <input type="text" name="unit" x-model="activeTest.unit" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">المعدل الطبيعي</label>
                        <input type="text" name="normal_range" x-model="activeTest.normal_range" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">وصف أو تعليمات</label>
                    <textarea name="description" x-model="activeTest.description" rows="2" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-teal-500 resize-none"></textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" :checked="activeTest.is_active" id="is_active_edit" class="rounded text-teal-600 focus:ring-teal-500">
                    <label for="is_active_edit" class="text-xs font-bold text-gray-700 cursor-pointer">فحص مفعل ومتاح للأطباء</label>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white py-2.5 rounded-xl text-xs font-bold transition">حفظ التعديلات</button>
                    <button type="button" @click="showEditModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
