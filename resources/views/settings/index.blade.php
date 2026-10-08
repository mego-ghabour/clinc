@extends('layouts.app')

@section('title', 'إعدادات النظام - Clinc')
@section('header', 'إعدادات النظام وتخصيص العيادة')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'general' }">

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-xl flex items-center justify-center text-2xl shadow-sm border border-teal-100">
                <i class="fa-solid fa-sliders"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-gray-800">إعدادات النظام الشاملة</h2>
                <p class="text-sm text-gray-500 font-bold mt-1">تخصيص الهوية البصرية، الإعدادات المالية، وبيانات التواصل.</p>
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

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col md:flex-row">
        
        <!-- Tabs Sidebar (Mobile: Top, Desktop: Right) -->
        <div class="w-full md:w-64 bg-gray-50 border-b md:border-b-0 md:border-l border-gray-100 p-4 shrink-0 overflow-x-auto md:overflow-visible flex md:flex-col gap-2 custom-scrollbar">
            <button @click="activeTab = 'general'" :class="activeTab === 'general' ? 'bg-white text-teal-600 shadow-sm border-teal-200' : 'text-gray-500 hover:bg-gray-100 border-transparent'" class="border whitespace-nowrap text-right px-4 py-3 rounded-xl text-sm font-bold transition flex items-center gap-3 shrink-0">
                <i class="fa-solid fa-hospital w-5"></i> بيانات العيادة الأساسية
            </button>
            <button @click="activeTab = 'branding'" :class="activeTab === 'branding' ? 'bg-white text-teal-600 shadow-sm border-teal-200' : 'text-gray-500 hover:bg-gray-100 border-transparent'" class="border whitespace-nowrap text-right px-4 py-3 rounded-xl text-sm font-bold transition flex items-center gap-3 shrink-0">
                <i class="fa-solid fa-palette w-5"></i> الهوية البصرية والمظهر
            </button>
            <button @click="activeTab = 'financial'" :class="activeTab === 'financial' ? 'bg-white text-teal-600 shadow-sm border-teal-200' : 'text-gray-500 hover:bg-gray-100 border-transparent'" class="border whitespace-nowrap text-right px-4 py-3 rounded-xl text-sm font-bold transition flex items-center gap-3 shrink-0">
                <i class="fa-solid fa-file-invoice-dollar w-5"></i> الإعدادات المالية والضرائب
            </button>
            <button @click="activeTab = 'contact'" :class="activeTab === 'contact' ? 'bg-white text-teal-600 shadow-sm border-teal-200' : 'text-gray-500 hover:bg-gray-100 border-transparent'" class="border whitespace-nowrap text-right px-4 py-3 rounded-xl text-sm font-bold transition flex items-center gap-3 shrink-0">
                <i class="fa-solid fa-address-book w-5"></i> بيانات التواصل (للطباعة)
            </button>
        </div>

        <!-- Form Content -->
        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="flex-1 p-6 md:p-8 flex flex-col min-h-[500px]">
            @csrf
            
            <!-- Tab 1: General -->
            <div x-show="activeTab === 'general'" x-transition.opacity class="flex-1">
                <h3 class="text-lg font-black text-gray-800 mb-6 pb-2 border-b border-gray-100">بيانات العيادة الأساسية</h3>
                <div class="space-y-5 max-w-xl">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">اسم المستشفى أو العيادة <span class="text-red-500">*</span></label>
                        <input type="text" name="app_name" value="{{ $settings['app_name']->value ?? 'العيادات الرقمية' }}" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom focus:bg-white transition text-sm font-bold">
                        <p class="text-[10px] font-bold text-gray-400 mt-1">يظهر هذا الاسم في أعلى لوحة التحكم وعلى الفواتير المطبوعة.</p>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Branding -->
            <div x-show="activeTab === 'branding'" x-transition.opacity class="flex-1" style="display: none;">
                <h3 class="text-lg font-black text-gray-800 mb-6 pb-2 border-b border-gray-100">الهوية البصرية والمظهر</h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">نوع الخط (Font)</label>
                        <select name="app_font" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom focus:bg-white transition text-sm font-bold">
                            <option value="Cairo" {{ ($settings['app_font']->value ?? '') == 'Cairo' ? 'selected' : '' }}>Cairo (الافتراضي - رسمي)</option>
                            <option value="Tajawal" {{ ($settings['app_font']->value ?? '') == 'Tajawal' ? 'selected' : '' }}>Tajawal (حديث وأنيق)</option>
                            <option value="Almarai" {{ ($settings['app_font']->value ?? '') == 'Almarai' ? 'selected' : '' }}>Almarai (عريض وواضح)</option>
                            <option value="Alexandria" {{ ($settings['app_font']->value ?? '') == 'Alexandria' ? 'selected' : '' }}>Alexandria (رقمي وعصري)</option>
                        </select>
                        <p class="text-[10px] font-bold text-gray-400 mt-1">سيتم تطبيق الخط على النظام بأكمله بعد الحفظ.</p>
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

            <!-- Tab 3: Financial -->
            <div x-show="activeTab === 'financial'" x-transition.opacity class="flex-1" style="display: none;">
                <h3 class="text-lg font-black text-gray-800 mb-6 pb-2 border-b border-gray-100">الإعدادات المالية والضرائب</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-3xl">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">الرقم الضريبي (VAT Number)</label>
                        <input type="text" name="vat_number" value="{{ $settings['vat_number']->value ?? '' }}" placeholder="مثال: 310000000000003" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom focus:bg-white transition text-sm font-bold font-mono" dir="ltr">
                        <p class="text-[10px] font-bold text-gray-400 mt-1">ضروري لظهوره في الفواتير الرسمية.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">نسبة ضريبة القيمة المضافة (%)</label>
                        <input type="number" step="0.01" name="vat_rate" value="{{ $settings['vat_rate']->value ?? '15' }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom focus:bg-white transition text-sm font-bold text-right" dir="ltr">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">العملة الافتراضية</label>
                        <select name="currency" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom focus:bg-white transition text-sm font-bold">
                            <option value="SAR" {{ ($settings['currency']->value ?? 'SAR') == 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                            <option value="USD" {{ ($settings['currency']->value ?? '') == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                            <option value="EGP" {{ ($settings['currency']->value ?? '') == 'EGP' ? 'selected' : '' }}>جنيه مصري (EGP)</option>
                            <option value="AED" {{ ($settings['currency']->value ?? '') == 'AED' ? 'selected' : '' }}>درهم إماراتي (AED)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tab 4: Contact -->
            <div x-show="activeTab === 'contact'" x-transition.opacity class="flex-1" style="display: none;">
                <h3 class="text-lg font-black text-gray-800 mb-6 pb-2 border-b border-gray-100">بيانات التواصل (للطباعة)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-3xl">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">رقم الهاتف الشامل</label>
                        <input type="text" name="clinic_phone" value="{{ $settings['clinic_phone']->value ?? '' }}" placeholder="920000000" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom focus:bg-white transition text-sm font-bold font-mono text-right" dir="ltr">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">البريد الإلكتروني</label>
                        <input type="email" name="clinic_email" value="{{ $settings['clinic_email']->value ?? '' }}" placeholder="info@clinic.com" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom focus:bg-white transition text-sm font-bold font-mono text-right" dir="ltr">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">العنوان الفعلي (National Address)</label>
                        <textarea name="clinic_address" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-custom focus:bg-white transition text-sm font-bold resize-none" placeholder="المدينة، الحي، اسم الشارع، المبنى...">{{ $settings['clinic_address']->value ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Footer Action -->
            <div class="mt-auto pt-6 flex justify-end">
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-8 py-3.5 rounded-xl font-bold transition shadow-lg shadow-teal-600/30 flex items-center gap-3 w-full sm:w-auto justify-center">
                    <i class="fa-solid fa-save"></i> حفظ كافة الإعدادات
                </button>
            </div>
            
        </form>
    </div>
</div>
@endsection
