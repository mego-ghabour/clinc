@extends('layouts.app')

@section('title', 'قسم المختبر والتحاليل الطبية - Clinc')
@section('header', 'إدارة الفحوصات والتحاليل المخبرية (Laboratory)')

@section('content')
<div class="space-y-6" x-data="{ showNewModal: false }">

    <!-- Header & Quick Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <h2 class="text-xl font-black text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-flask-vial text-teal-600"></i>
                قسم المختبر والتحاليل الطبية
            </h2>
            <p class="text-xs text-gray-500 font-bold mt-1">متابعة طلبات التحاليل، سحب العينات، تسجيل النتائج وطباعة التقارير المخبرية</p>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <a href="{{ route('lab-tests.index') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-700 text-xs font-bold transition">
                <i class="fa-solid fa-list-check"></i>
                دليل الفحوصات والأسعار
            </a>
            <button @click="showNewModal = true" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-md shadow-teal-600/20 transition">
                <i class="fa-solid fa-plus"></i>
                طلب تحليل جديد
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-calendar-day"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500">طلبات اليوم</p>
                <h4 class="text-2xl font-black text-gray-800">{{ $stats['total_today'] }}</h4>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500">بانتظار العينة (معلق)</p>
                <h4 class="text-2xl font-black text-amber-600">{{ $stats['pending'] }}</h4>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-microscope"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500">قيد التحليل المخبري</p>
                <h4 class="text-2xl font-black text-purple-600">{{ $stats['in_progress'] }}</h4>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-check-double"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500">اكتملت اليوم</p>
                <h4 class="text-2xl font-black text-green-600">{{ $stats['completed_today'] }}</h4>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <form method="GET" action="{{ route('lab-requests.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1">بحث برقم الطلب / المريض / MRN</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم الطلب أو اسم المريض..." class="w-full pl-3 pr-9 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                    <i class="fa-solid fa-magnifying-glass absolute right-3 top-2.5 text-gray-400 text-xs"></i>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1">حالة الطلب</label>
                <select name="status" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>جميع الحالات</option>
                    <option value="معلق" {{ request('status') == 'معلق' ? 'selected' : '' }}>معلق (بانتظار العينة)</option>
                    <option value="تم سحب العينة" {{ request('status') == 'تم سحب العينة' ? 'selected' : '' }}>تم سحب العينة</option>
                    <option value="قيد التحليل" {{ request('status') == 'قيد التحليل' ? 'selected' : '' }}>قيد التحليل</option>
                    <option value="مكتمل" {{ request('status') == 'مكتمل' ? 'selected' : '' }}>مكتمل (النتائج جاهزة)</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1">الأولوية</label>
                <select name="priority" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500">
                    <option value="all" {{ request('priority') == 'all' ? 'selected' : '' }}>جميع الأولويات</option>
                    <option value="عادي" {{ request('priority') == 'عادي' ? 'selected' : '' }}>عادي</option>
                    <option value="عاجل" {{ request('priority') == 'عاجل' ? 'selected' : '' }}>عاجل (Urgent)</option>
                    <option value="حرج" {{ request('priority') == 'حرج' ? 'selected' : '' }}>حرج (Critical / STAT)</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white py-2 rounded-xl text-xs font-bold transition">
                    <i class="fa-solid fa-filter ml-1"></i> تصفية
                </button>
                @if(request()->hasAny(['search', 'status', 'priority']))
                    <a href="{{ route('lab-requests.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-bold transition">
                        إلغاء
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table of Requests -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-gray-50/80 text-gray-500 font-bold border-b border-gray-100">
                    <tr>
                        <th class="py-3 px-4">رقم الطلب</th>
                        <th class="py-3 px-4">المريض</th>
                        <th class="py-3 px-4">الطبيب المحول</th>
                        <th class="py-3 px-4">الفحوصات المطلوبة</th>
                        <th class="py-3 px-4">الأولوية</th>
                        <th class="py-3 px-4">الحالة</th>
                        <th class="py-3 px-4">تاريخ الطلب</th>
                        <th class="py-3 px-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @forelse($labRequests as $req)
                    <tr class="hover:bg-gray-50/60 transition">
                        <td class="py-3.5 px-4 font-mono font-bold text-gray-800 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-teal-50 text-teal-700 border border-teal-100">
                                <i class="fa-solid fa-barcode text-[10px]"></i>
                                {{ $req->request_number }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-teal-100/50 text-teal-700 font-bold flex items-center justify-center shrink-0">
                                    {{ mb_substr($req->patient->name ?? 'م', 0, 1) }}
                                </div>
                                <div>
                                    <a href="{{ route('patients.show', $req->patient_id) }}" class="font-bold text-gray-800 hover:text-teal-600">
                                        {{ $req->patient->name ?? '—' }}
                                    </a>
                                    <p class="text-[10px] text-gray-400 font-mono">MRN: {{ $req->patient->mrn ?? '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-gray-700">{{ $req->doctor->name ?? 'مختبر مباشر' }}</span>
                            @if($req->visit_id)
                                <a href="{{ route('visits.show', $req->visit_id) }}" class="block text-[10px] text-teal-600 hover:underline">زيارة #{{ $req->visit_id }}</a>
                            @endif
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex flex-wrap gap-1 max-w-xs">
                                @foreach($req->items->take(3) as $item)
                                    <span class="inline-block px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 text-[10px] font-bold">
                                        {{ $item->test_name }}
                                        @if($item->is_abnormal)
                                            <i class="fa-solid fa-triangle-exclamation text-rose-500 mr-0.5"></i>
                                        @endif
                                    </span>
                                @endforeach
                                @if($req->items->count() > 3)
                                    <span class="inline-block px-1.5 py-0.5 rounded-md bg-teal-50 text-teal-600 text-[10px] font-bold">
                                        +{{ $req->items->count() - 3 }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            @if($req->priority === 'حرج')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-black bg-rose-100 text-rose-700 animate-pulse">
                                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i> حرج (STAT)
                                </span>
                            @elseif($req->priority === 'عاجل')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-700">
                                    <i class="fa-solid fa-bolt text-[10px]"></i> عاجل
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-gray-100 text-gray-700">
                                    عادي
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            @php
                                $statusStyles = [
                                    'معلق' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'تم سحب العينة' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'قيد التحليل' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    'مكتمل' => 'bg-green-50 text-green-700 border-green-200',
                                    'ملغي' => 'bg-gray-100 text-gray-500 border-gray-200',
                                ];
                            @endphp
                            <span class="inline-block px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $statusStyles[$req->status] ?? 'bg-gray-50 text-gray-700' }}">
                                {{ $req->status }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-gray-500 font-mono text-[11px]">
                            {{ $req->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('lab-requests.show', $req->id) }}" class="p-1.5 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-700 transition" title="عرض وتعديل النتائج">
                                    <i class="fa-solid fa-flask text-sm"></i>
                                </a>
                                @if($req->status === 'مكتمل')
                                <a href="{{ route('lab-requests.print', $req->id) }}" target="_blank" class="p-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 transition" title="طباعة التقرير">
                                    <i class="fa-solid fa-print text-sm"></i>
                                </a>
                                @endif
                                <form action="{{ route('lab-requests.destroy', $req->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف طلب التحليل هذا؟')" class="inline">
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
                            <i class="fa-solid fa-vial-circle-check text-4xl mb-3 text-gray-300"></i>
                            <p class="font-bold text-sm">لا توجد طلبات تحاليل مخبرية مطابقة لمعايير البحث</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($labRequests->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $labRequests->links() }}
        </div>
        @endif
    </div>

    <!-- New Lab Request Modal -->
    <div x-show="showNewModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-900/50 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="showNewModal = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden relative transform transition-all max-h-[90vh] flex flex-col">
            <div class="bg-teal-600 p-5 text-white flex justify-between items-center shrink-0">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-flask text-lg"></i>
                    <h3 class="font-black text-base">إصدار طلب تحليل مخبري جديد</h3>
                </div>
                <button @click="showNewModal = false" class="text-white/70 hover:text-white transition">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('lab-requests.store') }}" method="POST" class="p-6 overflow-y-auto space-y-4 flex-1">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Patient Select -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">المريض <span class="text-rose-500">*</span></label>
                        <select name="patient_id" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 focus:border-teal-500 rounded-xl text-xs font-bold">
                            <option value="">-- اختر المريض --</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->name }} (MRN: {{ $patient->mrn }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Doctor Select -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">الطبيب المحول</label>
                        <select name="doctor_id" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 focus:border-teal-500 rounded-xl text-xs font-bold">
                            <option value="">-- بدون طبيب محدد (طلب مباشر) --</option>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}" {{ auth()->id() == $doc->id ? 'selected' : '' }}>د. {{ $doc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Priority -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">أولوية الفحص <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="cursor-pointer border border-gray-200 rounded-xl p-2.5 text-center hover:bg-gray-50 flex items-center justify-center gap-2">
                            <input type="radio" name="priority" value="عادي" checked class="text-teal-600 focus:ring-teal-500">
                            <span class="text-xs font-bold text-gray-700">عادي</span>
                        </label>
                        <label class="cursor-pointer border border-amber-200 rounded-xl p-2.5 text-center bg-amber-50/30 hover:bg-amber-50 flex items-center justify-center gap-2">
                            <input type="radio" name="priority" value="عاجل" class="text-amber-600 focus:ring-amber-500">
                            <span class="text-xs font-bold text-amber-800">عاجل</span>
                        </label>
                        <label class="cursor-pointer border border-rose-200 rounded-xl p-2.5 text-center bg-rose-50/30 hover:bg-rose-50 flex items-center justify-center gap-2">
                            <input type="radio" name="priority" value="حرج" class="text-rose-600 focus:ring-rose-500">
                            <span class="text-xs font-bold text-rose-800">حرج (STAT)</span>
                        </label>
                    </div>
                </div>

                <!-- Available Tests (Checkboxes grouped by category) -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">الفحوصات المطلوبة <span class="text-rose-500">*</span></label>
                    <div class="border border-gray-200 rounded-2xl p-3 max-h-52 overflow-y-auto space-y-3 bg-gray-50/50">
                        @php
                            $groupedTests = $availableTests->groupBy('category');
                        @endphp
                        @foreach($groupedTests as $category => $tests)
                            <div>
                                <h5 class="text-[11px] font-black text-teal-700 border-b border-gray-200 pb-1 mb-2">{{ $category }}</h5>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($tests as $test)
                                        <label class="flex items-center gap-2 bg-white p-2 rounded-xl border border-gray-100 hover:border-teal-300 cursor-pointer transition">
                                            <input type="checkbox" name="test_ids[]" value="{{ $test->id }}" class="rounded text-teal-600 focus:ring-teal-500">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-bold text-gray-800 truncate">{{ $test->name }}</p>
                                                <div class="flex items-center justify-between text-[10px] text-gray-400">
                                                    <span>{{ $test->code }}</span>
                                                    <span class="font-mono text-teal-600 font-bold">{{ number_format($test->price, 2) }} ر.س</span>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Clinical Notes -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">ملاحظات سريرية لفني المختبر</label>
                    <textarea name="clinical_notes" rows="2" placeholder="أي ملاحظات سريرية أو أدوية يتناولها المريض..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 focus:border-teal-500 rounded-xl text-xs resize-none"></textarea>
                </div>

                <div class="flex items-center gap-3 pt-3 border-t border-gray-100">
                    <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white py-2.5 rounded-xl text-xs font-bold shadow-md shadow-teal-600/20 transition">
                        تأكيد وإصدار الطلب
                    </button>
                    <button type="button" @click="showNewModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
