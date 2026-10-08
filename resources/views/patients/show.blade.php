@extends('layouts.app')

@section('title', 'الملف الطبي - ' . $patient->name)
@section('header', 'الملف الطبي الشامل للمريض')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'overview' }">

    <!-- Header Actions -->
    <div class="flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <h1 class="text-xl font-black text-teal-800 flex items-center gap-2">
            <i class="fa-solid fa-folder-open text-teal-500"></i> ملف المريض
        </h1>
        <div class="flex gap-2">
            <a href="{{ route('visits.index', ['patient_id' => $patient->id, 'open_modal' => 1]) }}" class="bg-teal-custom hover:bg-teal-700 text-white px-6 py-3 rounded-xl text-sm font-bold transition flex items-center justify-center gap-2 shadow-sm"><i class="fa-solid fa-notes-medical"></i> بدء زيارة طبية (كشف)</a>
            <a href="{{ route('appointments.index', ['patient_id' => $patient->id, 'open_modal' => 1]) }}" class="bg-gray-50 hover:bg-gray-100 text-teal-700 border border-gray-200 px-6 py-3 rounded-xl text-sm font-bold transition flex items-center justify-center gap-2 shadow-sm"><i class="fa-solid fa-calendar-plus"></i> حجز موعد جديد</a>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex overflow-x-auto custom-scrollbar">
        <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'border-teal-custom text-teal-custom bg-teal-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'" class="whitespace-nowrap px-6 py-4 border-b-2 font-black text-sm transition">
            <i class="fa-solid fa-address-card ml-1.5"></i> نظرة عامة
        </button>
        <button @click="activeTab = 'visits'" :class="activeTab === 'visits' ? 'border-teal-custom text-teal-custom bg-teal-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'" class="whitespace-nowrap px-6 py-4 border-b-2 font-black text-sm transition">
            <i class="fa-solid fa-stethoscope ml-1.5"></i> الزيارات الطبية (EMR)
        </button>
        <button @click="activeTab = 'appointments'" :class="activeTab === 'appointments' ? 'border-teal-custom text-teal-custom bg-teal-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'" class="whitespace-nowrap px-6 py-4 border-b-2 font-black text-sm transition">
            <i class="fa-regular fa-calendar-check ml-1.5"></i> المواعيد
        </button>
    </div>

    <!-- Tab Contents -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 min-h-[400px]">
        
        <!-- Overview Tab -->
        <div x-show="activeTab === 'overview'" x-transition.opacity>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Data -->
                <div>
                    <h3 class="font-black text-gray-800 mb-4 border-b border-gray-100 pb-2"><i class="fa-solid fa-id-card text-teal-500 ml-1"></i> البيانات الشخصية</h3>
                    <div class="space-y-3 bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <div class="flex justify-between border-b border-gray-200 pb-2">
                            <span class="text-sm font-bold text-gray-500">رقم الهوية / الإقامة:</span>
                            <span class="text-sm font-black text-gray-800 font-mono">{{ $patient->id_number ?? 'غير مسجل' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 pb-2">
                            <span class="text-sm font-bold text-gray-500">تاريخ الميلاد:</span>
                            <span class="text-sm font-black text-gray-800 font-mono" dir="ltr">{{ $patient->date_of_birth ? substr($patient->date_of_birth, 0, 10) : 'غير مسجل' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 pb-2">
                            <span class="text-sm font-bold text-gray-500">الجنسية:</span>
                            <span class="text-sm font-black text-gray-800">{{ $patient->nationality ?? 'غير مسجل' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-bold text-gray-500">البريد الإلكتروني:</span>
                            <span class="text-sm font-black text-gray-800" dir="ltr">{{ $patient->email ?? 'لا يوجد' }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Notes -->
                <div>
                    <h3 class="font-black text-gray-800 mb-4 border-b border-gray-100 pb-2"><i class="fa-solid fa-notes-medical text-teal-500 ml-1"></i> ملاحظات طبية هامة</h3>
                    <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-200 h-[calc(100%-2.5rem)]">
                        @if($patient->notes)
                            <p class="text-sm font-bold text-yellow-800 whitespace-pre-line">{{ $patient->notes }}</p>
                        @else
                            <p class="text-sm font-bold text-yellow-600/60 text-center mt-6">لا توجد ملاحظات طبية مسجلة.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Visits Tab -->
        <div x-show="activeTab === 'visits'" x-transition.opacity style="display: none;">
            <div class="text-center py-16">
                <i class="fa-solid fa-stethoscope text-5xl text-gray-200 mb-4"></i>
                <h3 class="text-lg font-black text-gray-700">الزيارات الطبية (EMR)</h3>
                <p class="text-gray-500 text-sm mt-1">سجل الزيارات والوصفات الطبية سيظهر هنا.</p>
            </div>
        </div>

        <!-- Appointments Tab -->
        <div x-show="activeTab === 'appointments'" x-transition.opacity style="display: none;">
            <div class="text-center py-16">
                <i class="fa-regular fa-calendar-check text-5xl text-gray-200 mb-4"></i>
                <h3 class="text-lg font-black text-gray-700">المواعيد السابقة والقادمة</h3>
                <p class="text-gray-500 text-sm mt-1">جدول المواعيد الخاص بالمريض سيتم ربطه هنا قريباً.</p>
            </div>
        </div>

    </div>
</div>

<!-- Edit Patient Modal -->
<div x-data="editPatientModal()" @open-edit-modal.window="isModalOpen = true" x-cloak>
    <div x-show="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="isModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm transition-opacity" @click="isModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="isModalOpen" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block align-bottom bg-white rounded-3xl text-right overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-gray-100">
                
                <form action="{{ route('patients.update', $patient->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="bg-white px-6 py-5 border-b border-gray-100 flex justify-between items-center sticky top-0 z-10">
                        <h3 class="text-xl font-black text-gray-800 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm"><i class="fa-solid fa-pen"></i></span>
                            تعديل بيانات المريض
                        </h3>
                        <button type="button" @click="isModalOpen = false" class="w-8 h-8 rounded-full bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition flex items-center justify-center">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <div class="px-6 py-6 space-y-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                        
                        <!-- Avatar -->
                        <div class="flex flex-col items-center justify-center bg-gray-50 p-5 rounded-2xl border border-gray-100">
                            <label class="block text-sm font-bold text-gray-700 mb-3 text-center">الصورة الشخصية (اختياري)</label>
                            <div class="relative w-24 h-24 rounded-full overflow-hidden border-2 border-teal-500 bg-white group shadow-sm">
                                <template x-if="photoPreview">
                                    <img :src="photoPreview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!photoPreview">
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <i class="fa-solid fa-camera text-2xl"></i>
                                    </div>
                                </template>
                                <label class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition cursor-pointer">
                                    <i class="fa-solid fa-upload text-white text-xl"></i>
                                    <input type="file" name="avatar" class="hidden" accept="image/*" @change="updatePhotoPreview">
                                </label>
                            </div>
                        </div>

                        <!-- Data Fields -->
                        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">الاسم الأول <span class="text-red-500">*</span></label>
                                    <input type="text" name="first_name" x-model="form.first_name" required class="w-full px-4 py-3 bg-gray-50 border border-transparent focus:border-teal-custom/50 focus:bg-white rounded-xl text-sm transition font-bold">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">اسم الأب</label>
                                    <input type="text" name="father_name" x-model="form.father_name" class="w-full px-4 py-3 bg-gray-50 border border-transparent focus:border-teal-custom/50 focus:bg-white rounded-xl text-sm transition font-bold">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">اسم الجد</label>
                                    <input type="text" name="grandfather_name" x-model="form.grandfather_name" class="w-full px-4 py-3 bg-gray-50 border border-transparent focus:border-teal-custom/50 focus:bg-white rounded-xl text-sm transition font-bold">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">اسم العائلة <span class="text-red-500">*</span></label>
                                    <input type="text" name="family_name" x-model="form.family_name" required class="w-full px-4 py-3 bg-gray-50 border border-transparent focus:border-teal-custom/50 focus:bg-white rounded-xl text-sm transition font-bold">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">الجنس</label>
                                    <select name="gender" x-model="form.gender" class="w-full px-4 py-3 bg-gray-50 border border-transparent focus:border-teal-custom/50 focus:bg-white rounded-xl text-sm transition font-bold">
                                        <option value="ذكر">ذكر</option>
                                        <option value="أنثى">أنثى</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">تاريخ الميلاد</label>
                                    <input type="date" name="date_of_birth" x-model="form.date_of_birth" class="w-full px-4 py-3 bg-gray-50 border border-transparent focus:border-teal-custom/50 focus:bg-white rounded-xl text-sm transition font-bold text-right">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">الجنسية</label>
                                    <input type="text" name="nationality" x-model="form.nationality" class="w-full px-4 py-3 bg-gray-50 border border-transparent focus:border-teal-custom/50 focus:bg-white rounded-xl text-sm transition font-bold text-right">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">نوع الهوية</label>
                                    <select name="id_type" x-model="form.id_type" class="w-full px-4 py-3 bg-gray-50 border border-transparent focus:border-teal-custom/50 focus:bg-white rounded-xl text-sm transition font-bold">
                                        <option value="هوية وطنية">هوية وطنية</option>
                                        <option value="إقامة">إقامة</option>
                                        <option value="جواز سفر">جواز سفر</option>
                                    </select>
                                </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">رقم الهوية / الإقامة</label>
                                    <input type="text" name="id_number" x-model="form.id_number" dir="ltr" class="w-full px-4 py-3 bg-gray-50 border border-transparent focus:border-teal-custom/50 focus:bg-white rounded-xl text-sm transition font-bold text-right">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">رقم الجوال</label>
                                    <input type="text" name="mobile" x-model="form.mobile" dir="ltr" class="w-full px-4 py-3 bg-gray-50 border border-transparent focus:border-teal-custom/50 focus:bg-white rounded-xl text-sm transition font-bold text-right">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">فصيلة الدم</label>
                                    <select name="blood_type" x-model="form.blood_type" dir="ltr" class="w-full px-4 py-3 bg-gray-50 border border-transparent focus:border-teal-custom/50 focus:bg-white rounded-xl text-sm transition font-bold text-right">
                                        <option value="">غير محدد</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">البريد الإلكتروني</label>
                                    <input type="email" name="email" x-model="form.email" dir="ltr" class="w-full px-4 py-3 bg-gray-50 border border-transparent focus:border-teal-custom/50 focus:bg-white rounded-xl text-sm transition font-bold text-right">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">ملاحظات طبية (حساسية، أمراض مزمنة...)</label>
                                <textarea name="notes" x-model="form.notes" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-transparent focus:border-teal-custom/50 focus:bg-white rounded-xl text-sm transition font-bold resize-none"></textarea>
                            </div>
                        </div>

                    </div>

                    <div class="bg-white px-6 py-4 border-t border-gray-100 flex justify-end gap-3 items-center">
                        <button type="button" @click="isModalOpen = false" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl text-sm font-bold transition">إلغاء</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-sm transition flex items-center gap-2">
                            <i class="fa-solid fa-save"></i> حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Print Only Header -->
<div class="hidden print:block print-header border-b-2 border-gray-800 pb-4 mb-6 mt-0">
    <div class="flex justify-between items-center">
        <div>
            @php
                $appName = \App\Models\Setting::where('key', 'app_name')->value('value') ?? 'نظام عياداتي';
            @endphp
            <h1 class="text-2xl font-black text-gray-900">{{ $appName }}</h1>
            <p class="text-sm font-bold text-gray-600 mt-1">الملف الطبي الشامل للمريض</p>
        </div>
        <div class="text-left text-sm text-gray-500 font-bold">
            <p>تاريخ الطباعة: {{ date('Y-m-d H:i') }}</p>
            <p>رقم الملف (MRN): {{ $patient->mrn }}</p>
        </div>
    </div>
</div>

<style>
    @media print {
        @page {
            size: A4;
            margin: 1cm;
        }
        
        /* Remove specific flex/height rules from layout for print */
        body, html {
            height: auto !important;
            overflow: visible !important;
            background-color: white !important;
        }
        
        /* Hide UI elements within the content area */
        .print-hidden, button, a[href*="patients"], 
        .overflow-x-auto.custom-scrollbar, [x-show="isModalOpen"],
        .flex.gap-2.relative.z-10 {
            display: none !important;
        }
        
        /* Show print header */
        .print\:block {
            display: block !important;
        }
        
        /* Override tailwind colors for print */
        .bg-white { background: transparent !important; }
        .bg-teal-50 { background: #f0fdfa !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .bg-gray-50 { background: #f8fafc !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .bg-yellow-50 { background: #fefce8 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        
        /* Force text colors */
        h1, h2, h3, p, span {
            color: black !important;
        }
        
        /* Remove shadows */
        * {
            box-shadow: none !important;
        }
        
        /* Force borders */
        .border, .border-b, .border-t {
            border-color: #ddd !important;
        }
        
        /* Make sure tabs content is visible sequentially */
        [x-show="activeTab === 'overview'"],
        [x-show="activeTab === 'visits'"],
        [x-show="activeTab === 'appointments'"] {
            display: block !important;
            page-break-inside: avoid;
            margin-bottom: 2rem;
            opacity: 1 !important;
        }
        
        /* Layout fixes for print */
        .min-h-[400px] { min-height: 0 !important; }
        
        /* Make patient header fit print */
        .absolute.top-0.right-0.w-32.h-32 {
            display: none !important;
        }
        
        /* Hide "starts visit" buttons container */
        .flex-col.gap-2.w-full {
            display: none !important;
        }
    }
</style>

@endsection

@section('scripts')
<script>
function editPatientModal() {
    return {
        isModalOpen: false,
        photoPreview: '{{ $patient->avatar ? asset("storage/" . $patient->avatar) : null }}',
        form: {
            first_name: '{{ $patient->first_name }}',
            father_name: '{{ $patient->father_name }}',
            grandfather_name: '{{ $patient->grandfather_name }}',
            family_name: '{{ $patient->family_name }}',
            gender: '{{ $patient->gender == "M" ? "ذكر" : ($patient->gender == "F" ? "أنثى" : $patient->gender) }}',
            date_of_birth: '{{ $patient->date_of_birth ? substr($patient->date_of_birth, 0, 10) : "" }}',
            id_type: '{{ $patient->id_type }}',
            id_number: '{{ $patient->id_number }}',
            mobile: '{{ $patient->mobile }}',
            nationality: '{{ $patient->nationality }}',
            blood_type: '{{ $patient->blood_type }}',
            email: '{{ $patient->email }}',
            notes: `{!! addslashes($patient->notes) !!}`
        },
        updatePhotoPreview(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.photoPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    }
}
</script>
@endsection
