@extends('layouts.app')

@section('title', 'المستخدمون والأدوار - فكرتك الرقمية')

@section('content')
<div x-data="{ isModalOpen: false, modalTitle: 'إنشاء مستخدم' }">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-teal-custom mb-1">المستخدمون والأدوار</h1>
            <p class="text-gray-500 text-sm">كل حساب مرتبط بفرع وصلاحيات دقيقة</p>
        </div>
        <button @click="isModalOpen = true" class="bg-teal-custom hover:bg-teal-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm flex items-center gap-2">
            مستخدم جديد
        </button>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
        <table class="w-full text-right text-sm whitespace-nowrap">
            <thead class="bg-gray-50 text-gray-500 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 font-medium">المستخدم</th>
                    <th class="px-6 py-4 font-medium">الاسم</th>
                    <th class="px-6 py-4 font-medium text-center">الدور</th>
                    <th class="px-6 py-4 font-medium text-center">الحالة</th>
                    <th class="px-6 py-4 font-medium text-center">آخر دخول</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-mono text-gray-600">{{ $user->username ?? $user->email }}</td>
                    <td class="px-6 py-4 font-bold text-gray-800">{{ $user->name }}</td>
                    <td class="px-6 py-4 text-center">{{ $user->role ?? 'مستخدم' }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-xs font-bold text-teal-500 bg-teal-50 px-2 py-1 rounded">
                            {{ $user->status ?? 'نشط' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center font-mono text-gray-500 text-xs">
                        {{ $user->last_login_at ?? '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">لا يوجد مستخدمين</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4" dir="ltr">
        {{ $users->links() }}
    </div>

    <!-- Create User Modal -->
    <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div x-show="isModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" aria-hidden="true" @click="isModalOpen = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="isModalOpen" x-transition class="inline-block align-bottom bg-white rounded-xl text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <!-- Modal Header -->
                    <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <button type="button" @click="isModalOpen = false" class="text-gray-400 hover:text-gray-500 focus:outline-none w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 hover:bg-gray-100">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                        <h3 class="text-lg leading-6 font-bold text-teal-custom" id="modal-title" x-text="modalTitle"></h3>
                    </div>

                    <!-- Modal Body -->
                    <div class="bg-white px-6 py-6 space-y-4">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">اسم المستخدم</label>
                            <input type="text" name="username" required dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">الاسم الكامل</label>
                            <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">كلمة المرور</label>
                            <input type="password" name="password" required placeholder="Demo@123" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">الدور</label>
                            <select name="role" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom bg-white">
                                <option value="" disabled selected>اختر</option>
                                <option value="مدير النظام">مدير النظام</option>
                                <option value="مدير المركز">مدير المركز</option>
                                <option value="موظف استقبال">موظف استقبال</option>
                                <option value="طبيب">طبيب</option>
                                <option value="تمريض">تمريض</option>
                                <option value="فني مخبر">فني مخبر</option>
                                <option value="مراجع مخبر">مراجع مخبر</option>
                                <option value="فني أشعة">فني أشعة</option>
                                <option value="استشاري أشعة">استشاري أشعة</option>
                                <option value="صيدلي">صيدلي</option>
                                <option value="أمين صندوق">أمين صندوق</option>
                                <option value="محاسب">محاسب</option>
                                <option value="مسؤول تقنية">مسؤول تقنية</option>
                            </select>
                        </div>
                        
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                        <button type="submit" class="w-full bg-teal-custom hover:bg-teal-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition">
                            حفظ
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
