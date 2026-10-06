@extends('layouts.app')

@section('title', $doctor->name . ' - فكرتك الرقمية')

@section('content')
<div x-data="{ activeTab: 'basic', isEditModalOpen: false }">
    <!-- Breadcrumb & Header Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            
            <!-- Left Side (Actions) -->
            <div class="flex items-center gap-3 order-1 md:order-2">
                <form action="{{ route('doctors.toggle_status', $doctor->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="border {{ $doctor->status === 'نشط' ? 'border-red-200 text-red-500 hover:bg-red-50' : 'border-green-200 text-green-600 hover:bg-green-50' }} px-4 py-2 rounded-full text-sm font-medium transition">
                        {{ $doctor->status === 'نشط' ? 'إيقاف الحساب' : 'تفعيل الحساب' }}
                    </button>
                </form>
                <button @click="isEditModalOpen = true" class="border border-teal-200 text-teal-custom hover:bg-teal-50 px-4 py-2 rounded-full text-sm font-medium transition flex items-center gap-2">
                    <i class="fa-solid fa-pen"></i> تعديل البيانات
                </button>
                @if($doctor->is_present)
                    <span class="text-xs font-bold text-teal-600 bg-teal-50 px-3 py-2 rounded-full">حاضر</span>
                @else
                    <span class="text-xs font-bold text-gray-500 bg-gray-100 px-3 py-2 rounded-full">إجازة</span>
                @endif
            </div>

            <!-- Right Side (Doctor Info & Back Link) -->
            <div class="flex flex-col items-end gap-2 order-2 md:order-1 w-full md:w-auto">
                <a href="{{ route('doctors.index') }}" class="text-teal-custom text-sm hover:underline mb-2 flex items-center gap-1 self-start md:self-end">
                    الرجوع لكل الأطباء <i class="fa-solid fa-arrow-left text-xs"></i> 
                </a>
                
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <h2 class="text-xl font-bold text-gray-800">{{ $doctor->name }}</h2>
                        <p class="text-sm text-gray-500 mt-1">{{ $doctor->specialization ?? 'عام' }} - {{ $doctor->clinic->name ?? 'غير محدد' }}</p>
                    </div>
                    <!-- Initials Circle -->
                    <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-teal-custom text-lg font-bold shrink-0">
                        {{ mb_substr(str_replace('د. ', '', $doctor->name), 0, 2) }}
                    </div>
                </div>
            </div>

        </div>

        <!-- Tabs Navigation -->
        <div class="flex gap-8 border-b border-gray-100">
            <button @click="activeTab = 'basic'" :class="activeTab === 'basic' ? 'border-teal-custom text-teal-custom font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-3 border-b-2 transition">
                بيانات أساسية
            </button>
            <button @click="activeTab = 'schedule'" :class="activeTab === 'schedule' ? 'border-teal-custom text-teal-custom font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-3 border-b-2 transition">
                الدوام الأسبوعي
            </button>
            <button @click="activeTab = 'leaves'" :class="activeTab === 'leaves' ? 'border-teal-custom text-teal-custom font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-3 border-b-2 transition">
                الإجازات
            </button>
        </div>
    </div>

    <!-- Tab Content: Basic Data -->
    <div x-show="activeTab === 'basic'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            
            <!-- Picture Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-800 mb-6">الصورة الشخصية</h3>
                <div class="flex items-center gap-4">
                    @if($doctor->avatar)
                        <img src="{{ asset('storage/' . $doctor->avatar) }}" alt="{{ $doctor->name }}" class="w-16 h-16 rounded-full object-cover shrink-0 border border-gray-200">
                    @else
                        <div class="w-16 h-16 rounded-full bg-blue-50 flex items-center justify-center text-teal-custom text-xl font-bold shrink-0">
                            {{ mb_substr(str_replace('د. ', '', $doctor->name), 0, 2) }}
                        </div>
                    @endif

                    <form action="{{ route('doctors.avatar', $doctor->id) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                        @csrf
                        <input type="file" name="avatar" id="avatarInput" class="hidden" accept="image/*" onchange="this.form.submit()">
                        <button type="button" onclick="document.getElementById('avatarInput').click()" class="border border-gray-200 text-gray-600 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm transition">
                            رفع صورة
                        </button>
                    </form>
                </div>
                @error('avatar')
                    <span class="text-xs text-red-500 mt-2 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Prof & Personal Data Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-800 mb-6 text-teal-custom">البيانات الشخصية والمهنية</h3>
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-gray-500">التخصص</span>
                        <span class="font-medium text-gray-800">{{ $doctor->specialization ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-gray-500">العيادة/القسم</span>
                        <span class="font-bold text-teal-custom">{{ $doctor->clinic->name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-gray-500">مدة الكشف</span>
                        <span class="font-medium text-gray-800">{{ $doctor->consultation_duration }} دقيقة</span>
                    </div>
                    <div class="flex justify-between pb-2">
                        <span class="text-gray-500">رسوم الكشف</span>
                        <span class="font-medium text-gray-800">250 ر.س</span> <!-- Hardcoded for UI demo -->
                    </div>
                </div>
            </div>

            <!-- Contact Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-800 mb-6 text-teal-custom">بيانات التواصل</h3>
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-gray-500">الجوال</span>
                        <span class="font-medium text-gray-800" dir="ltr">{{ $doctor->phone ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between pb-2">
                        <span class="text-gray-500">البريد الداخلي</span>
                        <span class="font-medium text-gray-800">{{ $doctor->email ?? '—' }}</span>
                    </div>
                </div>
            </div>

            <!-- Quals & Licenses Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-800 mb-6 text-teal-custom">المؤهلات والتراخيص</h3>
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-gray-500">رقم الترخيص</span>
                        <span class="font-medium text-gray-800" dir="ltr">{{ $doctor->license_number ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between pb-2">
                        <span class="text-gray-500">اسم المستخدم</span>
                        <span class="font-medium text-gray-800">{{ $doctor->username ?? '—' }}</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Today's Appointments -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-bold text-teal-custom mb-6">مواعيد اليوم</h3>
            <div class="py-12 text-center text-gray-400 text-sm">
                لا مواعيد اليوم
            </div>
        </div>
    </div>

    <!-- Tab Content: Weekly Schedule -->
    <div x-show="activeTab === 'schedule'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
        
        <!-- Notice Box -->
        <div class="bg-blue-50 border border-blue-100 text-teal-custom px-4 py-3 rounded-lg text-xs font-bold mb-6 flex items-center gap-2">
            جدول الدوام هو المرجع الأساسي لحجز المواعيد. الأيام غير المفعلة أو خارج الفترات لن تظهر فيها مواعيد متاحة. الإجازات منفصلة وتدار من تبويب الإجازات.
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-4 mb-6">
            <!-- Render 7 Days -->
            @php
                $days = ['السبت' => false, 'الأحد' => true, 'الاثنين' => true, 'الثلاثاء' => true, 'الأربعاء' => true, 'الخميس' => true, 'الجمعة' => false];
            @endphp
            
            @foreach($days as $day => $isActive)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4" x-data="{ isActive: {{ $isActive ? 'true' : 'false' }} }">
                <!-- Day Header -->
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-bold text-gray-800 text-sm">{{ $day }}</h4>
                    <!-- Toggle Switch -->
                    <button @click="isActive = !isActive" :class="isActive ? 'bg-teal-custom' : 'bg-gray-200'" class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none">
                        <span :class="isActive ? '-translate-x-4 bg-white' : 'translate-x-0 bg-white'" class="inline-block h-4 w-4 transform rounded-full transition-transform"></span>
                    </button>
                </div>

                <!-- Active State -->
                <div x-show="isActive" class="space-y-3">
                    <!-- Shift 1 -->
                    <div class="bg-gray-50 border border-teal-100 rounded p-3">
                        <div class="text-[10px] text-teal-custom font-bold mb-2">فترة 1</div>
                        <div class="flex items-center gap-2 text-xs">
                            <span class="text-gray-400">من</span>
                            <input type="time" value="09:00" class="w-full bg-transparent border-none p-0 text-gray-700 text-center font-mono focus:ring-0">
                        </div>
                        <div class="flex items-center gap-2 text-xs mt-2 border-t border-gray-200 pt-2">
                            <span class="text-gray-400">إلى</span>
                            <input type="time" value="13:00" class="w-full bg-transparent border-none p-0 text-gray-700 text-center font-mono focus:ring-0">
                        </div>
                    </div>
                    <!-- Shift 2 -->
                    <div class="bg-gray-50 border border-teal-100 rounded p-3">
                        <div class="text-[10px] text-teal-custom font-bold mb-2">فترة 2</div>
                        <div class="flex items-center gap-2 text-xs">
                            <span class="text-gray-400">من</span>
                            <input type="time" value="17:00" class="w-full bg-transparent border-none p-0 text-gray-700 text-center font-mono focus:ring-0">
                        </div>
                        <div class="flex items-center gap-2 text-xs mt-2 border-t border-gray-200 pt-2">
                            <span class="text-gray-400">إلى</span>
                            <input type="time" value="21:00" class="w-full bg-transparent border-none p-0 text-gray-700 text-center font-mono focus:ring-0">
                        </div>
                    </div>

                    <button class="w-full text-xs text-gray-400 border border-dashed border-gray-200 rounded py-2 hover:bg-gray-50 hover:text-teal-custom transition">
                        + فترة إضافية
                    </button>
                </div>

                <!-- Inactive State -->
                <div x-show="!isActive" class="py-8 text-center text-xs text-gray-400" style="display: none;">
                    غير متاح
                </div>
            </div>
            @endforeach
        </div>

        <button class="bg-teal-custom hover:bg-teal-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
            حفظ جدول الدوام
        </button>
    </div>

    <!-- Tab Content: Leaves -->
    <div x-show="activeTab === 'leaves'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
        
        <!-- Notice Box -->
        <div class="bg-orange-50 border border-orange-100 text-orange-800 px-4 py-3 rounded-lg text-xs font-bold mb-6 flex items-center gap-2">
            أي إجازة تخفي الطبيب تلقائياً من قائمة حجز المواعيد في كل تاريخ يقع داخل الفترة. المواعيد المحجوزة مسبقاً لا تلغى - يلزم تنبيه المراجعين فقط.
        </div>

        <div class="mb-4">
            <button class="border border-teal-200 text-teal-custom bg-white hover:bg-teal-50 px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> تسجيل إجازة
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
            <table class="w-full text-right text-sm whitespace-nowrap">
                <thead class="bg-gray-50 text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-medium">السبب</th>
                        <th class="px-6 py-4 font-medium">من</th>
                        <th class="px-6 py-4 font-medium">إلى</th>
                        <th class="px-6 py-4 font-medium text-center">الحالة</th>
                        <th class="px-6 py-4 font-medium text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-gray-800 font-bold">إجازة تدريبية — تشاور، نيو عيد</td>
                        <td class="px-6 py-4 font-mono text-gray-500">2026-09-30</td>
                        <td class="px-6 py-4 font-mono text-gray-500">2026-10-30</td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs font-bold text-teal-600 bg-teal-50 px-2 py-1 rounded">معتمدة</span>
                        </td>
                        <td class="px-6 py-4 text-center flex justify-center gap-2">
                            <button class="text-blue-500 bg-blue-50 px-2 py-1 rounded text-xs hover:bg-blue-100">تعديل</button>
                            <button class="text-red-500 bg-red-50 px-2 py-1 rounded text-xs hover:bg-red-100">حذف</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    <!-- Edit Doctor Modal -->
    <div x-show="isEditModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div x-show="isEditModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" aria-hidden="true" @click="isEditModalOpen = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="isEditModalOpen" x-transition class="inline-block align-bottom bg-white rounded-xl text-right overflow-visible shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                
                <form action="{{ route('doctors.update', $doctor->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Modal Header -->
                    <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <button type="button" @click="isEditModalOpen = false" class="text-gray-400 hover:text-gray-500 focus:outline-none w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 hover:bg-gray-100">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                        <h3 class="text-lg leading-6 font-bold text-teal-custom" id="modal-title">تعديل بيانات الطبيب</h3>
                    </div>

                    <!-- Modal Body -->
                    <div class="bg-white px-8 py-6 space-y-6">
                        
                        <!-- Personal Data -->
                        <div>
                            <h4 class="text-sm font-bold text-teal-custom mb-4">البيانات الشخصية</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-2">الاسم الكامل</label>
                                    <input type="text" name="name" value="{{ $doctor->name }}" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom">
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-2">الجوال (اختياري)</label>
                                        <input type="text" name="phone" value="{{ $doctor->phone }}" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-2">البريد الداخلي (اختياري)</label>
                                        <input type="email" name="email" value="{{ $doctor->email }}" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Professional Data -->
                        <div>
                            <h4 class="text-sm font-bold text-teal-custom mb-4">البيانات المهنية</h4>
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-2">التخصص</label>
                                        <select name="specialization" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom bg-white">
                                            <option value="الجلدية" {{ $doctor->specialization == 'الجلدية' ? 'selected' : '' }}>الجلدية</option>
                                            <option value="الباطنة" {{ $doctor->specialization == 'الباطنة' ? 'selected' : '' }}>الباطنة</option>
                                            <option value="الأطفال" {{ $doctor->specialization == 'الأطفال' ? 'selected' : '' }}>الأطفال</option>
                                            <option value="النساء والولادة" {{ $doctor->specialization == 'النساء والولادة' ? 'selected' : '' }}>النساء والولادة</option>
                                            <option value="العظام" {{ $doctor->specialization == 'العظام' ? 'selected' : '' }}>العظام</option>
                                            <option value="عام" {{ $doctor->specialization == 'عام' ? 'selected' : '' }}>عام</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-2">العيادة / القسم</label>
                                        <select name="clinic_id" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom bg-white">
                                            @foreach($clinics as $clinic)
                                                <option value="{{ $clinic->id }}" {{ $doctor->clinic_id == $clinic->id ? 'selected' : '' }}>{{ $clinic->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-2">مدة الكشف (دقيقة)</label>
                                        <input type="number" name="consultation_duration" value="{{ $doctor->consultation_duration }}" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-2">رقم الترخيص</label>
                                        <input type="text" name="license_number" value="{{ $doctor->license_number }}" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-start gap-3">
                        <button type="submit" class="bg-teal-custom hover:bg-teal-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition">
                            حفظ التعديلات
                        </button>
                        <button type="button" @click="isEditModalOpen = false" class="border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 px-6 py-2 rounded-lg text-sm font-medium transition">
                            إلغاء
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
