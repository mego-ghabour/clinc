@extends('layouts.app')

@section('title', 'المواعيد - فكرتك الرقمية')

@section('content')
<div x-data="{ isModalOpen: false }">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-teal-custom mb-1">المواعيد</h1>
            <p class="text-gray-500 text-sm">حجوزات تتجاوز نسبة 0% بصلاحية خاصة</p>
        </div>
        <button @click="isModalOpen = true" class="bg-teal-custom hover:bg-teal-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> موعد جديد
        </button>
    </div>

    <!-- Search Form -->
    <form action="{{ route('appointments.index') }}" method="GET" class="flex flex-wrap md:flex-nowrap gap-3 mb-6 bg-white p-3 rounded-xl shadow-sm border border-gray-100">
        
        <div class="flex-1 min-w-[200px] relative">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالمريض أو رقم الموعد..." class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-sm text-right">
        </div>

        <div class="w-full md:w-auto relative">
            <label class="absolute -top-2 right-3 bg-white px-1 text-[10px] text-gray-400">الحالة</label>
            <select name="status" class="w-full md:w-32 px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-sm bg-white">
                <option value="">كل الحالات</option>
                <option value="مؤكد" {{ request('status') == 'مؤكد' ? 'selected' : '' }}>مؤكد</option>
                <option value="مكتمل" {{ request('status') == 'مكتمل' ? 'selected' : '' }}>مكتمل</option>
                <option value="في الانتظار" {{ request('status') == 'في الانتظار' ? 'selected' : '' }}>في الانتظار</option>
                <option value="ملغي" {{ request('status') == 'ملغي' ? 'selected' : '' }}>ملغي</option>
            </select>
        </div>

        <div class="w-full md:w-auto relative">
            <label class="absolute -top-2 right-3 bg-white px-1 text-[10px] text-gray-400">الطبيب</label>
            <select name="doctor_id" class="w-full md:w-40 px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-sm bg-white">
                <option value="">كل الأطباء</option>
                @foreach($doctors as $doc)
                    <option value="{{ $doc->id }}" {{ request('doctor_id') == $doc->id ? 'selected' : '' }}>{{ $doc->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="w-full md:w-auto relative">
            <label class="absolute -top-2 right-3 bg-white px-1 text-[10px] text-gray-400">التاريخ</label>
            <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}" class="w-full md:w-40 px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-sm text-right">
        </div>

        <div class="flex gap-2">
            <a href="{{ route('appointments.index') }}" class="bg-gray-50 border border-gray-200 text-gray-600 hover:bg-gray-100 px-4 py-2 rounded-lg text-sm font-medium transition flex items-center justify-center">
                إلغاء
            </a>
            <button type="submit" class="bg-teal-custom hover:bg-teal-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition flex items-center justify-center">
                بحث
            </button>
        </div>
    </form>

    <!-- Empty State OR Table -->
    @if($appointments->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-16 text-center mt-6">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 mx-auto mb-4 text-2xl">
                <i class="fa-solid fa-calendar-xmark"></i>
            </div>
            <h3 class="text-xl font-bold text-teal-custom mb-2">لا مواعيد في هذا اليوم</h3>
            <p class="text-gray-500 text-sm">غيّر التاريخ أو الطبيب، أو أضف موعداً جديداً من الزر أعلى الصفحة.</p>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
            <table class="w-full text-right text-sm whitespace-nowrap">
                <thead class="bg-gray-50 text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-medium">رقم الموعد</th>
                        <th class="px-6 py-4 font-medium">المريض</th>
                        <th class="px-6 py-4 font-medium">الطبيب</th>
                        <th class="px-6 py-4 font-medium">الخدمة</th>
                        <th class="px-6 py-4 font-medium">الوقت</th>
                        <th class="px-6 py-4 font-medium text-center">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @foreach($appointments as $appointment)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-mono text-teal-custom font-bold">{{ $appointment->appointment_number }}</td>
                        <td class="px-6 py-4 font-bold text-gray-800">{{ $appointment->patient->full_name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $appointment->doctor->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $appointment->service }}</td>
                        <td class="px-6 py-4 font-mono text-gray-500">{{ $appointment->appointment_time ?? '—' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs font-bold px-2 py-1 rounded 
                                {{ $appointment->status == 'مؤكد' ? 'text-blue-600 bg-blue-50' : '' }}
                                {{ $appointment->status == 'مكتمل' ? 'text-green-600 bg-green-50' : '' }}
                                {{ $appointment->status == 'ملغي' ? 'text-red-600 bg-red-50' : '' }}
                                {{ $appointment->status == 'في الانتظار' ? 'text-orange-600 bg-orange-50' : '' }}
                            ">
                                {{ $appointment->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4" dir="ltr">
            {{ $appointments->links() }}
        </div>
    @endif

    <!-- Create Appointment Modal -->
    <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div x-show="isModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" aria-hidden="true" @click="isModalOpen = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="isModalOpen" x-transition class="inline-block align-bottom bg-white rounded-xl text-right overflow-visible shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full">
                
                <form action="{{ route('appointments.store') }}" method="POST">
                    @csrf

                    <!-- Modal Header -->
                    <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <button type="button" @click="isModalOpen = false" class="text-gray-400 hover:text-gray-500 focus:outline-none w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 hover:bg-gray-100">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                        <h3 class="text-lg leading-6 font-bold text-teal-custom" id="modal-title">حجز موعد</h3>
                    </div>

                    <!-- Modal Body -->
                    <div class="bg-white px-6 py-6 space-y-5">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">الطبيب</label>
                                <select name="doctor_id" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom bg-white text-sm">
                                    <option value="" disabled selected>اختر</option>
                                    @foreach($doctors as $doc)
                                        <option value="{{ $doc->id }}">{{ $doc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">المريض</label>
                                <select name="patient_id" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom bg-white text-sm">
                                    <option value="" disabled selected>اختر</option>
                                    @foreach($patients as $pat)
                                        <option value="{{ $pat->id }}">{{ $pat->full_name }} ({{ $pat->mrn }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">الخدمة</label>
                                <select name="service" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom bg-white text-sm">
                                    <option value="" disabled selected>اختر</option>
                                    <option value="كشف جلدية — 250 ر.س">كشف جلدية — 250 ر.س</option>
                                    <option value="كشف باطنة — 200 ر.س">كشف باطنة — 200 ر.س</option>
                                    <option value="كشف أطفال — 150 ر.س">كشف أطفال — 150 ر.س</option>
                                    <option value="كشف أسنان — 150 ر.س">كشف أسنان — 150 ر.س</option>
                                    <option value="كي ثآليل — 400 ر.س">كي ثآليل — 400 ر.س</option>
                                    <option value="متابعة — 100 ر.س">متابعة — 100 ر.س</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">التاريخ</label>
                                <input type="date" name="appointment_date" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-sm text-right">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">الفترات المتاحة</label>
                            <div class="w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 text-gray-400 text-sm text-center">
                                اختر طبيباً وتاريخاً لعرض الفترات
                            </div>
                        </div>
                        
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-start">
                        <button type="submit" class="bg-teal-custom hover:bg-teal-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition">
                            تأكيد الحجز
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
