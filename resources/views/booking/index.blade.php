<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حجز موعد أونلاين - نظام العيادات</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    @php $appFont = \App\Models\Setting::where("key", "app_font")->value("value") ?? "Tajawal"; $fontFamily = $appFont === "Cairo" ? "Cairo" : "Tajawal"; @endphp @if($fontFamily === "Cairo") <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"> @else <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet"> @endif
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f7ff',
                            100: '#e0f0fe',
                            500: '#1565c0', // Deep blue from mockup
                            600: '#0d47a1',
                            900: '#0a2a5e'
                        }
                    },
                    fontFamily: {
                        sans: ['{{ $fontFamily }}', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: '{{ $fontFamily }}', sans-serif; background-color: #f8fafc; }
        [x-cloak] { display: none !important; }
        .wizard-step-active { background-color: #e0f0fe; color: #1565c0; border-color: #1565c0; font-weight: 800; }
        .wizard-step-completed { background-color: #f8fafc; color: #64748b; border-color: #e2e8f0; }
        .wizard-step-pending { background-color: white; color: #94a3b8; border-color: #e2e8f0; }
    </style>
</head>
<body class="min-h-screen bg-gray-50/50 flex flex-col items-center py-10 px-4">

    <!-- Header -->
    <div class="max-w-4xl w-full text-right mb-8">
        <h1 class="text-3xl font-black text-brand-900 mb-2">احجز موعدك أونلاين</h1>
        <p class="text-gray-500 font-bold text-sm">نفس أطباء وعيادات النظام — يظهر الطبيب فقط إذا كان متاحاً في التاريخ المختار.</p>
    </div>

    <!-- Booking Wizard Component -->
    <div x-data="bookingWizard()" x-init="init()" class="max-w-4xl w-full">
        
        <!-- Wizard Navigation -->
        <div class="flex flex-col md:flex-row gap-3 mb-8">
            <div :class="step === 1 ? 'wizard-step-active' : (step > 1 ? 'wizard-step-completed' : 'wizard-step-pending')" class="flex-1 py-3 px-4 rounded-xl border text-center transition-all duration-300">
                1. بياناتك
            </div>
            <div :class="step === 2 ? 'wizard-step-active' : (step > 2 ? 'wizard-step-completed' : 'wizard-step-pending')" class="flex-1 py-3 px-4 rounded-xl border text-center transition-all duration-300">
                2. العيادة والطبيب
            </div>
            <div :class="step === 3 ? 'wizard-step-active' : 'wizard-step-pending'" class="flex-1 py-3 px-4 rounded-xl border text-center transition-all duration-300">
                3. تأكيد الحجز
            </div>
        </div>

        <!-- Success Screen -->
        <div x-show="isSuccess" x-cloak class="bg-white rounded-3xl p-12 shadow-xl shadow-brand-500/5 border border-gray-100 text-center">
            <div class="w-24 h-24 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-5xl mx-auto mb-6">
                <i class="fa-solid fa-check"></i>
            </div>
            <h2 class="text-3xl font-black text-gray-800 mb-4">تم تأكيد حجزك بنجاح!</h2>
            <p class="text-gray-500 font-bold mb-8">رقم حجزك هو <span class="font-mono text-brand-600 bg-brand-50 px-2 py-1 rounded" x-text="bookingId"></span>. نرجو الحضور قبل الموعد بـ 15 دقيقة.</p>
            
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-bold transition">
                العودة للصفحة الرئيسية
            </a>
        </div>

        <!-- Error Alert -->
        <div x-show="errorMessage" x-cloak class="bg-red-50 border-r-4 border-red-500 text-red-700 p-4 rounded-xl mb-6 font-bold flex items-center justify-between shadow-sm">
            <span x-text="errorMessage"></span>
            <button @click="errorMessage = ''" class="text-red-500 hover:text-red-800"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <!-- Forms Container -->
        <div x-show="!isSuccess" class="bg-white rounded-3xl p-6 md:p-10 shadow-xl shadow-brand-500/5 border border-gray-100">
            
            <!-- STEP 1: Patient Info -->
            <div x-show="step === 1" x-transition.opacity.duration.300ms>
                <div class="flex justify-between items-center mb-8 border-b border-gray-100 pb-4">
                    <h2 class="text-xl font-black text-brand-900">بيانات المريض</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2">الاسم الأول <span class="text-red-500">*</span></label>
                        <input type="text" x-model="form.first_name" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-500/10 rounded-xl text-sm font-bold transition outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2">اسم العائلة <span class="text-red-500">*</span></label>
                        <input type="text" x-model="form.last_name" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-500/10 rounded-xl text-sm font-bold transition outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2">الجنس <span class="text-red-500">*</span></label>
                        <select x-model="form.gender" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-500/10 rounded-xl text-sm font-bold transition outline-none appearance-none">
                            <option value="">اختر الجنس...</option>
                            <option value="ذكر">ذكر</option>
                            <option value="أنثى">أنثى</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2">رقم الجوال <span class="text-red-500">*</span></label>
                        <input type="text" x-model="form.phone" placeholder="05xxxxxxxx" dir="ltr" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-500/10 rounded-xl text-sm font-bold transition outline-none text-right">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2">نوع الهوية <span class="text-red-500">*</span></label>
                        <select x-model="form.id_type" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-500/10 rounded-xl text-sm font-bold transition outline-none appearance-none">
                            <option value="هوية وطنية">هوية وطنية</option>
                            <option value="إقامة">إقامة</option>
                            <option value="جواز سفر">جواز سفر</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2">رقم الهوية / الإقامة <span class="text-red-500">*</span></label>
                        <input type="text" x-model="form.id_number" placeholder="1xxxxxxxxx" dir="ltr" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-500/10 rounded-xl text-sm font-bold transition outline-none text-right">
                    </div>
                </div>

                <div class="mt-10 flex justify-end">
                    <button @click="validateStep1()" class="bg-brand-600 hover:bg-brand-700 text-white px-10 py-3.5 rounded-xl text-sm font-black shadow-lg shadow-brand-500/30 transition">
                        التالي
                    </button>
                </div>
            </div>

            <!-- STEP 2: Clinic & Doctor -->
            <div x-show="step === 2" x-cloak x-transition.opacity.duration.300ms>
                <div class="flex justify-between items-center mb-8 border-b border-gray-100 pb-4">
                    <h2 class="text-xl font-black text-brand-900">اختر العيادة والموعد</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2">العيادة / القسم <span class="text-red-500">*</span></label>
                        <select x-model="form.clinic_id" @change="fetchDoctors()" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-500/10 rounded-xl text-sm font-bold transition outline-none appearance-none">
                            <option value="">كل العيادات</option>
                            @foreach($clinics as $clinic)
                                <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2">التاريخ <span class="text-red-500">*</span></label>
                        <input type="date" x-model="form.date" @change="fetchSlots()" min="{{ date('Y-m-d') }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-500/10 rounded-xl text-sm font-bold transition outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 mb-2">الطبيب <span class="text-red-500">*</span></label>
                        <select x-model="form.doctor_id" @change="fetchSlots()" :disabled="!form.clinic_id || isLoadingDoctors" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-500/10 rounded-xl text-sm font-bold transition outline-none appearance-none disabled:opacity-50">
                            <option value="">اختر الطبيب</option>
                            <template x-for="doc in doctorsList" :key="doc.id">
                                <option :value="doc.id" x-text="'د. ' + doc.name + (doc.specialization ? ' — ' + doc.specialization : '')"></option>
                            </template>
                        </select>
                        <p x-show="isLoadingDoctors" class="text-xs text-brand-500 mt-1 font-bold animate-pulse">جاري تحميل الأطباء...</p>
                    </div>
                </div>

                <!-- Time Slots -->
                <div class="mb-8 border-t border-gray-100 pt-6">
                    <h3 class="text-sm font-black text-gray-800 mb-4">الفترات المتاحة</h3>
                    
                    <div x-show="!form.doctor_id || !form.date" class="bg-gray-50 p-4 rounded-xl text-center text-sm font-bold text-gray-400">
                        اختر طبيباً وتاريخاً لعرض الفترات المتاحة
                    </div>

                    <div x-show="isLoadingSlots" class="text-center py-8">
                        <i class="fa-solid fa-circle-notch fa-spin text-brand-500 text-3xl"></i>
                    </div>

                    <div x-show="form.doctor_id && form.date && !isLoadingSlots && slots.length === 0" class="bg-red-50 text-red-600 p-4 rounded-xl text-center text-sm font-bold border border-red-100">
                        عفواً، لا توجد فترات متاحة لهذا الطبيب في التاريخ المختار.
                    </div>

                    <div x-show="form.doctor_id && form.date && !isLoadingSlots && slots.length > 0" class="flex flex-wrap gap-3">
                        <template x-for="timeSlot in slots" :key="timeSlot">
                            <button @click="form.time = timeSlot" type="button" 
                                    :class="form.time === timeSlot ? 'bg-brand-50 border-brand-500 text-brand-700 ring-2 ring-brand-500/30' : 'bg-white border-gray-200 text-gray-600 hover:border-brand-300 hover:bg-brand-50/50'" 
                                    class="px-5 py-2.5 rounded-xl border font-mono font-bold text-sm transition shadow-sm">
                                <span x-text="timeSlot"></span>
                            </button>
                        </template>
                    </div>
                    
                    <p x-show="showSlotError" x-cloak class="text-red-500 text-xs font-bold mt-2"><i class="fa-solid fa-circle-exclamation"></i> يرجى اختيار فترة الحجز للمتابعة.</p>
                </div>

                <div class="mt-10 flex justify-between">
                    <button @click="step = 1" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-8 py-3.5 rounded-xl text-sm font-bold transition">
                        رجوع
                    </button>
                    <button @click="validateStep2()" class="bg-brand-600 hover:bg-brand-700 text-white px-10 py-3.5 rounded-xl text-sm font-black shadow-lg shadow-brand-500/30 transition">
                        مراجعة الحجز
                    </button>
                </div>
            </div>

            <!-- STEP 3: Confirmation -->
            <div x-show="step === 3" x-cloak x-transition.opacity.duration.300ms>
                <div class="flex justify-between items-center mb-8 border-b border-gray-100 pb-4">
                    <h2 class="text-xl font-black text-brand-900">تأكيد البيانات</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                    <div class="bg-gray-50 p-4 rounded-xl flex justify-between items-center border border-gray-100">
                        <span class="text-xs font-bold text-gray-400">المريض</span>
                        <span class="text-sm font-black text-gray-800" x-text="form.first_name + ' ' + form.last_name"></span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl flex justify-between items-center border border-gray-100">
                        <span class="text-xs font-bold text-gray-400">الجوال</span>
                        <span class="text-sm font-black text-gray-800 font-mono" x-text="form.phone"></span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl flex justify-between items-center border border-gray-100">
                        <span class="text-xs font-bold text-gray-400">الهوية</span>
                        <span class="text-sm font-black text-gray-800 font-mono" x-text="form.id_number"></span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl flex justify-between items-center border border-gray-100">
                        <span class="text-xs font-bold text-gray-400">الطبيب</span>
                        <span class="text-sm font-black text-brand-700" x-text="'د. ' + getDoctorName()"></span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl flex justify-between items-center border border-gray-100">
                        <span class="text-xs font-bold text-gray-400">العيادة</span>
                        <span class="text-sm font-black text-gray-800" x-text="getClinicName()"></span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl flex justify-between items-center border border-gray-100">
                        <span class="text-xs font-bold text-gray-400">التاريخ والوقت</span>
                        <div class="text-left">
                            <div class="text-sm font-black text-gray-800 font-mono" dir="ltr" x-text="form.date"></div>
                            <div class="text-xs font-black text-brand-600 font-mono mt-0.5" dir="ltr" x-text="form.time"></div>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-sm font-black text-gray-800 mb-3">طريقة الدفع</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="relative flex flex-col p-5 bg-brand-50 border-2 border-brand-500 rounded-2xl cursor-pointer">
                            <span class="font-black text-brand-900 mb-1">الدفع عند الحضور</span>
                            <span class="text-xs font-bold text-brand-600">نقداً أو شبكة في الاستقبال — يُنشأ كشف مالي غير مسدد</span>
                            <div class="absolute top-5 left-5 w-4 h-4 rounded-full border-4 border-brand-500 bg-white"></div>
                        </label>
                        
                        <label class="relative flex flex-col p-5 bg-gray-50 border-2 border-gray-200 rounded-2xl cursor-not-allowed opacity-50">
                            <span class="font-black text-gray-500 mb-1">دفع إلكتروني (مدى / فيزا)</span>
                            <span class="text-xs font-bold text-gray-400">غير مفعل بعد — يحتاج ربط بوابة دفع (Moyasar / HyperPay)</span>
                            <div class="absolute top-5 left-5 w-4 h-4 rounded-full border border-gray-300"></div>
                        </label>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-100 p-5 rounded-2xl flex justify-between items-center mb-8">
                    <span class="font-black text-blue-900">رسوم الكشف التقديرية</span>
                    <span class="text-xl font-black text-blue-700 font-mono" dir="ltr">{{ $fee }} {{ $global_settings['currency'] ?? 'SAR' }}</span>
                </div>

                <div class="flex justify-between items-center border-t border-gray-100 pt-6">
                    <button @click="step = 2" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-8 py-3.5 rounded-xl text-sm font-bold transition" :disabled="isSubmitting">
                        رجوع
                    </button>
                    <button @click="submitBooking()" :disabled="isSubmitting" class="bg-brand-600 hover:bg-brand-700 disabled:bg-gray-400 text-white px-10 py-3.5 rounded-xl text-base font-black shadow-lg shadow-brand-500/30 transition flex items-center gap-2">
                        <span x-show="!isSubmitting">تأكيد الحجز النهائي</span>
                        <span x-show="isSubmitting"><i class="fa-solid fa-circle-notch fa-spin"></i> جاري التأكيد...</span>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <script>
        function bookingWizard() {
            return {
                step: 1,
                isSuccess: false,
                isSubmitting: false,
                errorMessage: '',
                bookingId: '',
                
                doctorsList: [],
                slots: [],
                isLoadingDoctors: false,
                isLoadingSlots: false,
                showSlotError: false,

                clinicsData: @json($clinics),

                form: {
                    first_name: '',
                    last_name: '',
                    phone: '',
                    gender: '',
                    id_type: 'هوية وطنية',
                    id_number: '',
                    
                    clinic_id: '',
                    doctor_id: '',
                    date: '',
                    time: ''
                },

                init() {
                    // Initialization if needed
                },

                validateStep1() {
                    if(!this.form.first_name || !this.form.last_name || !this.form.phone || !this.form.id_number || !this.form.gender) {
                        this.errorMessage = 'يرجى تعبئة جميع الحقول المطلوبة (المميزة بنجمة حمراء).';
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        return;
                    }
                    this.errorMessage = '';
                    this.step = 2;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                validateStep2() {
                    if(!this.form.clinic_id || !this.form.doctor_id || !this.form.date) {
                        this.errorMessage = 'يرجى اختيار العيادة، الطبيب، والتاريخ.';
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        return;
                    }
                    if(!this.form.time) {
                        this.showSlotError = true;
                        return;
                    }
                    this.showSlotError = false;
                    this.errorMessage = '';
                    this.step = 3;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                async fetchDoctors() {
                    this.form.doctor_id = '';
                    this.form.time = '';
                    this.slots = [];
                    if(!this.form.clinic_id) {
                        this.doctorsList = [];
                        return;
                    }
                    
                    this.isLoadingDoctors = true;
                    try {
                        const response = await fetch(`/api/public/clinics/${this.form.clinic_id}/doctors`);
                        this.doctorsList = await response.json();
                    } catch (error) {
                        console.error('Error fetching doctors:', error);
                    }
                    this.isLoadingDoctors = false;
                },

                async fetchSlots() {
                    this.form.time = '';
                    if(!this.form.doctor_id || !this.form.date) {
                        this.slots = [];
                        return;
                    }
                    
                    this.isLoadingSlots = true;
                    try {
                        const response = await fetch(`/api/public/doctors/${this.form.doctor_id}/slots?date=${this.form.date}`);
                        this.slots = await response.json();
                    } catch (error) {
                        console.error('Error fetching slots:', error);
                        this.slots = [];
                    }
                    this.isLoadingSlots = false;
                },

                getDoctorName() {
                    const doc = this.doctorsList.find(d => d.id == this.form.doctor_id);
                    return doc ? doc.name : '';
                },

                getClinicName() {
                    const clinic = this.clinicsData.find(c => c.id == this.form.clinic_id);
                    return clinic ? clinic.name : '';
                },

                async submitBooking() {
                    this.isSubmitting = true;
                    this.errorMessage = '';
                    
                    try {
                        const response = await fetch('{{ route("booking.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.form)
                        });
                        
                        const data = await response.json();
                        
                        if(response.ok && data.success) {
                            this.bookingId = '#' + String(data.appointment_id).padStart(5, '0');
                            this.isSuccess = true;
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        } else {
                            this.errorMessage = data.message || 'حدث خطأ غير متوقع.';
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }
                    } catch(error) {
                        this.errorMessage = 'فشل الاتصال بالخادم. حاول مرة أخرى.';
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                    
                    this.isSubmitting = false;
                }
            }
        }
    </script>
</body>
</html>
