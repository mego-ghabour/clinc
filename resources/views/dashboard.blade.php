@extends('layouts.app')

@section('content')
            <!-- Page Header -->
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-teal-custom mb-1">نظرة عامة</h1>
                    <p class="text-gray-500 text-sm">فكرتك الرقمية - لوحة المدير العام</p>
                </div>
                
                <div class="flex items-center gap-2 text-teal-custom bg-teal-50 px-4 py-2 rounded-full text-sm border border-teal-100">
                    <span class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-teal-500"></span>
                    </span>
                    بيانات محدثة لحظياً
                </div>
            </div>

            <!-- Banner Alert -->
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-5 mb-6 flex justify-between items-center">
                <div>
                    <h3 class="text-teal-custom font-bold text-lg">مواعيد الأطباء المتاحة</h3>
                    <p class="text-gray-600 text-sm mt-1">حدد أيام وساعات عمل كل طبيب أو سجل إجازة — ينعكس فوراً على الحجز الداخلي والإلكتروني.</p>
                </div>
                <button class="bg-teal-custom hover:bg-teal-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    إدارة المواعيد
                </button>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <!-- Card 1 -->
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between h-32 relative">
                    <div class="flex justify-between items-start">
                        <span class="text-gray-500 text-sm">إجمالي مواعيد اليوم</span>
                        <div class="w-8 h-8 bg-gray-50 rounded-lg flex items-center justify-center text-gray-500">
                            <i class="fa-regular fa-calendar"></i>
                        </div>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-xs text-gray-400">0 منتهية - 0 ملغية</span>
                        <span class="text-4xl font-bold text-teal-custom">0</span>
                    </div>
                    <!-- Indicator Line -->
                    <div class="absolute bottom-0 right-0 left-0 h-1 bg-gray-200 rounded-b-xl"></div>
                </div>

                <!-- Card 2 -->
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between h-32 relative">
                    <div class="flex justify-between items-start">
                        <span class="text-gray-500 text-sm">في الانتظار الآن</span>
                        <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center text-orange-500">
                            <i class="fa-solid fa-hourglass-half"></i>
                        </div>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-xs text-gray-400">الطوابير بالحال</span>
                        <span class="text-4xl font-bold text-teal-custom">4</span>
                    </div>
                    <!-- Indicator Line -->
                    <div class="absolute bottom-0 right-0 left-0 h-1 bg-teal-500 rounded-b-xl"></div>
                </div>

                <!-- Card 3 -->
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between h-32 relative">
                    <div class="flex justify-between items-start">
                        <span class="text-gray-500 text-sm">حالات متأخرة عن موعدها</span>
                        <div class="w-8 h-8 bg-yellow-50 rounded-lg flex items-center justify-center text-yellow-500">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-xs text-gray-400">تجاوزت وقت البداية دون حضور</span>
                        <span class="text-4xl font-bold text-teal-custom">0</span>
                    </div>
                    <!-- Indicator Line -->
                    <div class="absolute bottom-0 right-0 left-0 h-1 bg-yellow-400 rounded-b-xl"></div>
                </div>

                <!-- Card 4 -->
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between h-32 relative">
                    <div class="flex justify-between items-start">
                        <span class="text-gray-500 text-sm">نسبة الحضور اليوم</span>
                        <div class="w-8 h-8 bg-gray-50 rounded-lg flex items-center justify-center text-gray-500">
                            <i class="fa-solid fa-check"></i>
                        </div>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-xs text-gray-400">من إجمالي المواعيد المحجوزة</span>
                        <span class="text-4xl font-bold text-teal-custom">0%</span>
                    </div>
                    <!-- Indicator Line -->
                    <div class="absolute bottom-0 right-0 left-0 h-1 bg-gray-200 rounded-b-xl"></div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-teal-custom font-bold">حركة العيادات والأقسام الآن</h3>
                </div>
                
                <table class="w-full text-right text-sm">
                    <thead class="bg-gray-50 text-gray-500">
                        <tr>
                            <th class="px-6 py-3 font-medium">العيادة / القسم</th>
                            <th class="px-6 py-3 font-medium text-center">في الانتظار</th>
                            <th class="px-6 py-3 font-medium text-center">منجز اليوم</th>
                            <th class="px-6 py-3 font-medium text-center">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        <!-- Row 1 -->
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center text-teal-custom">
                                        <i class="fa-solid fa-user-doctor"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800">الجلدية</div>
                                        <div class="text-xs text-gray-500">د. خالد الشمري</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-red-500 font-bold">1 مرضى</span>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-500">
                                0
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-teal-500 font-medium">طبيعي</span>
                            </td>
                        </tr>

                        <!-- Row 2 -->
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center text-teal-custom">
                                        <i class="fa-solid fa-baby"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800">الأطفال</div>
                                        <div class="text-xs text-gray-500">د. فهد العتيبي</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-red-500 font-bold">1 مرضى</span>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-500">
                                0
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-teal-500 font-medium">طبيعي</span>
                            </td>
                        </tr>

                        <!-- Row 3 -->
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center text-teal-custom">
                                        <i class="fa-solid fa-person-pregnant"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800">النساء والولادة</div>
                                        <div class="text-xs text-gray-500">د. سارة العتيبي</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-red-500 font-bold">1 مرضى</span>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-500">
                                0
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-teal-500 font-medium">طبيعي</span>
                            </td>
                        </tr>

                        <!-- Row 4 -->
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center text-teal-custom">
                                        <i class="fa-solid fa-bone"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800">العظام</div>
                                        <div class="text-xs text-gray-500">د. فيصل القحطاني</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-red-500 font-bold">1 مرضى</span>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-500">
                                0
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-teal-500 font-medium">طبيعي</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="h-10"></div>
@endsection
