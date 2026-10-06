@extends('layouts.app')

@section('title', $clinic->name . ' - فكرتك الرقمية')

@section('content')
<div class="space-y-6" x-data="clinicSingleManager()">
    <!-- Top Action Bar -->
    <div class="flex justify-end mb-2">
        <a href="{{ route('clinics.index') }}" class="text-teal-custom hover:text-teal-700 text-sm font-bold flex items-center gap-2 transition">
            الرجوع لكل العيادات <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 flex justify-between items-center">
        <!-- Right side Header (Clinic Info) -->
        <div class="flex items-center gap-4 text-right">
            <div class="w-12 h-12 rounded-full bg-teal-50 flex items-center justify-center text-teal-custom text-xl overflow-hidden shadow-sm shrink-0">
                @if($clinic->icon && str_contains($clinic->icon, '.'))
                    <img src="{{ asset('storage/' . $clinic->icon) }}" class="w-full h-full object-cover" alt="أيقونة العيادة">
                @else
                    <i class="fa-solid {{ $clinic->icon ?? 'fa-stethoscope' }}"></i>
                @endif
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-1">{{ $clinic->name }}</h1>
                <p class="text-gray-400 text-sm font-medium">{{ $clinic->code ?? '00' }} - {{ $clinic->headDoctor ? $clinic->headDoctor->name : 'بدون رئيس قسم' }}</p>
            </div>
        </div>

        <!-- Edit Button (Left) -->
        <button @click="openEditModal()" class="bg-white border border-gray-200 text-teal-custom hover:bg-gray-50 px-5 py-2.5 rounded-full text-sm font-bold transition shadow-sm flex items-center gap-2">
            تعديل بيانات العيادة
            <i class="fa-solid fa-pen-to-square"></i>
        </button>
    </div>

    <!-- KPIs Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- KPI 1 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col items-center justify-center">
            <span class="text-gray-400 text-sm mb-3">في انتظار الان</span>
            <span class="text-3xl font-bold text-teal-custom">1</span>
        </div>
        <!-- KPI 2 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col items-center justify-center">
            <span class="text-gray-400 text-sm mb-3">منجز اليوم</span>
            <span class="text-3xl font-bold text-teal-custom">0</span>
        </div>
        <!-- KPI 3 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col items-center justify-center">
            <span class="text-gray-400 text-sm mb-3">عدد الأطباء</span>
            <span class="text-3xl font-bold text-teal-custom">{{ $clinic->doctors->count() }}</span>
        </div>
        <!-- KPI 4 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col items-center justify-center">
            <span class="text-gray-400 text-sm mb-3">الحالة</span>
            <span class="text-xl font-bold {{ $clinic->status == 'نشطة' ? 'text-teal-custom' : 'text-red-500' }}">{{ $clinic->status }}</span>
        </div>
    </div>

    <!-- Clinic Info Bar -->
    <div class="flex flex-wrap gap-4 items-center bg-teal-50/50 rounded-xl border border-teal-100 p-4">
        <div class="flex items-center gap-2 text-sm text-teal-800">
            <i class="fa-regular fa-clock text-teal-600"></i>
            <span class="font-bold">ساعات العمل:</span>
            <span>{{ $clinic->start_time ? \Carbon\Carbon::parse($clinic->start_time)->format('h:i A') : 'غير محدد' }} - {{ $clinic->end_time ? \Carbon\Carbon::parse($clinic->end_time)->format('h:i A') : 'غير محدد' }}</span>
        </div>
        <div class="w-px h-5 bg-teal-200 hidden md:block"></div>
        <div class="flex items-center gap-2 text-sm text-teal-800">
            <i class="fa-solid fa-users text-teal-600"></i>
            <span class="font-bold">الطاقة الاستيعابية:</span>
            <span>{{ $clinic->daily_quota ?? 'غير محدد' }} مريض يومياً</span>
        </div>
        <div class="w-px h-5 bg-teal-200 hidden md:block"></div>
        <div class="flex items-center gap-2 text-sm text-teal-800">
            <i class="fa-regular fa-clipboard text-teal-600"></i>
            <span class="font-bold">ملاحظات:</span>
            <span class="truncate max-w-xs">{{ $clinic->notes ?: 'لا توجد ملاحظات' }}</span>
        </div>
    </div>

    <!-- Doctors Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-teal-custom font-bold">أطباء القسم</h3>
            <div class="flex gap-2">
                <button @click="openAssignDoctorModal()" class="text-sm bg-white border border-teal-custom text-teal-custom px-4 py-1.5 rounded-lg hover:bg-teal-50 transition">
                    تعيين طبيب موجود
                </button>
                <button @click="openAddDoctorModal()" class="text-sm bg-teal-custom text-white px-4 py-1.5 rounded-lg hover:bg-teal-700 transition">
                    + إضافة طبيب
                </button>
            </div>
        </div>
        
        <table class="w-full text-right text-sm">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="px-6 py-4 font-medium w-1/3">الطبيب</th>
                    <th class="px-6 py-4 font-medium text-center w-1/3">التخصص</th>
                    <th class="px-6 py-4 font-medium text-center w-1/3">الحالة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @forelse($clinic->doctors as $doctor)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <a href="{{ route('doctors.show', $doctor->id) }}" class="font-bold text-gray-800 hover:text-teal-custom transition block">{{ $doctor->name }}</a>
                        </td>
                        <td class="px-6 py-4 text-center text-gray-500">
                            {{ $doctor->specialization ?? $clinic->name }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 text-teal-500 font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                                نشط
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i class="fa-solid fa-user-doctor text-3xl mb-2 text-gray-300"></i>
                                لا يوجد أطباء مرتبطين بهذه العيادة حالياً.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Edit Clinic Modal -->
    <div x-show="isEditModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="isEditModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" aria-hidden="true" @click="closeEditModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="isEditModalOpen" x-transition class="inline-block align-bottom bg-white rounded-xl text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form :action="formAction" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <!-- Modal Header -->
                    <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg leading-6 font-bold text-teal-custom" id="modal-title">تعديل بيانات العيادة</h3>
                        <button type="button" @click="closeEditModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 hover:bg-gray-100">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <!-- Modal Body -->
                    <div class="bg-white px-6 py-6 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2 text-right">اسم العيادة / القسم</label>
                                <input type="text" name="name" x-model="form.name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right bg-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2 text-right">أيقونة العيادة (صورة)</label>
                                <input type="file" name="icon_file" accept="image/*" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right bg-white text-sm">
                                <p class="text-xs text-gray-400 mt-1 text-right">اختياري، يفضل صورة بخلفية شفافة</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2 text-right">لون القسم (التمييز اللوني)</label>
                                <input type="color" name="color" x-model="form.color" class="w-full h-10 px-1 py-1 border border-gray-200 rounded-lg focus:outline-none bg-white cursor-pointer">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2 text-right">رئيس القسم (اختياري)</label>
                                <select name="head_doctor_id" x-model="form.head_doctor_id" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right bg-white">
                                    <option value="">بدون رئيس قسم حالياً</option>
                                    @foreach($allDoctors ?? [] as $doc)
                                        <option value="{{ $doc->id }}">{{ $doc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">الرمز</label>
                                <input type="text" name="code" x-model="form.code" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">حالة العيادة</label>
                                <select name="status" x-model="form.status" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom bg-white">
                                    <option value="نشطة">نشطة</option>
                                    <option value="متوقفة">متوقفة</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2 text-right">الطاقة الاستيعابية</label>
                                <input type="number" name="daily_quota" x-model="form.daily_quota" min="1" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right bg-white" placeholder="مثال: 30">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2 text-right">بداية الدوام</label>
                                <input type="time" name="start_time" x-model="form.start_time" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right bg-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2 text-right">نهاية الدوام</label>
                                <input type="time" name="end_time" x-model="form.end_time" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right bg-white">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2 text-right">ملاحظات / الموقع</label>
                            <textarea name="notes" x-model="form.notes" rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom resize-none text-right bg-gray-50"></textarea>
                        </div>
                    </div>
                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end gap-3 items-center">
                        <button type="button" @click="closeEditModal()" class="border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 px-6 py-2 rounded-lg text-sm font-medium transition">
                            إلغاء
                        </button>
                        <button type="submit" class="bg-teal-custom hover:bg-teal-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition">
                            حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Doctor Modal -->
    <div x-show="isAddDoctorModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="isAddDoctorModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" aria-hidden="true" @click="closeAddDoctorModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="isAddDoctorModalOpen" x-transition class="inline-block align-bottom bg-white rounded-xl text-right overflow-visible shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                <form action="{{ route('doctors.store') }}" method="POST">
                    @csrf
                    <!-- Modal Header -->
                    <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <button type="button" @click="closeAddDoctorModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 hover:bg-gray-100">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                        <h3 class="text-lg leading-6 font-bold text-teal-custom" id="modal-title">إضافة طبيب جديد</h3>
                    </div>
                    <!-- Modal Body -->
                    <div class="bg-white px-8 py-6 space-y-6">
                        <!-- Personal Data -->
                        <div>
                            <h4 class="text-sm font-bold text-teal-custom mb-4">البيانات الشخصية</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-2">الاسم الكامل</label>
                                    <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom">
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-2">الجوال (اختياري)</label>
                                        <input type="text" name="phone" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-2">البريد الداخلي (اختياري)</label>
                                        <input type="email" name="email" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right">
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
                                            <option value="" disabled selected>اختر</option>
                                            <option value="الجلدية" {{ $clinic->name == 'الجلدية' ? 'selected' : '' }}>الجلدية</option>
                                            <option value="الباطنة" {{ $clinic->name == 'الباطنة' ? 'selected' : '' }}>الباطنة</option>
                                            <option value="الأطفال" {{ $clinic->name == 'الأطفال' ? 'selected' : '' }}>الأطفال</option>
                                            <option value="النساء والولادة" {{ $clinic->name == 'النساء والولادة' ? 'selected' : '' }}>النساء والولادة</option>
                                            <option value="العظام" {{ $clinic->name == 'العظام' ? 'selected' : '' }}>العظام</option>
                                            <option value="عام" {{ $clinic->name == 'عام' ? 'selected' : '' }}>عام</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-2">العيادة / القسم</label>
                                        <select name="clinic_id" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom bg-white">
                                            <option value="{{ $clinic->id }}" selected>{{ $clinic->name }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-2">مدة الكشف (دقيقة)</label>
                                        <input type="number" name="consultation_duration" value="20" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-2">رقم الترخيص</label>
                                        <input type="text" name="license_number" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-start gap-3">
                        <button type="submit" class="bg-teal-custom hover:bg-teal-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition">
                            حفظ بيانات الطبيب
                        </button>
                        <button type="button" @click="closeAddDoctorModal()" class="border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 px-6 py-2 rounded-lg text-sm font-medium transition">
                            إلغاء
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Assign Existing Doctor Modal -->
    <div x-show="isAssignDoctorModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="isAssignDoctorModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" aria-hidden="true" @click="closeAssignDoctorModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="isAssignDoctorModalOpen" x-transition class="inline-block align-bottom bg-white rounded-xl text-right overflow-visible shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                <form action="{{ route('clinics.assign_doctor', $clinic->id) }}" method="POST">
                    @csrf
                    <!-- Modal Header -->
                    <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <button type="button" @click="closeAssignDoctorModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 hover:bg-gray-100">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                        <h3 class="text-lg leading-6 font-bold text-teal-custom" id="modal-title">تعيين طبيب موجود</h3>
                    </div>
                    <!-- Modal Body -->
                    <div class="bg-white px-8 py-6 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">اختر الطبيب</label>
                            <select name="doctor_id" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom bg-white">
                                <option value="" disabled selected>اختر طبيباً لتعيينه في هذا القسم...</option>
                                @foreach($allDoctors->where('clinic_id', '!=', $clinic->id) as $doc)
                                    <option value="{{ $doc->id }}">{{ $doc->name }} ({{ $doc->specialization ?? 'عام' }})</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-2">ملاحظة: نقل الطبيب إلى هذا القسم سيؤدي إلى إزالته من قسمه الحالي.</p>
                        </div>
                    </div>
                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-start gap-3">
                        <button type="submit" class="bg-teal-custom hover:bg-teal-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition">
                            تعيين الطبيب
                        </button>
                        <button type="button" @click="closeAssignDoctorModal()" class="border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 px-6 py-2 rounded-lg text-sm font-medium transition">
                            إلغاء
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const registerClinicSingleManager = () => {
        Alpine.data('clinicSingleManager', () => ({
            isEditModalOpen: false,
            isAddDoctorModalOpen: false,
            isAssignDoctorModalOpen: false,
            formAction: '/clinics/{{ $clinic->id }}',
            form: {
                id: {{ $clinic->id }},
                name: '{{ $clinic->name }}',
                icon: '{{ $clinic->icon }}',
                color: '{{ $clinic->color }}',
                head_doctor_id: '{{ $clinic->head_doctor_id }}',
                code: '{{ $clinic->code }}',
                status: '{{ $clinic->status }}',
                daily_quota: '{{ $clinic->daily_quota }}',
                start_time: '{{ $clinic->start_time ? substr($clinic->start_time, 0, 5) : '' }}',
                end_time: '{{ $clinic->end_time ? substr($clinic->end_time, 0, 5) : '' }}',
                notes: '{{ $clinic->notes }}'
            },
            
            openEditModal() {
                this.isEditModalOpen = true;
            },
            
            closeEditModal() {
                this.isEditModalOpen = false;
            },

            openAddDoctorModal() {
                this.isAddDoctorModalOpen = true;
            },

            closeAddDoctorModal() {
                this.isAddDoctorModalOpen = false;
            },
            
            openAssignDoctorModal() {
                this.isAssignDoctorModalOpen = true;
            },
            
            closeAssignDoctorModal() {
                this.isAssignDoctorModalOpen = false;
            }
        }));
    };

    if (window.Alpine) {
        registerClinicSingleManager();
    } else {
        document.addEventListener('alpine:init', registerClinicSingleManager);
    }
</script>
@endsection
