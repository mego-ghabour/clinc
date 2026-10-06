@extends('layouts.app')

@section('title', 'إدارة المرضى - فكرتك الرقمية')

@section('content')
<div x-data="{ isModalOpen: false, modalTitle: 'تسجيل مريض جديد' }">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-teal-custom mb-1">إدارة المرضى</h1>
            <p class="text-gray-500 text-sm">البحث بالرقم الطبي أو الاسم أو الهوية أو الجوال</p>
        </div>
        <button @click="isModalOpen = true" class="bg-teal-custom hover:bg-teal-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-plus"></i>
            مريض جديد
        </button>
    </div>

    <!-- Search Form -->
    <form action="{{ route('patients.index') }}" method="GET" class="flex gap-2 mb-6">
        <div class="relative flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث: MRN / اسم / هوية / جوال" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-sm text-right bg-white">
        </div>
        <button type="submit" class="bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 px-6 py-3 rounded-lg text-sm font-medium transition">
            بحث
        </button>
    </form>

    <!-- Patients Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
        <table class="w-full text-right text-sm whitespace-nowrap">
            <thead class="bg-gray-50 text-gray-500 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 font-medium">الرقم الطبي</th>
                    <th class="px-6 py-4 font-medium text-right">الاسم</th>
                    <th class="px-6 py-4 font-medium text-center">الجنس</th>
                    <th class="px-6 py-4 font-medium text-center">الهوية</th>
                    <th class="px-6 py-4 font-medium text-center">الجوال</th>
                    <th class="px-6 py-4 font-medium text-center">المدينة</th>
                    <th class="px-6 py-4 font-medium text-center">الملف</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @forelse($patients as $patient)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-teal-custom font-bold font-mono">{{ $patient->mrn }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-teal-custom text-xs font-bold">
                                {{ mb_substr($patient->first_name, 0, 1) }}{{ mb_substr($patient->family_name, 0, 1) }}
                            </div>
                            <span class="font-bold text-gray-800">{{ $patient->full_name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">{{ $patient->gender ?? '-' }}</td>
                    <td class="px-6 py-4 text-center font-mono">{{ $patient->id_number ?? '-' }}</td>
                    <td class="px-6 py-4 text-center font-mono">{{ $patient->mobile ?? '-' }}</td>
                    <td class="px-6 py-4 text-center">{{ $patient->city ?? '-' }}</td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('patients.show', $patient->id) }}" class="text-xs bg-light-blue-custom text-teal-custom px-3 py-1.5 rounded-lg hover:bg-teal-50 transition inline-flex items-center gap-1">
                            فتح الملف <i class="fa-solid fa-arrow-left text-[10px]"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">لا يوجد مرضى مسجلين</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4" dir="ltr">
        {{ $patients->links() }}
    </div>

    <!-- Add Patient Modal -->
    <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div x-show="isModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" aria-hidden="true" @click="isModalOpen = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="isModalOpen" x-transition class="inline-block align-bottom bg-white rounded-xl text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                
                <form action="{{ route('patients.store') }}" method="POST">
                    @csrf

                    <!-- Modal Header -->
                    <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <button type="button" @click="isModalOpen = false" class="text-gray-400 hover:text-gray-500 focus:outline-none w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 hover:bg-gray-100">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                        <h3 class="text-lg leading-6 font-bold text-teal-custom" id="modal-title" x-text="modalTitle"></h3>
                    </div>

                    <!-- Modal Body -->
                    <div class="bg-white px-6 py-6 space-y-5">
                        
                        <!-- Row 1 -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">الاسم الأول *</label>
                                <input type="text" name="first_name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">اسم الأب</label>
                                <input type="text" name="father_name" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom">
                            </div>
                        </div>

                        <!-- Row 2 -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">اسم الجد</label>
                                <input type="text" name="grandfather_name" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">اسم العائلة *</label>
                                <input type="text" name="family_name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom">
                            </div>
                        </div>

                        <!-- Row 3 -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">الجنس</label>
                                <select name="gender" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom bg-white">
                                    <option value="ذكر">ذكر</option>
                                    <option value="أنثى">أنثى</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">تاريخ الميلاد</label>
                                <input type="date" name="date_of_birth" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right">
                            </div>
                        </div>

                        <!-- Row 4 -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">نوع الهوية</label>
                                <select name="id_type" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom bg-white">
                                    <option value="هوية وطنية">هوية وطنية</option>
                                    <option value="إقامة">إقامة</option>
                                    <option value="جواز سفر">جواز سفر</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">رقم الهوية</label>
                                <input type="text" name="id_number" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right">
                            </div>
                        </div>

                        <!-- Row 5 -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">الجنسية</label>
                                <input type="text" name="nationality" value="سعودي" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">الجوال</label>
                                <input type="text" name="mobile" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right">
                            </div>
                        </div>

                        <!-- Row 6 -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">المدينة</label>
                                <input type="text" name="city" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">الحي</label>
                                <input type="text" name="district" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom">
                            </div>
                        </div>

                        <!-- Row 7 -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">فصيلة الدم</label>
                                <input type="text" name="blood_type" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">جهة اتصال للطوارئ</label>
                                <input type="text" name="emergency_contact" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right">
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end gap-3 items-center">
                        <button type="submit" class="bg-teal-custom hover:bg-teal-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition">
                            حفظ
                        </button>
                        <button type="button" @click="isModalOpen = false" class="border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 px-6 py-2 rounded-lg text-sm font-medium transition">
                            إلغاء
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
