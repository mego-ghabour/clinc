@extends('layouts.app')

@section('title', 'الإعدادات الأساسية - فكرتك الرقمية')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-teal-custom mb-1">الإعدادات الأساسية</h1>
            <p class="text-gray-500 text-sm">إدارة هوية النظام والتخصيصات العامة</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="p-6 space-y-8">
                
                <!-- System Info -->
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">معلومات النظام</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">اسم النظام (العنوان)</label>
                            <input type="text" name="app_name" value="{{ $settings['app_name']->value ?? 'فكرتك الرقمية' }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">الخط المستخدم (Google Font)</label>
                            <select name="app_font" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-teal-custom bg-gray-50">
                                <option value="Tajawal" {{ ($settings['app_font']->value ?? '') == 'Tajawal' ? 'selected' : '' }}>Tajawal (الافتراضي)</option>
                                <option value="Cairo" {{ ($settings['app_font']->value ?? '') == 'Cairo' ? 'selected' : '' }}>Cairo</option>
                                <option value="Almarai" {{ ($settings['app_font']->value ?? '') == 'Almarai' ? 'selected' : '' }}>Almarai</option>
                                <option value="Alexandria" {{ ($settings['app_font']->value ?? '') == 'Alexandria' ? 'selected' : '' }}>Alexandria</option>
                                <option value="Changa" {{ ($settings['app_font']->value ?? '') == 'Changa' ? 'selected' : '' }}>Changa</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Branding -->
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">الهوية البصرية</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Main Logo -->
                        <div class="p-4 border border-gray-100 rounded-lg bg-gray-50">
                            <label class="block text-sm font-bold text-gray-700 mb-2">شعار النظام (Logo)</label>
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-20 h-20 bg-white border border-gray-200 rounded-lg flex items-center justify-center p-2 overflow-hidden shadow-sm">
                                    @if(isset($settings['app_logo']) && $settings['app_logo']->value)
                                        <img src="{{ asset('storage/' . $settings['app_logo']->value) }}" alt="Logo" class="max-w-full max-h-full object-contain">
                                    @else
                                        <i class="fa-solid fa-image text-gray-300 text-2xl"></i>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">
                                    <p>يفضل أن تكون خلفية شفافة PNG</p>
                                    <p>المقاس المقترح: 200x80 بيكسل</p>
                                </div>
                            </div>
                            <input type="file" name="app_logo" accept="image/*" class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer">
                        </div>

                        <!-- Favicon -->
                        <div class="p-4 border border-gray-100 rounded-lg bg-gray-50">
                            <label class="block text-sm font-bold text-gray-700 mb-2">أيقونة المتصفح (Favicon)</label>
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 bg-white border border-gray-200 rounded-lg flex items-center justify-center p-1 overflow-hidden shadow-sm">
                                    @if(isset($settings['app_favicon']) && $settings['app_favicon']->value)
                                        <img src="{{ asset('storage/' . $settings['app_favicon']->value) }}" alt="Favicon" class="max-w-full max-h-full object-contain">
                                    @else
                                        <i class="fa-solid fa-globe text-gray-300 text-xl"></i>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">
                                    <p>تظهر في أعلى تبويب المتصفح</p>
                                    <p>المقاس المقترح: 32x32 بيكسل (مربعة)</p>
                                </div>
                            </div>
                            <input type="file" name="app_favicon" accept="image/*" class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer">
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer Actions -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                <button type="submit" class="bg-teal-custom hover:bg-teal-700 text-white px-8 py-2.5 rounded-lg font-bold transition shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    حفظ الإعدادات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
