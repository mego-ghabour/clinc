@extends('layouts.app')

@section('title', 'إعدادات النظام - Clinc')
@section('header', 'إعدادات النظام وتخصيص العيادة')

@section('content')
<div class="space-y-6" x-data="{ activeTab: '{{ session('backup_tab') ? 'backup' : 'general' }}' }">

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-xl flex items-center justify-center text-2xl shadow-sm border border-teal-100">
                <i class="fa-solid fa-sliders"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-gray-800">إعدادات النظام الشاملة</h2>
                <p class="text-sm text-gray-500 font-bold mt-1">تخصيص التوقيت، العملات، الهوية البصرية، الإعدادات المالية، والمواعيد والنسخ الاحتياطي.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-r-4 border-green-500 text-green-700 p-4 rounded-xl flex items-center gap-3 shadow-sm" x-data="{ show: true }" x-show="show">
            <i class="fa-solid fa-circle-check text-xl"></i>
            <p class="font-bold text-sm">{{ session('success') }}</p>
            <button @click="show = false" class="mr-auto hover:text-green-900"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border-r-4 border-red-500 text-red-700 p-4 rounded-xl flex items-center gap-3 shadow-sm" x-data="{ show: true }" x-show="show">
            <i class="fa-solid fa-circle-exclamation text-xl"></i>
            <div>
                <p class="font-bold text-sm">{{ $errors->first() }}</p>
            </div>
            <button @click="show = false" class="mr-auto hover:text-red-900"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col md:flex-row">
        
        <!-- Tabs Sidebar -->
        <div class="w-full md:w-64 bg-gray-50 border-b md:border-b-0 md:border-l border-gray-100 p-4 shrink-0 overflow-x-auto md:overflow-visible flex md:flex-col gap-2 custom-scrollbar">
            <button @click="activeTab = 'general'" :class="activeTab === 'general' ? 'bg-white text-teal-600 shadow-sm border-teal-200' : 'text-gray-500 hover:bg-gray-100 border-transparent'" class="border whitespace-nowrap text-right px-4 py-3 rounded-xl text-sm font-bold transition flex items-center gap-3 shrink-0">
                <i class="fa-solid fa-clock w-5 text-center"></i> بيانات العيادة والتوقيت
            </button>
            <button @click="activeTab = 'financial'" :class="activeTab === 'financial' ? 'bg-white text-teal-600 shadow-sm border-teal-200' : 'text-gray-500 hover:bg-gray-100 border-transparent'" class="border whitespace-nowrap text-right px-4 py-3 rounded-xl text-sm font-bold transition flex items-center gap-3 shrink-0">
                <i class="fa-solid fa-coins w-5 text-center"></i> العملة والمالية والضرائب
            </button>
            <button @click="activeTab = 'appointments'" :class="activeTab === 'appointments' ? 'bg-white text-teal-600 shadow-sm border-teal-200' : 'text-gray-500 hover:bg-gray-100 border-transparent'" class="border whitespace-nowrap text-right px-4 py-3 rounded-xl text-sm font-bold transition flex items-center gap-3 shrink-0">
                <i class="fa-solid fa-calendar-check w-5 text-center"></i> المواعيد والحجز
            </button>
            <button @click="activeTab = 'branding'" :class="activeTab === 'branding' ? 'bg-white text-teal-600 shadow-sm border-teal-200' : 'text-gray-500 hover:bg-gray-100 border-transparent'" class="border whitespace-nowrap text-right px-4 py-3 rounded-xl text-sm font-bold transition flex items-center gap-3 shrink-0">
                <i class="fa-solid fa-palette w-5 text-center"></i> الهوية البصرية والمظهر
            </button>
            <button @click="activeTab = 'contact'" :class="activeTab === 'contact' ? 'bg-white text-teal-600 shadow-sm border-teal-200' : 'text-gray-500 hover:bg-gray-100 border-transparent'" class="border whitespace-nowrap text-right px-4 py-3 rounded-xl text-sm font-bold transition flex items-center gap-3 shrink-0">
                <i class="fa-solid fa-address-book w-5 text-center"></i> بيانات التواصل للطباعة
            </button>
            <button @click="activeTab = 'backup'" :class="activeTab === 'backup' ? 'bg-white text-teal-600 shadow-sm border-teal-200' : 'text-gray-500 hover:bg-gray-100 border-transparent'" class="border whitespace-nowrap text-right px-4 py-3 rounded-xl text-sm font-bold transition flex items-center gap-3 shrink-0">
                <i class="fa-solid fa-shield-halved w-5 text-center"></i> النسخ الاحتياطي والأمان
            </button>
        </div>

        <div class="flex-1 flex flex-col min-h-[550px]">
            <!-- General Settings Form -->
            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8 flex-1 flex flex-col" x-show="activeTab !== 'backup'">
                @csrf
                
                <!-- Tab 1: General (Clinic, Timezone, Locale, Format) -->
                <div x-show="activeTab === 'general'" x-transition.opacity class="flex-1 space-y-6">
                    <h3 class="text-lg font-black text-gray-800 pb-2 border-b border-gray-100">بيانات العيادة والتوقيت واللغة</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl">
                        <!-- Clinic Name -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">اسم المستشفى أو العيادة <span class="text-red-500">*</span></label>
                            <input type="text" name="app_name" value="{{ $settings['app_name']->value ?? 'العيادات الرقمية' }}" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom focus:bg-white transition text-sm font-bold">
                            <p class="text-[10px] font-bold text-gray-400 mt-1">يظهر هذا الاسم في أعلى لوحة التحكم، صفحات الحجز، والتقارير المطبوعة.</p>
                        </div>

                        <!-- Timezone Dropdown -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">المنطقة الزمنية (Timezone) <span class="text-red-500">*</span></label>
                            @php
                                $selectedTz = $settings['app_timezone']->value ?? 'Asia/Riyadh';
                            @endphp
                            <select name="app_timezone" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom focus:bg-white transition text-sm font-bold">
                                <optgroup label="المملكة ودول الخليج العربي">
                                    <option value="Asia/Riyadh" {{ $selectedTz == 'Asia/Riyadh' ? 'selected' : '' }}>🇸🇦 السعودية - الرياض ومكة المكرمة (GMT+3)</option>
                                    <option value="Asia/Dubai" {{ $selectedTz == 'Asia/Dubai' ? 'selected' : '' }}>🇦🇪 الإمارات - دبي وأبوظبي (GMT+4)</option>
                                    <option value="Asia/Kuwait" {{ $selectedTz == 'Asia/Kuwait' ? 'selected' : '' }}>🇰🇼 الكويت (GMT+3)</option>
                                    <option value="Asia/Qatar" {{ $selectedTz == 'Asia/Qatar' ? 'selected' : '' }}>🇶🇦 قطر - الدوحة (GMT+3)</option>
                                    <option value="Asia/Bahrain" {{ $selectedTz == 'Asia/Bahrain' ? 'selected' : '' }}>🇧🇭 البحرين - المنامة (GMT+3)</option>
                                    <option value="Asia/Muscat" {{ $selectedTz == 'Asia/Muscat' ? 'selected' : '' }}>🇴🇲 سلطنة عمان - مسقط (GMT+4)</option>
                                </optgroup>
                                <optgroup label="مصر والدول العربية">
                                    <option value="Africa/Cairo" {{ $selectedTz == 'Africa/Cairo' ? 'selected' : '' }}>🇪🇬 مصر - القاهرة (GMT+2 / +3)</option>
                                    <option value="Asia/Amman" {{ $selectedTz == 'Asia/Amman' ? 'selected' : '' }}>🇯🇴 الأردن - عمّان (GMT+3)</option>
                                    <option value="Asia/Baghdad" {{ $selectedTz == 'Asia/Baghdad' ? 'selected' : '' }}>🇮🇶 العراق - بغداد (GMT+3)</option>
                                    <option value="Africa/Tripoli" {{ $selectedTz == 'Africa/Tripoli' ? 'selected' : '' }}>🇱🇾 ليبيا - طرابلس (GMT+2)</option>
                                    <option value="Africa/Tunis" {{ $selectedTz == 'Africa/Tunis' ? 'selected' : '' }}>🇹🇳 تونس (GMT+1)</option>
                                    <option value="Africa/Algiers" {{ $selectedTz == 'Africa/Algiers' ? 'selected' : '' }}>🇩🇿 الجزائر (GMT+1)</option>
                                    <option value="Africa/Casablanca" {{ $selectedTz == 'Africa/Casablanca' ? 'selected' : '' }}>🇲🇦 المغرب - الدار البيضاء (GMT+1)</option>
                                </optgroup>
                                <optgroup label="التوقيت العالمي">
                                    <option value="UTC" {{ $selectedTz == 'UTC' ? 'selected' : '' }}>🌐 التوقيت العالمي المنسق (UTC 00:00)</option>
                                    <option value="Europe/London" {{ $selectedTz == 'Europe/London' ? 'selected' : '' }}>🇬🇧 بريطانيا - لندن (GMT+0)</option>
                                    <option value="America/New_York" {{ $selectedTz == 'America/New_York' ? 'selected' : '' }}>🇺🇸 أمريكا - نيويورك (EST)</option>
                                </optgroup>
                            </select>
                            <p class="text-[10px] font-bold text-gray-400 mt-1">يحدد وقت تسجيل الزيارات والمواعيد وسجل النشاطات.</p>
                        </div>

                        <!-- System Locale Dropdown -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">لغة النظام (System Locale)</label>
                            @php
                                $selectedLocale = $settings['app_locale']->value ?? 'ar';
                            @endphp
                            <select name="app_locale" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom focus:bg-white transition text-sm font-bold">
                                <option value="ar" {{ $selectedLocale == 'ar' ? 'selected' : '' }}>العربية (Arabic) - الافتراضية</option>
                                <option value="en" {{ $selectedLocale == 'en' ? 'selected' : '' }}>English (الإنجليزية)</option>
                            </select>
                            <p class="text-[10px] font-bold text-gray-400 mt-1">اللغة المعتمدة لواجهات النظام والرسائل البرمجية.</p>
                        </div>

                        <!-- Time Format -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">صيغة عرض الوقت (Time Format)</label>
                            @php
                                $timeFormat = $settings['time_format']->value ?? '12';
                            @endphp
                            <select name="time_format" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom focus:bg-white transition text-sm font-bold">
                                <option value="12" {{ $timeFormat == '12' ? 'selected' : '' }}>نظام 12 ساعة (مثال: 02:30 م / ص)</option>
                                <option value="24" {{ $timeFormat == '24' ? 'selected' : '' }}>نظام 24 ساعة (مثال: 14:30)</option>
                            </select>
                        </div>

                        <!-- Date Format -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">صيغة عرض التاريخ (Date Format)</label>
                            @php
                                $dateFormat = $settings['date_format']->value ?? 'Y-m-d';
                            @endphp
                            <select name="date_format" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom focus:bg-white transition text-sm font-bold font-mono">
                                <option value="Y-m-d" {{ $dateFormat == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (مثال: {{ date('Y-m-d') }})</option>
                                <option value="d/m/Y" {{ $dateFormat == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (مثال: {{ date('d/m/Y') }})</option>
                                <option value="d-m-Y" {{ $dateFormat == 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY (مثال: {{ date('d-m-Y') }})</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Financial & Currencies -->
                <div x-show="activeTab === 'financial'" x-transition.opacity class="flex-1 space-y-6" style="display: none;">
                    <h3 class="text-lg font-black text-gray-800 pb-2 border-b border-gray-100">الإعدادات المالية، العملات، والضرائب</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl">
                        
                        <!-- Currency Selection -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">العملة الافتراضية للنظام (Default Currency) <span class="text-red-500">*</span></label>
                            @php
                                $currentCurrency = $settings['currency']->value ?? 'SAR';
                            @endphp
                            <select name="currency" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom focus:bg-white transition text-sm font-bold">
                                <optgroup label="المملكة العربية السعودية ودول الخليج">
                                    <option value="SAR" {{ $currentCurrency == 'SAR' ? 'selected' : '' }}>🇸🇦 ريال سعودي (SAR) - الافتراضي</option>
                                    <option value="AED" {{ $currentCurrency == 'AED' ? 'selected' : '' }}>🇦🇪 درهم إماراتي (AED)</option>
                                    <option value="KWD" {{ $currentCurrency == 'KWD' ? 'selected' : '' }}>🇰🇼 دينار كويتي (KWD)</option>
                                    <option value="QAR" {{ $currentCurrency == 'QAR' ? 'selected' : '' }}>🇶🇦 ريال قطري (QAR)</option>
                                    <option value="BHD" {{ $currentCurrency == 'BHD' ? 'selected' : '' }}>🇧🇭 دينار بحريني (BHD)</option>
                                    <option value="OMR" {{ $currentCurrency == 'OMR' ? 'selected' : '' }}>🇴🇲 ريال عماني (OMR)</option>
                                </optgroup>
                                <optgroup label="مصر والدول العربية">
                                    <option value="EGP" {{ $currentCurrency == 'EGP' ? 'selected' : '' }}>🇪🇬 جنيه مصري (EGP)</option>
                                    <option value="JOD" {{ $currentCurrency == 'JOD' ? 'selected' : '' }}>🇯🇴 دينار أردني (JOD)</option>
                                </optgroup>
                                <optgroup label="العملات العالمية">
                                    <option value="USD" {{ $currentCurrency == 'USD' ? 'selected' : '' }}>🇺🇸 دولار أمريكي (USD $)</option>
                                    <option value="EUR" {{ $currentCurrency == 'EUR' ? 'selected' : '' }}>🇪🇺 يورو أوروبي (EUR €)</option>
                                    <option value="GBP" {{ $currentCurrency == 'GBP' ? 'selected' : '' }}>🇬🇧 جنيه إسترليني (GBP £)</option>
                                </optgroup>
                            </select>
                            <p class="text-[10px] font-bold text-gray-400 mt-1">تظهر هذه العملة على جميع الفواتير، أسعار الخدمات الطبية، وسندات القبض.</p>
                        </div>

                        <!-- VAT Number -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">الرقم الضريبي (VAT Number)</label>
                            <input type="text" name="vat_number" value="{{ $settings['vat_number']->value ?? '' }}" placeholder="مثال: 310000000000003" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom focus:bg-white transition text-sm font-bold font-mono" dir="ltr">
                            <p class="text-[10px] font-bold text-gray-400 mt-1">مطلوب للفوترة الضريبية ونظام هيئة الزكاة والضريبة.</p>
                        </div>

                        <!-- VAT Rate -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">نسبة ضريبة القيمة المضافة (%)</label>
                            <input type="number" step="0.01" name="vat_rate" value="{{ $settings['vat_rate']->value ?? '15' }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom focus:bg-white transition text-sm font-bold text-right" dir="ltr">
                        </div>

                        <!-- Invoice Prefix -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">بادئة ترقيم الفواتير (Invoice Prefix)</label>
                            <input type="text" name="invoice_prefix" value="{{ $settings['invoice_prefix']->value ?? 'INV-' }}" placeholder="INV-" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom focus:bg-white transition text-sm font-bold font-mono text-right" dir="ltr">
                            <p class="text-[10px] font-bold text-gray-400 mt-1">مثال: INV-1001 أو فاتورة-</p>
                        </div>

                        <!-- Invoice Terms & Footer Note -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">ملاحظات وشروط الفاتورة المطبوعة (Terms & Footer)</label>
                            <textarea name="invoice_terms" rows="2" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom focus:bg-white transition text-sm font-bold resize-none" placeholder="مثال: نتمنى لكم دوام الصحة والعافية - الفاتورة صالحة للمراجعة الطبية خلال 14 يوماً.">{{ $settings['invoice_terms']->value ?? 'نتمنى لكم دوام الصحة والعافية.' }}</textarea>
                            <p class="text-[10px] font-bold text-gray-400 mt-1">تظهر هذه الملاحظة أسفل كل فاتورة عند الطباعة.</p>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Appointments & Booking Settings -->
                <div x-show="activeTab === 'appointments'" x-transition.opacity class="flex-1 space-y-6" style="display: none;">
                    <h3 class="text-lg font-black text-gray-800 pb-2 border-b border-gray-100">إعدادات المواعيد والحجز الإلكتروني</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl">
                        
                        <!-- Default Consultation Duration -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">مدة الكشف الافتراضية (بالدقائق)</label>
                            @php
                                $duration = $settings['default_consultation_duration']->value ?? '20';
                            @endphp
                            <select name="default_consultation_duration" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom focus:bg-white transition text-sm font-bold">
                                <option value="10" {{ $duration == '10' ? 'selected' : '' }}>10 دقائق</option>
                                <option value="15" {{ $duration == '15' ? 'selected' : '' }}>15 دقيقة</option>
                                <option value="20" {{ $duration == '20' ? 'selected' : '' }}>20 دقيقة (الافتراضية)</option>
                                <option value="30" {{ $duration == '30' ? 'selected' : '' }}>30 دقيقة</option>
                                <option value="45" {{ $duration == '45' ? 'selected' : '' }}>45 دقيقة</option>
                                <option value="60" {{ $duration == '60' ? 'selected' : '' }}>60 دقيقة (ساعة كاملة)</option>
                            </select>
                            <p class="text-[10px] font-bold text-gray-400 mt-1">المدة التلقائية لتوزيع الخانات الزمنية في جدول الطبيب.</p>
                        </div>

                        <!-- Patient Prefix -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">بادئة رقم ملف المريض (Patient File Prefix)</label>
                            <input type="text" name="patient_prefix" value="{{ $settings['patient_prefix']->value ?? 'PAT-' }}" placeholder="PAT-" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom focus:bg-white transition text-sm font-bold font-mono text-right" dir="ltr">
                            <p class="text-[10px] font-bold text-gray-400 mt-1">تضاف تلقائياً قبل رقم ملف المريض الجديد (مثال: PAT-2045).</p>
                        </div>

                        <!-- Max Daily Appointments per Doctor -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">الحد الأقصى للمواعيد اليومية لكل طبيب</label>
                            <input type="number" min="5" max="100" name="max_daily_appointments" value="{{ $settings['max_daily_appointments']->value ?? '25' }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom focus:bg-white transition text-sm font-bold text-right" dir="ltr">
                            <p class="text-[10px] font-bold text-gray-400 mt-1">لحماية جدول الأطباء من التكدس الزائد.</p>
                        </div>

                        <!-- Public Booking Enable/Disable Toggle -->
                        <div class="md:col-span-2 p-4 bg-teal-50/50 border border-teal-100 rounded-2xl flex items-center justify-between">
                            <div>
                                <h4 class="text-xs font-black text-teal-900 mb-0.5">بوابة حجز المرضى الخارجية أونلاين (/book)</h4>
                                <p class="text-[11px] font-bold text-teal-700/70">السماح للمرضى بفتح رابط الحجز الخارجي وحجز مواعيد بأنفسهم.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="public_booking_enabled" value="0">
                                <input type="checkbox" name="public_booking_enabled" value="1" {{ ($settings['public_booking_enabled']->value ?? '1') === '1' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-600"></div>
                            </label>
                        </div>

                    </div>
                </div>

                <!-- Tab 4: Branding -->
                <div x-show="activeTab === 'branding'" x-transition.opacity class="flex-1 space-y-6" style="display: none;">
                    <h3 class="text-lg font-black text-gray-800 pb-2 border-b border-gray-100">الهوية البصرية والمظهر</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 max-w-4xl">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">نوع الخط العربي (App Font)</label>
                            <select name="app_font" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom focus:bg-white transition text-sm font-bold">
                                <option value="Cairo" {{ ($settings['app_font']->value ?? '') == 'Cairo' ? 'selected' : '' }}>Cairo (الافتراضي - رسمي وأنيق)</option>
                                <option value="Tajawal" {{ ($settings['app_font']->value ?? '') == 'Tajawal' ? 'selected' : '' }}>Tajawal (حديث ومرن)</option>
                                <option value="Almarai" {{ ($settings['app_font']->value ?? '') == 'Almarai' ? 'selected' : '' }}>Almarai (عريض وواضح جداً)</option>
                                <option value="Alexandria" {{ ($settings['app_font']->value ?? '') == 'Alexandria' ? 'selected' : '' }}>Alexandria (رقمي وعصري)</option>
                            </select>
                            <p class="text-[10px] font-bold text-gray-400 mt-1">سيتم تطبيق الخط على النظام بأكمله فور الحفظ.</p>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">شعار المستشفى (Logo)</label>
                                <div class="flex items-center gap-4">
                                    <div class="w-20 h-20 bg-gray-50 border border-gray-200 rounded-2xl flex items-center justify-center p-2 overflow-hidden shadow-inner">
                                        @if(isset($settings['app_logo']) && $settings['app_logo']->value)
                                            <img src="{{ asset('storage/' . $settings['app_logo']->value) }}" alt="Logo" class="max-w-full max-h-full object-contain">
                                        @else
                                            <i class="fa-solid fa-hospital text-gray-300 text-3xl"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <input type="file" name="app_logo" accept="image/*" class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">أيقونة المتصفح (Favicon)</label>
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-center overflow-hidden shadow-inner p-1">
                                        @if(isset($settings['app_favicon']) && $settings['app_favicon']->value)
                                            <img src="{{ asset('storage/' . $settings['app_favicon']->value) }}" alt="Favicon" class="max-w-full max-h-full object-contain">
                                        @else
                                            <i class="fa-solid fa-globe text-gray-300 text-xl"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <input type="file" name="app_favicon" accept="image/*" class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 5: Contact -->
                <div x-show="activeTab === 'contact'" x-transition.opacity class="flex-1 space-y-6" style="display: none;">
                    <h3 class="text-lg font-black text-gray-800 pb-2 border-b border-gray-100">بيانات التواصل والطباعة</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">رقم الهاتف الشامل / الاستقبال</label>
                            <input type="text" name="clinic_phone" value="{{ $settings['clinic_phone']->value ?? '' }}" placeholder="920000000" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom focus:bg-white transition text-sm font-bold font-mono text-right" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">البريد الإلكتروني للعيادة</label>
                            <input type="email" name="clinic_email" value="{{ $settings['clinic_email']->value ?? '' }}" placeholder="info@clinic.com" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom focus:bg-white transition text-sm font-bold font-mono text-right" dir="ltr">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">العنوان الفعلي (National Address)</label>
                            <textarea name="clinic_address" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom focus:bg-white transition text-sm font-bold resize-none" placeholder="المدينة، الحي، اسم الشارع، المبنى...">{{ $settings['clinic_address']->value ?? '' }}</textarea>
                            <p class="text-[10px] font-bold text-gray-400 mt-1">يُطبع هذا العنوان في ترويسة التقارير الطبية وفواتير المرضى.</p>
                        </div>
                    </div>
                </div>

                <!-- Footer Action for General Settings -->
                <div class="mt-auto pt-6 flex justify-end">
                    <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-8 py-3.5 rounded-xl font-bold transition shadow-lg shadow-teal-600/30 flex items-center gap-3 w-full sm:w-auto justify-center">
                        <i class="fa-solid fa-save"></i> حفظ كافة الإعدادات
                    </button>
                </div>
            </form>

            <!-- Tab 6: Backup & Security (Independent tab) -->
            <div x-show="activeTab === 'backup'" x-transition.opacity class="p-6 md:p-8 flex-1 space-y-8" style="display: none;">
                
                <!-- Overview Header -->
                <div>
                    <h3 class="text-lg font-black text-gray-800 mb-1 pb-2 border-b border-gray-100 flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-teal-600"></i> نظام النسخ الاحتياطي والأمان المتقدم
                    </h3>
                    <p class="text-xs font-bold text-gray-500 mt-1">حماية بيانات المرضى، السجلات الطبية، والفواتير عبر النسخ الاحتياطي اليدوي والتلقائي المجدول.</p>
                </div>

                <!-- Status Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-gray-50 border border-gray-200/80 rounded-2xl p-4 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-database"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase">محرك قاعدة البيانات</p>
                            <h4 class="text-base font-black text-gray-800 font-mono">{{ strtoupper($dbDriver) }}</h4>
                        </div>
                    </div>

                    <div class="bg-gray-50 border border-gray-200/80 rounded-2xl p-4 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase">آخر عملية نسخ</p>
                            <h4 class="text-xs font-black text-gray-800" dir="ltr">{{ $settings['backup_last_run']->value ?? 'لم يتم بعد' }}</h4>
                        </div>
                    </div>

                    <div class="bg-gray-50 border border-gray-200/80 rounded-2xl p-4 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-box-archive"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase">النسخ المحفوظة محلياً</p>
                            <h4 class="text-base font-black text-gray-800">{{ count($backups) }} نسخ</h4>
                        </div>
                    </div>
                </div>

                <!-- Instant Manual Backup Action -->
                <div class="bg-gradient-to-r from-teal-50/50 to-blue-50/50 border border-teal-100 rounded-2xl p-6">
                    <h4 class="text-sm font-black text-gray-800 mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-bolt text-teal-600"></i> إنشاء نسخة احتياطية فورية الآن
                    </h4>
                    <p class="text-xs text-gray-500 font-bold mb-4">يمكنك إنشاء وتنزيل نسخة احتياطية بضغطة زر وتخزينها بأمان على جهازك أو وسيط خارجي.</p>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <form action="{{ route('settings.backups.create') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="db">
                            <button type="submit" class="w-full sm:w-auto bg-teal-600 hover:bg-teal-700 text-white px-5 py-3 rounded-xl text-xs font-bold transition shadow-sm flex items-center justify-center gap-2">
                                <i class="fa-solid fa-database"></i> نسخ قاعدة البيانات فقط (سريع وخفيف)
                            </button>
                        </form>

                        <form action="{{ route('settings.backups.create') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="full">
                            <button type="submit" class="w-full sm:w-auto bg-gray-800 hover:bg-black text-white px-5 py-3 rounded-xl text-xs font-bold transition shadow-sm flex items-center justify-center gap-2">
                                <i class="fa-solid fa-file-zipper"></i> نسخ شامل (قاعدة البيانات + صور المرفقات ZIP)
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Automated Scheduling Configuration -->
                <div class="border border-gray-100 rounded-2xl p-6 bg-white shadow-sm">
                    <h4 class="text-sm font-black text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-calendar-check text-teal-600"></i> إعدادات الجدولة والنسخ التلقائي
                    </h4>
                    
                    <form action="{{ route('settings.update') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="from_backup_tab" value="1">

                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200/70">
                            <input type="checkbox" name="backup_auto_enabled" id="backup_auto_enabled" value="1" {{ ($settings['backup_auto_enabled']->value ?? '0') === '1' ? 'checked' : '' }} class="w-5 h-5 text-teal-600 rounded border-gray-300 focus:ring-teal-500 cursor-pointer">
                            <label for="backup_auto_enabled" class="text-xs font-bold text-gray-700 cursor-pointer select-none">
                                تفعيل النسخ الاحتياطي التلقائي المجدول يومياً (الساعة 02:00 صباحاً)
                            </label>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">عدد النسخ المحتفظ بها (Retention Policy)</label>
                                <input type="number" min="1" max="50" name="backup_retention_count" value="{{ $settings['backup_retention_count']->value ?? '10' }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:border-teal-500 focus:bg-white outline-none">
                                <p class="text-[10px] text-gray-400 font-bold mt-1">يقوم النظام تلقائياً بحذف أقدم النسخ للحفاظ على مساحة تخزين السيرفر.</p>
                            </div>
                        </div>

                        <!-- Cron Job Instruction -->
                        <div class="bg-amber-50/60 border border-amber-200/70 p-4 rounded-xl text-xs text-amber-900 font-bold space-y-1.5">
                            <div class="flex items-center gap-2 text-amber-800 font-black">
                                <i class="fa-solid fa-circle-info"></i> كيفية تشغيل الجدولة التلقائية على السيرفر (cPanel Cron Job):
                            </div>
                            <p class="text-[11px] text-amber-800">لتفعيل الجدولة التلقائية على السيرفر، أضف هذا السطر في لوحة التحكم (cPanel -> Cron Jobs) مرة واحدة:</p>
                            <code class="block bg-white/90 p-2.5 rounded-lg border border-amber-200 font-mono text-[11px] text-gray-800 select-all" dir="ltr">
                                * * * * * cd {{ base_path() }} && php artisan schedule:run >> /dev/null 2>&1
                            </code>
                        </div>

                        <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-2">
                            <i class="fa-solid fa-check"></i> حفظ إعدادات الجدولة
                        </button>
                    </form>
                </div>

                <!-- Backups History Table -->
                <div>
                    <h4 class="text-sm font-black text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-teal-600"></i> سجل النسخ الاحتياطية المتوفرة
                    </h4>

                    @if(count($backups) > 0)
                        <div class="border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
                            <table class="w-full text-right text-xs">
                                <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 font-bold uppercase">
                                    <tr>
                                        <th class="px-5 py-3.5">اسم الملف</th>
                                        <th class="px-5 py-3.5">النوع</th>
                                        <th class="px-5 py-3.5">الحجم</th>
                                        <th class="px-5 py-3.5">تاريخ الإنشاء</th>
                                        <th class="px-5 py-3.5 text-center">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 font-bold text-gray-700">
                                    @foreach($backups as $b)
                                        <tr class="hover:bg-gray-50/50 transition">
                                            <td class="px-5 py-3.5">
                                                <div class="flex items-center gap-2 font-mono text-xs text-gray-800" dir="ltr">
                                                    @if($b['extension'] === 'zip')
                                                        <i class="fa-solid fa-file-zipper text-amber-500 text-sm"></i>
                                                    @else
                                                        <i class="fa-solid fa-database text-teal-600 text-sm"></i>
                                                    @endif
                                                    <span>{{ $b['filename'] }}</span>
                                                </div>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="px-2.5 py-1 rounded-lg text-[10px] {{ str_contains($b['filename'], '_full_') ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-teal-50 text-teal-700 border border-teal-200' }}">
                                                    {{ $b['type'] }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3.5 font-mono text-gray-500" dir="ltr">
                                                {{ $b['size'] }}
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <div class="text-gray-800 font-mono" dir="ltr">{{ $b['created_at'] }}</div>
                                                <div class="text-[10px] text-gray-400 font-bold">{{ $b['created_at_human'] }}</div>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <div class="flex items-center justify-center gap-2">
                                                    <a href="{{ route('settings.backups.download', $b['filename']) }}" class="px-3 py-1.5 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-700 border border-teal-200 text-xs font-bold transition flex items-center gap-1.5" title="تحميل النسخة لجهازك">
                                                        <i class="fa-solid fa-download"></i> تحميل
                                                    </a>

                                                    <form action="{{ route('settings.backups.delete', $b['filename']) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذه النسخة الاحتياطية نهائياً؟');" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 transition flex items-center justify-center" title="حذف النسخة">
                                                            <i class="fa-solid fa-trash text-xs"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-10 bg-gray-50 border border-dashed border-gray-200 rounded-2xl">
                            <i class="fa-solid fa-box-open text-3xl text-gray-300 mb-2"></i>
                            <p class="text-xs font-bold text-gray-500">لا توجد نسخ احتياطية محفوظة حالياً.</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">اضغط على أحد أزرار الإنشاء الفوري بالأعلى لأخذ أول نسخة احتياطية.</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
