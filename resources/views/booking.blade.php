<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حجز موعد أونلاين - فكرتك الرقمية</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f8fafc;
        }
        .text-teal-custom { color: #0b4a60; }
        .bg-teal-custom { background-color: #0b4a60; }
        .border-teal-custom { border-color: #0b4a60; }
        .bg-light-blue-custom { background-color: #e5f0f4; }
    </style>
</head>
<body class="min-h-screen py-10 px-4">
    
    <div class="max-w-4xl mx-auto" x-data="{
        step: 1,
        patient: {
            firstName: '',
            lastName: '',
            gender: 'ذكر',
            mobile: '',
            idType: 'هوية وطنية',
            idNumber: ''
        },
        appointment: {
            clinic: 'الباطنة',
            date: '',
            doctor: '',
            time: '20:00 - 20:20',
            fee: '200 ر.س + ضريبة'
        },
        paymentMethod: 'arrival'
    }">
        
        <!-- Header -->
        <div class="text-right mb-8">
            <h1 class="text-3xl font-bold text-teal-custom mb-2">احجز موعدك أونلاين</h1>
            <p class="text-gray-500 text-sm">نفس أطباء وعيادات النظام — يظهر الطبيب فقط إذا كان متاحاً في التاريخ المختار.</p>
        </div>

        <!-- Stepper -->
        <div class="flex gap-4 mb-6">
            <div class="flex-1 text-center py-3 rounded-xl border text-sm font-medium transition-colors" 
                 :class="step === 1 ? 'bg-light-blue-custom border-teal-custom text-teal-custom' : 'bg-white border-gray-200 text-gray-500'">
                1. بياناتك
            </div>
            <div class="flex-1 text-center py-3 rounded-xl border text-sm font-medium transition-colors" 
                 :class="step === 2 ? 'bg-light-blue-custom border-teal-custom text-teal-custom' : 'bg-white border-gray-200 text-gray-500'">
                2. العيادة والطبيب
            </div>
            <div class="flex-1 text-center py-3 rounded-xl border text-sm font-medium transition-colors" 
                 :class="step === 3 ? 'bg-light-blue-custom border-teal-custom text-teal-custom' : 'bg-white border-gray-200 text-gray-500'">
                3. تأكيد الحجز
            </div>
        </div>

        <!-- Form Container -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            
            <!-- Step 1: Patient Data -->
            <div x-show="step === 1" x-transition.opacity>
                <h2 class="text-xl font-bold text-teal-custom mb-6">بيانات المريض</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">الاسم الأول</label>
                        <input type="text" x-model="patient.firstName" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right" placeholder="الاسم الأول">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">اسم العائلة</label>
                        <input type="text" x-model="patient.lastName" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right" placeholder="اسم العائلة">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">الجنس</label>
                        <select x-model="patient.gender" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right bg-white appearance-none">
                            <option value="ذكر">ذكر</option>
                            <option value="أنثى">أنثى</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">رقم الجوال</label>
                        <input type="text" x-model="patient.mobile" dir="ltr" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right" placeholder="05xxxxxxxx">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">نوع الهوية</label>
                        <select x-model="patient.idType" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right bg-white appearance-none">
                            <option value="هوية وطنية">هوية وطنية</option>
                            <option value="إقامة">إقامة</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">رقم الهوية / الإقامة</label>
                        <input type="text" x-model="patient.idNumber" dir="ltr" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right" placeholder="10xxxxxxxx">
                    </div>
                </div>

                <div class="mt-8 flex justify-start">
                    <button @click="step = 2" class="bg-teal-custom hover:bg-teal-700 text-white font-medium py-2 px-8 rounded-lg transition duration-200">
                        التالي
                    </button>
                </div>
            </div>

            <!-- Step 2: Clinic & Doctor -->
            <div x-show="step === 2" x-transition.opacity style="display: none;">
                <h2 class="text-xl font-bold text-teal-custom mb-6">اختر العيادة والموعد</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">العيادة / القسم</label>
                        <select x-model="appointment.clinic" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right bg-white appearance-none">
                            <option value="كل العيادات">كل العيادات</option>
                            <option value="الباطنة">الباطنة</option>
                            <option value="الأسنان">الأسنان</option>
                            <option value="الأطفال">الأطفال</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">التاريخ</label>
                        <input type="date" x-model="appointment.date" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right bg-white">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-2">الطبيب</label>
                        <select x-model="appointment.doctor" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom text-right bg-white appearance-none">
                            <option value="">اختر الطبيب</option>
                            <option value="د. منى القحطاني">د. منى القحطاني</option>
                            <option value="د. أحمد عبدالله">د. أحمد عبدالله</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-600 mb-2">الفترات المتاحة</label>
                    <div class="border border-gray-200 rounded-lg p-6 text-center bg-gray-50 text-gray-400 text-sm" x-show="!appointment.doctor">
                        اختر طبيباً لعرض الفترات
                    </div>
                    <div class="grid grid-cols-3 gap-3" x-show="appointment.doctor" style="display: none;">
                        <!-- Mocked slots -->
                        <button type="button" @click="appointment.time = '10:00 - 10:20'" class="py-2 border rounded-lg text-sm" :class="appointment.time === '10:00 - 10:20' ? 'border-teal-custom bg-light-blue-custom text-teal-custom' : 'border-gray-200 text-gray-600 hover:bg-gray-50'">10:00 - 10:20</button>
                        <button type="button" @click="appointment.time = '20:00 - 20:20'" class="py-2 border rounded-lg text-sm" :class="appointment.time === '20:00 - 20:20' ? 'border-teal-custom bg-light-blue-custom text-teal-custom' : 'border-gray-200 text-gray-600 hover:bg-gray-50'">20:00 - 20:20</button>
                    </div>
                </div>

                <div class="mt-8 flex justify-between items-center">
                    <button @click="step = 3" class="bg-teal-custom hover:bg-teal-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200">
                        مراجعة الحجز
                    </button>
                    <button @click="step = 1" class="border border-gray-300 text-gray-600 hover:bg-gray-50 font-medium py-2 px-6 rounded-lg transition duration-200">
                        رجوع
                    </button>
                </div>
            </div>

            <!-- Step 3: Confirmation -->
            <div x-show="step === 3" x-transition.opacity style="display: none;">
                <h2 class="text-xl font-bold text-teal-custom mb-6">تأكيد البيانات</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-xl flex justify-between">
                        <span class="text-gray-500 text-sm">المريض</span>
                        <span x-text="patient.firstName + ' ' + patient.lastName" class="font-medium text-gray-800"></span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl flex justify-between">
                        <span class="text-gray-500 text-sm">الجوال</span>
                        <span x-text="patient.mobile" class="font-medium text-gray-800" dir="ltr"></span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl flex justify-between">
                        <span class="text-gray-500 text-sm">الطبيب</span>
                        <span x-text="appointment.doctor" class="font-medium text-gray-800"></span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl flex justify-between">
                        <span class="text-gray-500 text-sm">الهوية</span>
                        <span x-text="patient.idNumber" class="font-medium text-gray-800" dir="ltr"></span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl flex justify-between">
                        <span class="text-gray-500 text-sm">التاريخ</span>
                        <span x-text="appointment.date" class="font-medium text-gray-800" dir="ltr"></span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl flex justify-between">
                        <span class="text-gray-500 text-sm">العيادة</span>
                        <span x-text="appointment.clinic" class="font-medium text-gray-800"></span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl flex justify-between">
                        <span class="text-gray-500 text-sm">رسوم الكشف</span>
                        <span x-text="appointment.fee" class="font-medium text-gray-800"></span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl flex justify-between">
                        <span class="text-gray-500 text-sm">الوقت</span>
                        <span x-text="appointment.time" class="font-medium text-gray-800" dir="ltr"></span>
                    </div>
                </div>

                <div class="mt-8">
                    <label class="block text-sm font-medium text-gray-600 mb-3">طريقة الدفع</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <!-- Pay on Arrival -->
                        <div @click="paymentMethod = 'arrival'" class="cursor-pointer border-2 rounded-xl p-4 transition-colors"
                             :class="paymentMethod === 'arrival' ? 'border-teal-custom bg-light-blue-custom/30' : 'border-gray-200 bg-white'">
                            <div class="font-bold text-teal-custom text-lg mb-1">الدفع عند الحضور</div>
                            <div class="text-gray-500 text-sm">نقداً أو شبكة في الاستقبال — يُنشأ كشف مالي غير مسدد</div>
                        </div>

                        <!-- Online Payment (Disabled) -->
                        <div class="border border-gray-200 rounded-xl p-4 bg-gray-50 opacity-60 cursor-not-allowed">
                            <div class="font-bold text-gray-600 text-lg mb-1">دفع إلكتروني (مدى / فيزا)</div>
                            <div class="text-gray-400 text-sm">غير مفعل بعد — يحتاج ربط بوابة دفع (Moyasar / Tap / HyperPay)</div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-between items-center">
                    <button @click="alert('تم تأكيد الحجز بنجاح!')" class="bg-teal-custom hover:bg-teal-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200">
                        تأكيد الحجز
                    </button>
                    <button @click="step = 2" class="border border-gray-300 text-gray-600 hover:bg-gray-50 font-medium py-2 px-6 rounded-lg transition duration-200">
                        رجوع
                    </button>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
