@extends('layouts.app')

@section('title', 'دليل وشرح النظام الشامل - دليل الاستخدام والتشغيل')
@section('header', 'دليل وشرح النظام الشامل والتوثيق التشغيلي')

@section('content')
<div class="space-y-8 pb-16" x-data="{
    searchQuery: '',
    activeTab: 'all',
    selectedRole: 'all',
    isPrinting: false,
    expandedSection: null,
    matchesSearch(text) {
        if (!this.searchQuery.trim()) return true;
        return text.toLowerCase().includes(this.searchQuery.toLowerCase());
    }
}">

    <!-- Hero Header & Quick Tools -->
    <div class="bg-gradient-to-l from-[#0a3a4c] via-[#0b4a60] to-[#135d77] rounded-3xl p-6 md:p-10 text-white shadow-xl relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
        <div class="absolute -left-16 -top-16 w-72 h-72 bg-teal-300 rounded-full mix-blend-screen filter blur-3xl opacity-20"></div>
        
        <div class="relative z-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div class="space-y-3 max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-400/20 text-amber-300 text-xs font-bold border border-amber-400/30">
                    <i class="fa-solid fa-book-medical"></i>
                    <span>الدليل الإجرائي والتشغيلي المعتمد لجميع أقسام العيادة</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-black tracking-tight">الدليل الشامل لاستخدام وتشغيل النظام 🏥</h1>
                <p class="text-teal-100 text-sm md:text-base leading-relaxed">
                    مرجع تدريبي وتوثيقي كامل يغطي كافة تفاصيل ودورات العمل بالعيادة: من لحظة حجز المريض والاستقبال، إلى الفحص الطبي والتشخيص، وسير عمل المختبر والأشعة، وصرف الأدوية، وحسابات التأمين، والفوترة الضريبية وإدارة الصلاحيات.
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <a href="{{ route('guide.download.doc') }}" class="px-5 py-3 rounded-2xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition flex items-center gap-2 shadow-lg">
                    <i class="fa-solid fa-file-word text-sm"></i> تحميل الدليل Word (DOC)
                </a>
                <button onclick="window.print()" class="px-5 py-3 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/20 text-white text-xs font-bold transition flex items-center gap-2 backdrop-blur-sm">
                    <i class="fa-solid fa-print"></i> طباعة / حفظ PDF
                </button>
                <a href="{{ route('dashboard') }}" class="px-5 py-3 rounded-2xl bg-amber-400 hover:bg-amber-300 text-gray-900 text-xs font-black transition flex items-center gap-2 shadow-lg">
                    <i class="fa-solid fa-arrow-right"></i> العودة للوحة التحكم
                </a>
            </div>
        </div>

        <!-- Live Metrics Bar inside Banner -->
        <div class="relative z-10 mt-8 pt-6 border-t border-white/15 grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3 text-center">
            <div class="bg-black/20 p-3 rounded-xl border border-white/10">
                <p class="text-[10px] text-teal-200 font-bold mb-0.5">المرضى المسجلين</p>
                <p class="text-base font-black font-mono">{{ number_format($systemStats['total_patients'] ?? 0) }}</p>
            </div>
            <div class="bg-black/20 p-3 rounded-xl border border-white/10">
                <p class="text-[10px] text-teal-200 font-bold mb-0.5">الأطباء والعيادات</p>
                <p class="text-base font-black font-mono">{{ $systemStats['total_doctors'] ?? 0 }} / {{ $systemStats['total_clinics'] ?? 0 }}</p>
            </div>
            <div class="bg-black/20 p-3 rounded-xl border border-white/10">
                <p class="text-[10px] text-teal-200 font-bold mb-0.5">الفحوصات المخبرية</p>
                <p class="text-base font-black font-mono">{{ $systemStats['total_lab_tests'] ?? 0 }} فحص</p>
            </div>
            <div class="bg-black/20 p-3 rounded-xl border border-white/10">
                <p class="text-[10px] text-teal-200 font-bold mb-0.5">الأدوية بالمخزن</p>
                <p class="text-base font-black font-mono">{{ $systemStats['total_medicines'] ?? 0 }} صنف</p>
            </div>
            <div class="bg-black/20 p-3 rounded-xl border border-white/10">
                <p class="text-[10px] text-teal-200 font-bold mb-0.5">شركات التأمين</p>
                <p class="text-base font-black font-mono">{{ $systemStats['total_insurances'] ?? 0 }} شركة</p>
            </div>
            <div class="bg-black/20 p-3 rounded-xl border border-white/10">
                <p class="text-[10px] text-teal-200 font-bold mb-0.5">الأدوار المعتمدة</p>
                <p class="text-base font-black font-mono">{{ $systemStats['total_roles'] ?? 0 }} أدوار</p>
            </div>
            <div class="bg-black/20 p-3 rounded-xl border border-white/10">
                <p class="text-[10px] text-emerald-300 font-bold mb-0.5">حالة النظام</p>
                <p class="text-base font-black text-emerald-300">متصل وجاهز</p>
            </div>
        </div>
    </div>

    <!-- Live Search & Interactive Filter Bar -->
    <div class="bg-white rounded-2xl p-4 md:p-5 shadow-sm border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4 sticky top-2 z-20 print:hidden">
        <!-- Search Input -->
        <div class="relative w-full md:w-96">
            <i class="fa-solid fa-magnifying-glass absolute right-4 top-3.5 text-gray-400"></i>
            <input 
                type="text" 
                x-model="searchQuery" 
                placeholder="ابحث عن أي قسم، ميزة، أو زر (مثال: روشتة، تأمين، باركود، سحب عينة، صلاحيات)..."
                class="w-full pr-11 pl-4 py-2.5 rounded-xl border border-gray-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-100 text-sm font-medium transition"
            >
            <button 
                x-show="searchQuery.length > 0" 
                @click="searchQuery = ''" 
                class="absolute left-3 top-3 text-gray-400 hover:text-gray-600 text-xs bg-gray-100 p-1 rounded-full"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Role Filter Badges -->
        <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto custom-scrollbar pb-1 md:pb-0 text-xs font-bold">
            <span class="text-gray-400 text-[11px] shrink-0 ml-1">تصفية حسب التخصص:</span>
            <button @click="selectedRole = 'all'" :class="selectedRole === 'all' ? 'bg-[#0b4a60] text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-3 py-1.5 rounded-lg transition shrink-0">الكل (14 قسم)</button>
            <button @click="selectedRole = 'reception'" :class="selectedRole === 'reception' ? 'bg-[#0b4a60] text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-3 py-1.5 rounded-lg transition shrink-0">الاستقبال والطابور</button>
            <button @click="selectedRole = 'doctor'" :class="selectedRole === 'doctor' ? 'bg-[#0b4a60] text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-3 py-1.5 rounded-lg transition shrink-0">الأطباء والكشف</button>
            <button @click="selectedRole = 'lab'" :class="selectedRole === 'lab' ? 'bg-[#0b4a60] text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-3 py-1.5 rounded-lg transition shrink-0">المختبر والتحاليل</button>
            <button @click="selectedRole = 'pharmacy'" :class="selectedRole === 'pharmacy' ? 'bg-[#0b4a60] text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-3 py-1.5 rounded-lg transition shrink-0">الصيدلية والمخزون</button>
            <button @click="selectedRole = 'insurance'" :class="selectedRole === 'insurance' ? 'bg-[#0b4a60] text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-3 py-1.5 rounded-lg transition shrink-0">التأمين والمطالبات</button>
            <button @click="selectedRole = 'finance'" :class="selectedRole === 'finance' ? 'bg-[#0b4a60] text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-3 py-1.5 rounded-lg transition shrink-0">المالية والفواتير</button>
            <button @click="selectedRole = 'admin'" :class="selectedRole === 'admin' ? 'bg-[#0b4a60] text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-3 py-1.5 rounded-lg transition shrink-0">الإدارة والأمان</button>
        </div>
    </div>

    <!-- Quick Table of Contents Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 print:hidden">
        <a href="#section-patient-lifecycle" class="p-3.5 rounded-xl bg-white border border-gray-100 hover:border-teal-400 hover:shadow-md transition group flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-teal-50 text-teal-700 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-route"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-black text-gray-800 truncate">1. خريطة رحلة المريض</p>
                <p class="text-[10px] text-gray-400 truncate">التسلسل السريري والإداري</p>
            </div>
        </a>

        <a href="#section-reception-queues" class="p-3.5 rounded-xl bg-white border border-gray-100 hover:border-teal-400 hover:shadow-md transition group flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-black text-gray-800 truncate">2. الاستقبال والطابور الحي</p>
                <p class="text-[10px] text-gray-400 truncate">الملفات والمواعيد والنداء</p>
            </div>
        </a>

        <a href="#section-public-booking" class="p-3.5 rounded-xl bg-white border border-gray-100 hover:border-teal-400 hover:shadow-md transition group flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-globe"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-black text-gray-800 truncate">3. بوابة الحجز الإلكتروني</p>
                <p class="text-[10px] text-gray-400 truncate">حجز المرضى أونلاين</p>
            </div>
        </a>

        <a href="#section-doctor-emr" class="p-3.5 rounded-xl bg-white border border-gray-100 hover:border-teal-400 hover:shadow-md transition group flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-user-doctor"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-black text-gray-800 truncate">4. العيادات والكشف الطبي</p>
                <p class="text-[10px] text-gray-400 truncate">العلامات الحيوية والتشخيص</p>
            </div>
        </a>

        <a href="#section-laboratory" class="p-3.5 rounded-xl bg-white border border-gray-100 hover:border-teal-400 hover:shadow-md transition group flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-flask-vial"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-black text-gray-800 truncate">5. قسم المختبر والتحاليل</p>
                <p class="text-[10px] text-gray-400 truncate">العينات والنتائج والتقارير</p>
            </div>
        </a>

        <a href="#section-pharmacy" class="p-3.5 rounded-xl bg-white border border-gray-100 hover:border-teal-400 hover:shadow-md transition group flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-pills"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-black text-gray-800 truncate">6. الصيدلية والمخزون</p>
                <p class="text-[10px] text-gray-400 truncate">الأدوية والتنبيهات وصرف الروشتة</p>
            </div>
        </a>

        <a href="#section-radiology" class="p-3.5 rounded-xl bg-white border border-gray-100 hover:border-teal-400 hover:shadow-md transition group flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-x-ray"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-black text-gray-800 truncate">7. قسم الأشعة والتصوير</p>
                <p class="text-[10px] text-gray-400 truncate">الطلبات ورفع الصور والتقارير</p>
            </div>
        </a>

        <a href="#section-insurance" class="p-3.5 rounded-xl bg-white border border-gray-100 hover:border-teal-400 hover:shadow-md transition group flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-building-shield"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-black text-gray-800 truncate">8. التأمين والمطالبات</p>
                <p class="text-[10px] text-gray-400 truncate">الشركات والتحمل والموافقات</p>
            </div>
        </a>

        <a href="#section-billing" class="p-3.5 rounded-xl bg-white border border-gray-100 hover:border-teal-400 hover:shadow-md transition group flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-black text-gray-800 truncate">9. الفواتير والمالية</p>
                <p class="text-[10px] text-gray-400 truncate">الضريبة والدفعات وسند القبض</p>
            </div>
        </a>

        <a href="#section-doctors-schedules" class="p-3.5 rounded-xl bg-white border border-gray-100 hover:border-teal-400 hover:shadow-md transition group flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-cyan-50 text-cyan-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-calendar-week"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-black text-gray-800 truncate">10. الجداول والعيادات</p>
                <p class="text-[10px] text-gray-400 truncate">أوقات العمل والإجازات والخدمات</p>
            </div>
        </a>

        <a href="#section-roles-security" class="p-3.5 rounded-xl bg-white border border-gray-100 hover:border-teal-400 hover:shadow-md transition group flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-black text-gray-800 truncate">11. الصلاحيات والأمان</p>
                <p class="text-[10px] text-gray-400 truncate">المستخدمين وتقمص الأدوار</p>
            </div>
        </a>

        <a href="#section-audit-settings" class="p-3.5 rounded-xl bg-white border border-gray-100 hover:border-teal-400 hover:shadow-md transition group flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-gear"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-black text-gray-800 truncate">12. الإعدادات والنسخ</p>
                <p class="text-[10px] text-gray-400 truncate">الهوية والباك اب وسجل التدقيق</p>
            </div>
        </a>

        <a href="#section-whatsapp-integration" class="p-3.5 rounded-xl bg-white border border-emerald-200 hover:border-emerald-500 hover:shadow-md transition group flex items-center gap-3 bg-emerald-50/20">
            <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <i class="fa-brands fa-whatsapp text-lg"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-black text-gray-900 truncate">13. منظومة الواتساب</p>
                <p class="text-[10px] text-emerald-700 font-bold truncate">الباركود والمراسلة الفورية</p>
            </div>
        </a>

        <a href="#section-security-hardening" class="p-3.5 rounded-xl bg-white border border-rose-200 hover:border-rose-500 hover:shadow-md transition group flex items-center gap-3 bg-rose-50/20">
            <div class="w-9 h-9 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-black text-gray-900 truncate">14. الأمان وإغلاق الثغرات</p>
                <p class="text-[10px] text-rose-700 font-bold truncate">حماية الإدارة ومنع التخمين</p>
            </div>
        </a>

        <a href="#section-double-booking" class="p-3.5 rounded-xl bg-white border border-amber-200 hover:border-amber-500 hover:shadow-md transition group flex items-center gap-3 bg-amber-50/20">
            <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-calendar-xmark"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-black text-gray-900 truncate">15. منع الحجز المزدوج</p>
                <p class="text-[10px] text-amber-700 font-bold truncate">حماية جداول الأطباء</p>
            </div>
        </a>

        <a href="#section-hostinger-guide" class="p-3.5 rounded-xl bg-white border border-indigo-200 hover:border-indigo-500 hover:shadow-md transition group flex items-center gap-3 bg-indigo-50/20">
            <div class="w-9 h-9 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-server"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-black text-gray-900 truncate">16. النشر على هوستنجر</p>
                <p class="text-[10px] text-indigo-700 font-bold truncate">الاستضافة المشتركة والـ VPS</p>
            </div>
        </a>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 1: Patient Journey & Overall System Architecture -->
    <!-- ========================================================================= -->
    <div id="section-patient-lifecycle" x-show="matchesSearch('رحلة المريض دورة العمل الاستقبال الطبيب المختبر الصيدلية الفاتورة EMR') && (selectedRole === 'all' || selectedRole === 'reception' || selectedRole === 'doctor')" class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center text-lg font-black">1</span>
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-gray-900">خريطة رحلة المريض ودورة العمل السريرية والإدارية (Patient Journey)</h2>
                    <p class="text-xs font-bold text-gray-500">التدفق الإجرائي المترابط من لحظة وصول المريض حتى انتهاء الزيارة وسداد الحساب</p>
                </div>
            </div>
            <span class="text-xs bg-teal-100 text-teal-800 px-3 py-1 rounded-full font-bold">مخطط شامل</span>
        </div>

        <!-- Visual Flow Steps -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 relative">
            <div class="bg-gradient-to-b from-teal-50 to-white p-4 rounded-2xl border border-teal-100 space-y-2 relative">
                <div class="w-8 h-8 rounded-full bg-teal-600 text-white flex items-center justify-center text-xs font-black mb-2 shadow-sm">1</div>
                <h4 class="text-sm font-black text-teal-900">الحجز والتسجيل</h4>
                <p class="text-xs text-gray-600 leading-relaxed">تسجيل المريض أو التحقق من ملفه بالهوية الوطنية / الإقامة، وحجز الموعد أونلاين أو عبر موظف الاستقبال.</p>
                <div class="text-[10px] font-bold text-teal-700 bg-white p-1.5 rounded-lg border border-teal-200">الأقسام: الاستقبال / البوابة العامة</div>
            </div>

            <div class="bg-gradient-to-b from-blue-50 to-white p-4 rounded-2xl border border-blue-100 space-y-2 relative">
                <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-black mb-2 shadow-sm">2</div>
                <h4 class="text-sm font-black text-blue-900">الحضور والطابور الحي</h4>
                <p class="text-xs text-gray-600 leading-relaxed">تسجيل حضور المريض (Check-in) ونقله لشاشة الطابور التفاعلية، وإنشاء زيارة طبية جاهزة للطبيب المعالج.</p>
                <div class="text-[10px] font-bold text-blue-700 bg-white p-1.5 rounded-lg border border-blue-200">الأقسام: شاشة الطابور (Queues)</div>
            </div>

            <div class="bg-gradient-to-b from-indigo-50 to-white p-4 rounded-2xl border border-indigo-100 space-y-2 relative">
                <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-black mb-2 shadow-sm">3</div>
                <h4 class="text-sm font-black text-indigo-900">الكشف والتشخيص الطبي</h4>
                <p class="text-xs text-gray-600 leading-relaxed">فحص العلامات الحيوية، تدوين التشخيص ICD-10، وطلب الفحوصات المخبرية، صور الأشعة، وكتابة الوصفة الدوائية.</p>
                <div class="text-[10px] font-bold text-indigo-700 bg-white p-1.5 rounded-lg border border-indigo-200">الأقسام: العيادات والزيارات (Visits)</div>
            </div>

            <div class="bg-gradient-to-b from-purple-50 to-white p-4 rounded-2xl border border-purple-100 space-y-2 relative">
                <div class="w-8 h-8 rounded-full bg-purple-600 text-white flex items-center justify-center text-xs font-black mb-2 shadow-sm">4</div>
                <h4 class="text-sm font-black text-purple-900">المختبر والأشعة والصيدلية</h4>
                <p class="text-xs text-gray-600 leading-relaxed">سحب العينات وتوثيق النتائج المخبرية، فحص الأشعة، ومراجعة الصيدلي للوصفة وصرف الدواء مع خصم المخزون.</p>
                <div class="text-[10px] font-bold text-purple-700 bg-white p-1.5 rounded-lg border border-purple-200">الأقسام: المختبر / الأشعة / الصيدلية</div>
            </div>

            <div class="bg-gradient-to-b from-amber-50 to-white p-4 rounded-2xl border border-amber-100 space-y-2 relative">
                <div class="w-8 h-8 rounded-full bg-amber-600 text-white flex items-center justify-center text-xs font-black mb-2 shadow-sm">5</div>
                <h4 class="text-sm font-black text-amber-900">الفوترة والتأمين والسداد</h4>
                <p class="text-xs text-gray-600 leading-relaxed">توليد الفاتورة الشاملة، احتساب نسبة تحمل التأمين والخصومات، وتسجيل الدفعات وإصدار الفاتورة الضريبية الرسمية.</p>
                <div class="text-[10px] font-bold text-amber-700 bg-white p-1.5 rounded-lg border border-amber-200">الأقسام: الفواتير / التأمين والمطالبات</div>
            </div>
        </div>

        <div class="bg-amber-50 rounded-2xl p-4 border border-amber-200 flex items-start gap-3">
            <i class="fa-solid fa-lightbulb text-amber-600 text-lg mt-0.5"></i>
            <div class="text-xs text-amber-900 space-y-1">
                <p class="font-bold">ميزة التكامل والترابط التلقائي (Zero-Redundancy):</p>
                <p class="leading-relaxed">أي إجراء يطلبه الطبيب (تحليل، أشعة، دواء) يذهب آلياً وبدون تدخل يدوي إلى لوحة القسم المختص، ويظهر في فاتورة المريض النهائية مباشرة بدون الحاجة لإعادة كتابة الأسعار أو البيانات من قبل المحاسب.</p>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 2: Reception & Live Queues -->
    <!-- ========================================================================= -->
    <div id="section-reception-queues" x-show="matchesSearch('الاستقبال المرضى الطابور المواعيد الحجز حضور الهوية ملف المريض Checkin') && (selectedRole === 'all' || selectedRole === 'reception')" class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-black">2</span>
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-gray-900">قسم الاستقبال، إدارة المرضى وشاشة الطابور الحي (Reception & Queues)</h2>
                    <p class="text-xs font-bold text-gray-500">البوابة التشغيلية الأولى لاستقبال المرضى وتنظيم تدفقهم على العيادات</p>
                </div>
            </div>
            <a href="{{ route('patients.index') }}" class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold px-3 py-1.5 rounded-xl transition flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> الانتقال لقسم المرضى
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Part A: Patients Management -->
            <div class="space-y-4">
                <h3 class="text-base font-black text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-address-card text-blue-600"></i> إدارة السجلات وملفات المرضى (Patients)
                </h3>
                <ul class="space-y-2.5 text-xs text-gray-600 leading-relaxed">
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-500 mt-1 shrink-0"></i>
                        <div><strong class="text-gray-800">إضافة مريض جديد:</strong> الضغط على "إضافة مريض" وتعبئة (الاسم الكامل، رقم الهوية أو الإقامة المكون من 10 أرقام، تاريخ الميلاد، رقم الجوال، الجنس، فصيلة الدم، وتاريخ الأمراض المزمنة والحساسية).</div>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-500 mt-1 shrink-0"></i>
                        <div><strong class="text-gray-800">منع التكرار التلقائي:</strong> يرفض النظام تسجيل أي رقم هوية أو إقامة مسجل مسبقاً لمنع ازدواجية الملفات الطبية.</div>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-500 mt-1 shrink-0"></i>
                        <div><strong class="text-gray-800">البحث السريع اللحظي:</strong> يمكن البحث الفوري بالاسم، رقم الجوال، أو رقم الهوية من خلال مربع البحث السريع أو واجهة الـ API.</div>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-500 mt-1 shrink-0"></i>
                        <div><strong class="text-gray-800">الأرشفة والاستعادة (Soft Deletes):</strong> عند حذف ملف مريض فإنه يؤرشف بأمان دون مسحه نهائياً من قاعدة البيانات، ويمكن استعادته بضغطة زر.</div>
                    </li>
                </ul>
            </div>

            <!-- Part B: Appointments & Live Queue -->
            <div class="space-y-4">
                <h3 class="text-base font-black text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-users-rays text-teal-600"></i> حجز المواعيد وشاشة الطابور الحي (Live Queue)
                </h3>
                <ul class="space-y-2.5 text-xs text-gray-600 leading-relaxed">
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-500 mt-1 shrink-0"></i>
                        <div><strong class="text-gray-800">حجز موعد جديد:</strong> اختيار المريض، العيادة، الطبيب المعالج، وتاريخ ووقت الموعد وفقاً لجدول دوام الطبيب المسجل بالنظام.</div>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-500 mt-1 shrink-0"></i>
                        <div><strong class="text-gray-800">تسجيل الحضور (Check-In):</strong> بمجرد وصول المريض لمقر العيادة، ينقر موظف الاستقبال على زر <span class="bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded font-bold">تسجيل الحضور</span> من شاشة المواعيد أو شاشة الطابور.</div>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-500 mt-1 shrink-0"></i>
                        <div><strong class="text-gray-800">الأتمتة التلقائية:</strong> تسجيل الحضور ينقل المريض لحالة <span class="text-amber-600 font-bold">في الانتظار</span>، ويفتح زيارة طبية جديدة مرتبطة مباشرة بغرفة الكشف لدى الطبيب.</div>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-500 mt-1 shrink-0"></i>
                        <div><strong class="text-gray-800">شاشة العرض لصالة الانتظار:</strong> يمكن فتح شاشة الطابور <a href="{{ route('queues.index') }}" class="text-teal-600 underline font-bold">/queues</a> على شاشات التلفزيون في صالة الانتظار لعرض أرقام المرضى وأسمائهم وأسماء الأطباء وغرفهم مباشرة.</div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 3: Online Public Booking Portal -->
    <!-- ========================================================================= -->
    <div id="section-public-booking" x-show="matchesSearch('الحجز الإلكتروني الحجز أونلاين حجز خارجي بوابة المرضى book public') && (selectedRole === 'all' || selectedRole === 'reception' || selectedRole === 'admin')" class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-black">3</span>
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-gray-900">بوابة الحجز الإلكتروني للجمهور (Public Booking Portal)</h2>
                    <p class="text-xs font-bold text-gray-500">تمكين المرضى من حجز مواعيدهم ذاتياً عبر الإنترنت بكل سهولة</p>
                </div>
            </div>
            <a href="{{ route('booking.index') }}" target="_blank" class="text-xs bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold px-3 py-1.5 rounded-xl transition flex items-center gap-1.5">
                <i class="fa-solid fa-globe"></i> فتح بوابة الحجز الخارجية
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100 space-y-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-black">1</div>
                <h4 class="text-sm font-black text-gray-800">رابط البوابة العامة</h4>
                <p class="text-xs text-gray-600 leading-relaxed">
                    يعمل الرابط <code class="bg-white px-2 py-1 rounded text-emerald-700 font-bold border border-gray-200">/book</code> كصفحة عامة مستقلة لا تتطلب تسجيل دخول، ومصممة للتوافق مع أجهزة الجوال لفتحها للمرضى من خلال وسائل التواصل وموقع العيادة.
                </p>
            </div>

            <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100 space-y-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-black">2</div>
                <h4 class="text-sm font-black text-gray-800">سير عملية الحجز الذاتي</h4>
                <p class="text-xs text-gray-600 leading-relaxed">
                    يحدد المريض العيادة $\rightarrow$ يختار الطبيب $\rightarrow$ يقوم النظام بجلب الفترات والمواعيد الشاغرة (Slots) لحظياً $\rightarrow$ يدخل المريض اسمه ورقم هويته وجواله $\rightarrow$ يتم تأكيد الحجز فوراً وحفظه بالنظام.
                </p>
            </div>

            <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100 space-y-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-black">3</div>
                <h4 class="text-sm font-black text-gray-800">التحكم من الإعدادات</h4>
                <p class="text-xs text-gray-600 leading-relaxed">
                    يمكن للمدير تفعيل أو إيقاف استقبال الحجوزات الإلكترونية بنقرة واحدة من صفحة إعدادات النظام، مع تخصيص رسالة تظهر للمرضى في حال إغلاق الحجز المؤقت.
                </p>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 4: Doctor EMR & Clinical Visits -->
    <!-- ========================================================================= -->
    <div id="section-doctor-emr" x-show="matchesSearch('الطبيب العيادة الكشف الزيارة التشخيص العلامات الحيوية الروشتة الوصفة EMR Visit') && (selectedRole === 'all' || selectedRole === 'doctor')" class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg font-black">4</span>
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-gray-900">العيادات الطبية، الكشف السريري والسجل الإلكتروني (Doctor EMR & Visits)</h2>
                    <p class="text-xs font-bold text-gray-500">منظومة الطبيب المتكاملة لتدوين الكشف، طلب الفحوصات والأشعة، وكتابة الوصفات</p>
                </div>
            </div>
            <a href="{{ route('visits.index') }}" class="text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold px-3 py-1.5 rounded-xl transition flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> ملفات الزيارات
            </a>
        </div>

        <div class="space-y-4">
            <h3 class="text-base font-black text-gray-800">شاشة الكشف الطبي الشاملة (Clinical Encounter):</h3>
            <p class="text-xs text-gray-600 leading-relaxed">
                عند دخول المريض للعيادة يفتح الطبيب ملف الزيارة الطبية ([`visits/show.blade.php`](file:///c:/Users/fkrtk/Desktop/Clinc/resources/views/visits/show.blade.php))، والتي تحتوي على المحاور التالية:
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/50 space-y-2">
                    <div class="text-indigo-600 font-bold text-xs flex items-center gap-2">
                        <i class="fa-solid fa-heart-pulse"></i> 1. العلامات الحيوية (Vitals)
                    </div>
                    <p class="text-[11px] text-gray-600 leading-relaxed">
                        تسجيل ضغط الدم (Blood Pressure)، النبض، الحرارة، معدل التنفس، الوزن، الطول، مع احتساب مؤشر كتلة الجسم (BMI) التلقائي.
                    </p>
                </div>

                <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/50 space-y-2">
                    <div class="text-indigo-600 font-bold text-xs flex items-center gap-2">
                        <i class="fa-solid fa-stethoscope"></i> 2. الشكوى والتشخيص (Diagnosis)
                    </div>
                    <p class="text-[11px] text-gray-600 leading-relaxed">
                        تدوين الشكوى الرئيسية (Chief Complaint)، الفحص السريري، وكود التشخيص المعتمد وتصنيف الأمراض الدولي (ICD-10) وتوصيات الطبيب.
                    </p>
                </div>

                <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/50 space-y-2">
                    <div class="text-indigo-600 font-bold text-xs flex items-center gap-2">
                        <i class="fa-solid fa-flask-vial"></i> 3. طلب التحاليل والأشعة
                    </div>
                    <p class="text-[11px] text-gray-600 leading-relaxed">
                        بكبسة زر واحدة داخل الزيارة: يختار الطبيب باقة التحاليل المخبرية أو فحوصات الأشعة، لتنتقل فوراً لأقسامها المعنية وتُضاف للفاتورة.
                    </p>
                </div>

                <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/50 space-y-2">
                    <div class="text-indigo-600 font-bold text-xs flex items-center gap-2">
                        <i class="fa-solid fa-file-prescription"></i> 4. الروشتة الإلكترونية (Rx)
                    </div>
                    <p class="text-[11px] text-gray-600 leading-relaxed">
                        نافذة سهلة لكتابة الوصفة، اختيار الأدوية من قائمة المخزن، وتحديد الجرعة، التكرار، ومدة العلاج، ثم إرسالها إلكترونياً للصيدلية فوراً.
                    </p>
                </div>
            </div>

            <!-- Doctor Workflow Steps -->
            <div class="bg-indigo-50/40 rounded-2xl p-5 border border-indigo-100 space-y-3">
                <h4 class="text-xs font-black text-indigo-900">إجراءات الطبيب بالخطوات:</h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs text-indigo-800">
                    <div class="bg-white p-3 rounded-xl border border-indigo-200">
                        <span class="font-black text-indigo-600 block mb-1">الخطوة الأولى:</span>
                        الاطلاع على التاريخ المرضي والحساسيات المسجلة بملف المريض.
                    </div>
                    <div class="bg-white p-3 rounded-xl border border-indigo-200">
                        <span class="font-black text-indigo-600 block mb-1">الخطوة الثانية:</span>
                        فحص المريض وتدوين الملاحظات السريرية، وطلب التحاليل إن لزم.
                    </div>
                    <div class="bg-white p-3 rounded-xl border border-indigo-200">
                        <span class="font-black text-indigo-600 block mb-1">الخطوة الثالثة:</span>
                        إصدار الوصفة الطبية وإنهاء الزيارة، مما يحدث حالة الموعد إلى <span class="font-bold text-emerald-600">مكتمل</span>.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 5: Laboratory Module -->
    <!-- ========================================================================= -->
    <div id="section-laboratory" x-show="matchesSearch('المختبر التحاليل الفحوصات العينة نتيجة تقرير طبي كود فحص Lab Test') && (selectedRole === 'all' || selectedRole === 'lab' || selectedRole === 'doctor')" class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg font-black">5</span>
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-gray-900">قسم المختبر والتحاليل الطبية (Laboratory Module) 🧪</h2>
                    <p class="text-xs font-bold text-gray-500">نظام إدارة العينات، تسجيل النتائج المخبرية، وطباعة التقارير المعتمدة بـ QR Code</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('lab-tests.index') }}" class="text-xs bg-purple-50 hover:bg-purple-100 text-purple-700 font-bold px-3 py-1.5 rounded-xl transition">دليل التحاليل</a>
                <a href="{{ route('lab-requests.index') }}" class="text-xs bg-purple-600 hover:bg-purple-700 text-white font-bold px-3 py-1.5 rounded-xl transition">طلبات الفحص</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Catalog -->
            <div class="space-y-3">
                <h3 class="text-base font-black text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-purple-600"></i> دليل الفحوصات الطبية (Lab Test Catalog)
                </h3>
                <p class="text-xs text-gray-600 leading-relaxed">
                    يحتوي الدليل على كافة التحاليل المخبرية المعتمدة (CBC, Fasting Blood Sugar, Lipid Profile, Kidney & Liver Function, TSH, Urinalysis, إلخ):
                </p>
                <ul class="space-y-2 text-xs text-gray-600">
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-emerald-500 text-[10px]"></i>
                        <span><strong>كود الفحص واسمه:</strong> باللغتين العربية والإنجليزية.</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-emerald-500 text-[10px]"></i>
                        <span><strong>نوع العينة المطلوبة:</strong> (دم وريدي، بول، مسحة، بلازما، مصل، إلخ).</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-emerald-500 text-[10px]"></i>
                        <span><strong>القيم والمعدلات الطبيعية (Normal Ranges):</strong> والوحدات (mg/dL, mmol/L, %).</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-emerald-500 text-[10px]"></i>
                        <span><strong>التسعيرة المعتمدة:</strong> لحساب تكلفة التحليل في الفاتورة والتأمين آلياً.</span>
                    </li>
                </ul>
            </div>

            <!-- Workflow -->
            <div class="space-y-3">
                <h3 class="text-base font-black text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-diagram-project text-purple-600"></i> دورة حياة طلب التحليل (Lifecycle)
                </h3>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50 border border-gray-100 text-xs">
                        <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-800 font-black flex items-center justify-center shrink-0">1</span>
                        <div><strong>معلق (Pending):</strong> الطلب وصل من الطبيب في انتظار حضور المريض للمختبر.</div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50 border border-gray-100 text-xs">
                        <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-800 font-black flex items-center justify-center shrink-0">2</span>
                        <div><strong>تم سحب العينة (Sample Collected):</strong> قام فني المختبر بسحب وتكويد العينة.</div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50 border border-gray-100 text-xs">
                        <span class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-800 font-black flex items-center justify-center shrink-0">3</span>
                        <div><strong>قيد التحليل (In Progress):</strong> العينة داخل أجهزة التحاليل ويجري تسجيل النتائج.</div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900">
                        <span class="w-6 h-6 rounded-lg bg-emerald-200 text-emerald-800 font-black flex items-center justify-center shrink-0">4</span>
                        <div><strong>مكتمل ومعتمد (Completed):</strong> النتيجة مدخلة ومعتمدة، وجاهزة للطباعة والظهور في ملف الطبيب.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-purple-50/50 border border-purple-100 text-xs text-purple-900 flex items-start gap-3">
            <i class="fa-solid fa-qrcode text-purple-700 text-lg mt-0.5"></i>
            <div>
                <strong>التقرير المخبري المعتمد والطباعة:</strong>
                <p class="mt-1 leading-relaxed text-gray-700">
                    يمكن طباعة تقرير التحليل المخبري الرسمي بنقرة واحدة عبر زر <span class="bg-white px-2 py-0.5 rounded border border-purple-200 font-bold">طباعة التقرير</span>؛ حيث يتم توليد كود QR للتحقق وتوقيع أخصائي المختبر وهيدر العيادة ومقارنة نتائج الفحص بالمعدلات الطبيعية مع تمييز القيم الشاذة أو الحرجة (Abnormal Flags) باللون الأحمر.
                </p>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 6: Pharmacy & Inventory Module -->
    <!-- ========================================================================= -->
    <div id="section-pharmacy" x-show="matchesSearch('الصيدلية الأدوية المخزون صرف الروشتة تاريخ الصلاحية الرصيد دواء صيدلي Pharmacy Medicine') && (selectedRole === 'all' || selectedRole === 'pharmacy' || selectedRole === 'doctor')" class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg font-black">6</span>
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-gray-900">قسم الصيدلية وإدارة المخزون الدوائي (Pharmacy & Inventory) 💊</h2>
                    <p class="text-xs font-bold text-gray-500">إدارة الأدوية والمخازن، صرف الوصفات الطبية، والإنذار المبكر لنفاد وصلاحية الأدوية</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('medicines.index') }}" class="text-xs bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold px-3 py-1.5 rounded-xl transition">أدوية المخزون</a>
                <a href="{{ route('prescriptions.index') }}" class="text-xs bg-rose-600 hover:bg-rose-700 text-white font-bold px-3 py-1.5 rounded-xl transition">الوصفات الطبية</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Box 1 -->
            <div class="p-5 rounded-2xl border border-gray-100 bg-gray-50 space-y-3">
                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-lg font-black">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <h4 class="text-sm font-black text-gray-800">بيانات الأدوية والمخزون</h4>
                <p class="text-xs text-gray-600 leading-relaxed">
                    تسجيل كل دواء بالاسم التجاري والعلمي، الشكل الدوائي (أقراص، شراب، حقن، مرهم)، التركيز، الشركة، رقم التشغيلة (Batch)، تاريخ الصلاحية، وسعر الشراء وسعر البيع.
                </p>
            </div>

            <!-- Box 2 -->
            <div class="p-5 rounded-2xl border border-gray-100 bg-gray-50 space-y-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg font-black">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h4 class="text-sm font-black text-gray-800">نظام الإنذار المبكر الذكي</h4>
                <p class="text-xs text-gray-600 leading-relaxed">
                    مؤشرات تفاعلية تلقائية تحذر الصيدلي من:
                    <br>• <strong class="text-rose-600">أدوية قاربت على النفاد:</strong> عند هبوط الكمية عن حد الطلب الأدنى.
                    <br>• <strong class="text-amber-600">أدوية قريبة الانتهاء:</strong> الأدوية التي ستنتهي صلاحيتها خلال 60 يوماً.
                </p>
            </div>

            <!-- Box 3 -->
            <div class="p-5 rounded-2xl border border-gray-100 bg-gray-50 space-y-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg font-black">
                    <i class="fa-solid fa-hand-holding-medical"></i>
                </div>
                <h4 class="text-sm font-black text-gray-800">صرف الوصفات بنقرة زر</h4>
                <p class="text-xs text-gray-600 leading-relaxed">
                    تصل الوصفة من عيادة الطبيب فوراً، يراجعها الصيدلي، وبالضغط على زر <strong class="text-emerald-700">صرف الوصفة</strong> يتم خصم الكميات من المخزون تلقائياً وتسجيل حركة المخزون وتحديث حالة الوصفة إلى "مصروفة".
                </p>
            </div>
        </div>

        <!-- Extra Tips on Pharmacy -->
        <div class="border-t border-gray-100 pt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div class="p-4 rounded-2xl bg-white border border-gray-200 space-y-1">
                <strong class="text-gray-800 block font-black"><i class="fa-solid fa-sliders text-rose-500 ml-1"></i> التسويات الجردية اليدوية (Stock Adjustment):</strong>
                <p class="text-gray-600 leading-relaxed">
                    يمكن للصيدلي تعديل كمية أي دواء يدوياً عند استلام شحنة جديدة (توريد) أو عند وجود هدر أو كسر أو جرد دوري، مع تدوين سبب التعديل ليتم تسجيله في جدول حركات المخزون.
                </p>
            </div>
            <div class="p-4 rounded-2xl bg-white border border-gray-200 space-y-1">
                <strong class="text-gray-800 block font-black"><i class="fa-solid fa-print text-teal-600 ml-1"></i> طباعة الروشتة وإرشادات الاستخدام:</strong>
                <p class="text-gray-600 leading-relaxed">
                    نموذج طباعة روشتة رسمي يشمل اسم المريض ورقم الملف واسم الطبيب وتاريخ الوصفة وتعليمات تناول كل دواء والجرعات بدقة لتسليمها للمريض.
                </p>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 7: Radiology Department -->
    <!-- ========================================================================= -->
    <div id="section-radiology" x-show="matchesSearch('الأشعة التصوير تقرير إشعاعي رنين سونار سينية X-Ray Radiology') && (selectedRole === 'all' || selectedRole === 'doctor' || selectedRole === 'admin')" class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-lg font-black">7</span>
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-gray-900">قسم الأشعة والتصوير الطبي (Radiology Department) 🩻</h2>
                    <p class="text-xs font-bold text-gray-500">طلبات الفحص الإشعاعي، رفع الصور الطبية، وتدوين تقارير الاستشاريين</p>
                </div>
            </div>
            <a href="{{ route('radiology.index') }}" class="text-xs bg-sky-50 hover:bg-sky-100 text-sky-700 font-bold px-3 py-1.5 rounded-xl transition flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> قائمة الأشعة
            </a>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-2">
                    <span class="font-black text-sky-700 block text-sm">1. إنشاء طلب الأشعة:</span>
                    <p class="text-gray-600 leading-relaxed">
                        يتم إما مباشرة من شاشة الزيارة بواسطة الطبيب أو من قسم الاستقبال، مع تحديد العضو المراد تصويره، نوع الأشعة (X-Ray, Ultrasound, CT, MRI)، والأسباب السريرية.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-2">
                    <span class="font-black text-sky-700 block text-sm">2. رفع الصور والأفلام:</span>
                    <p class="text-gray-600 leading-relaxed">
                        يقوم فني الأشعة بعد إتمام الفحص برفع الصور والمستندات الطبية بصيغ الصور المختلفة أو ملفات PDF وربطها بالطلب مباشرة.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-2">
                    <span class="font-black text-sky-700 block text-sm">3. كتابة التقرير والاعتماد:</span>
                    <p class="text-gray-600 leading-relaxed">
                        يكتب أخصائي الأشعة النتيجة والتشخيص الإشعاعي (Findings & Impression) واعتماد الطلب ليكتمل ويصبح متاحاً فوراً للطبيب المعالج.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 8: Insurance & Claims Management -->
    <!-- ========================================================================= -->
    <div id="section-insurance" x-show="matchesSearch('التأمين المطالبات نسبة التحمل بوليصة شركة تأمين كلاس Copay Claim Insurance') && (selectedRole === 'all' || selectedRole === 'insurance' || selectedRole === 'finance' || selectedRole === 'reception')" class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg font-black">8</span>
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-gray-900">التأمين الطبي وإدارة المطالبات والشركات (Insurance & Claims) 🛡️</h2>
                    <p class="text-xs font-bold text-gray-500">تسجيل بوالص المرضى، حساب نسب التحمل آلياً، ومتابعة اعتمادات وسداد المطالبات</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('insurance-companies.index') }}" class="text-xs bg-teal-50 hover:bg-teal-100 text-teal-700 font-bold px-3 py-1.5 rounded-xl transition">شركات التأمين</a>
                <a href="{{ route('insurance-claims.index') }}" class="text-xs bg-teal-700 hover:bg-teal-800 text-white font-bold px-3 py-1.5 rounded-xl transition">مطالبات التأمين</a>
            </div>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Companies and Policies -->
                <div class="space-y-3">
                    <h3 class="text-sm font-black text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-teal-600"></i> شركات التأمين وبطاقات المرضى
                    </h3>
                    <ul class="space-y-2 text-xs text-gray-600 leading-relaxed">
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-circle-dot text-teal-500 mt-1 shrink-0 text-[10px]"></i>
                            <div><strong>دليل الشركات:</strong> إدخال شركات وهيئات التأمين (مثل: بوبا، التعاونية، ميدغلف، تكافل الراجحي، سلامة) ونسبة الخصم المعتمدة وبيانات الاتصال.</div>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-circle-dot text-teal-500 mt-1 shrink-0 text-[10px]"></i>
                            <div><strong>ربط المريض بالبوليصة:</strong> تسجيل بطاقة تأمين المريض مع إدخال: رقم البوليصة، رقم العضوية، فئة التأمين (Class VIP, A, B, C)، نسبة تحمل المريض (Copay %)، والحد الأقصى للتحمل (Max Copay).</div>
                        </li>
                    </ul>
                </div>

                <!-- Claims and Calculation -->
                <div class="space-y-3">
                    <h3 class="text-sm font-black text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-calculator text-teal-600"></i> الاحتساب الآلي ودورة حياة المطالبة
                    </h3>
                    <ul class="space-y-2 text-xs text-gray-600 leading-relaxed">
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-circle-dot text-teal-500 mt-1 shrink-0 text-[10px]"></i>
                            <div><strong>الاحتساب الدقيق لحصة المريض والتأمين:</strong> يحسب النظام آلياً مبلغ التحمل (Patient Share) والمبلغ المتبقي على شركة التأمين (Claim Amount) مع التوقف التلقائي عند الحد الأقصى المحدد في بطاقة المريض.</div>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-circle-dot text-teal-500 mt-1 shrink-0 text-[10px]"></i>
                            <div><strong>تتبع حالات المطالبة:</strong>
                                <span class="bg-gray-100 text-gray-700 px-1.5 py-0.5 rounded font-bold text-[10px]">معلقة</span> $\rightarrow$
                                <span class="bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded font-bold text-[10px]">موافقة مبدئية</span> $\rightarrow$
                                <span class="bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded font-bold text-[10px]">مقبولة</span> $\rightarrow$
                                <span class="bg-teal-100 text-teal-800 px-1.5 py-0.5 rounded font-bold text-[10px]">مسددة</span> (أو <span class="bg-rose-100 text-rose-700 px-1.5 py-0.5 rounded font-bold text-[10px]">مرفوضة</span> مع تدوين سبب الرفض).
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bg-teal-50 rounded-2xl p-4 border border-teal-200 text-xs text-teal-900 flex items-center justify-between">
                <div>
                    <strong class="font-black">نموذج المطالبة التأمينية الرسمي المعتمد:</strong>
                    <p class="text-gray-600 mt-0.5">يمكن فتح أي مطالبة وطباعة نموذج المطالبة الطبية الموحد المشتمل على بيانات المريض، رقم الموافقة، تفاصيل الخدمات، والمبالغ المستحقة.</p>
                </div>
                <i class="fa-solid fa-file-invoice text-teal-600 text-2xl ml-4"></i>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 9: Billing, Invoices & Financial Reports -->
    <!-- ========================================================================= -->
    <div id="section-billing" x-show="matchesSearch('الفواتير المالية الحسابات الدفعات سداد ضريبة سند قبض تقرير مالي Invoice Payment VAT') && (selectedRole === 'all' || selectedRole === 'finance' || selectedRole === 'reception')" class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg font-black">9</span>
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-gray-900">المالية، الفواتير، الدفعات والضرائب (Billing & Finance) 💰</h2>
                    <p class="text-xs font-bold text-gray-500">إصدار الفواتير المجمعة، احتساب الضريبة، وإدارة المدفوعات والتقارير المالية</p>
                </div>
            </div>
            <a href="{{ route('invoices.index') }}" class="text-xs bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold px-3 py-1.5 rounded-xl transition flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> قائمة الفواتير
            </a>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-2">
                    <span class="font-black text-gray-800 block text-sm">1. الفاتورة الشاملة</span>
                    <p class="text-gray-600 leading-relaxed">
                        تتضمن الفاتورة كل البنود: رسوم الكشف الطبي، بنود التحاليل المخبرية، فحوصات الأشعة، والأدوية المصروفة.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-2">
                    <span class="font-black text-gray-800 block text-sm">2. الضريبة والخصم</span>
                    <p class="text-gray-600 leading-relaxed">
                        احتساب آلي لضريبة القيمة المضافة (VAT المحددة في الإعدادات) مع إمكانية تطبيق خصم إضافي على الفاتورة.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-2">
                    <span class="font-black text-gray-800 block text-sm">3. تعدد طرق السداد</span>
                    <p class="text-gray-600 leading-relaxed">
                        تسجيل الدفعات بطرق دفع متعددة: (نقدي Cash، بطاقة مدى/شبكة، فيزا/ماستركارد، تحويل بنكي).
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-2">
                    <span class="font-black text-gray-800 block text-sm">4. الدفعات الجزئية</span>
                    <p class="text-gray-600 leading-relaxed">
                        إمكانية سداد جزء من المبلغ المتبقي، ويقوم النظام بتحديث حالة الفاتورة تلقائياً إلى (مدفوعة جزئياً) وعرض الرصيد المتبقي بدقة.
                    </p>
                </div>
            </div>

            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-200 text-xs text-gray-700 space-y-2">
                <div class="flex items-center gap-2 font-black text-gray-900">
                    <i class="fa-solid fa-chart-line text-emerald-600"></i> التقارير المالية اللحظية في لوحة القيادة:
                </div>
                <p class="leading-relaxed">
                    توفر لوحة التحكم الرئيسية (<a href="{{ route('dashboard') }}" class="text-teal-600 font-bold underline">Dashboard</a>) رسوماً بيانية ومؤشرات للأداء المالي: إيراد اليوم، إيراد الشهر الحالي، إجمالي المبالغ غير المحصلة (المتبقية على المرضى)، وحجم الإيرادات لآخر 7 أيام.
                </p>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 10: Doctors, Schedules, Clinics & Services -->
    <!-- ========================================================================= -->
    <div id="section-doctors-schedules" x-show="matchesSearch('الأطباء الجداول العيادات الخدمات مواعيد العمل الدوام إجازات الدكتور Doctor Schedule Clinic Service') && (selectedRole === 'all' || selectedRole === 'admin' || selectedRole === 'reception')" class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-lg font-black">10</span>
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-gray-900">إدارة الأطباء، جداول الدوام، العيادات وقائمة الخدمات</h2>
                    <p class="text-xs font-bold text-gray-500">البنية التحتية الطبية لتنظيم العيادات، أوقات العمل، والأسعار</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('doctors.index') }}" class="text-xs bg-cyan-50 hover:bg-cyan-100 text-cyan-700 font-bold px-3 py-1.5 rounded-xl transition">الأطباء</a>
                <a href="{{ route('doctor-schedules.index') }}" class="text-xs bg-cyan-700 hover:bg-cyan-800 text-white font-bold px-3 py-1.5 rounded-xl transition">جداول الدوام</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs">
            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-2">
                <strong class="text-gray-900 block font-black text-sm"><i class="fa-solid fa-user-doctor text-cyan-600 ml-1"></i> ملف الطبيب والتخصص:</strong>
                <p class="text-gray-600 leading-relaxed">
                    تسجيل الطبيب برقم الرخصة المهنية، التخصص، سنوات الخبرة، وتعيين العيادة التي يباشر فيها، مع تحديد سعر الكشفية الأساسي.
                </p>
            </div>

            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-2">
                <strong class="text-gray-900 block font-black text-sm"><i class="fa-solid fa-calendar-check text-cyan-600 ml-1"></i> جداول الدوام وفترات الكشف:</strong>
                <p class="text-gray-600 leading-relaxed">
                    تحديد أيام عمل الطبيب في الأسبوع، وساعات البدء والانتهاء، مع تحديد مدة الكشف بالدقائق (Slot Duration مثلاً 20 دقيقة) لحساب أوقات الحجز تلقائياً.
                </p>
            </div>

            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-2">
                <strong class="text-gray-900 block font-black text-sm"><i class="fa-solid fa-bed text-cyan-600 ml-1"></i> إدارة الإجازات والاستئذانات:</strong>
                <p class="text-gray-600 leading-relaxed">
                    تسجيل إجازات الأطباء الرسمية والطارئة، لمنع المرضى أو الاستقبال من حجز أي مواعيد في أيام أو ساعات غياب الطبيب.
                </p>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 11: Security, Roles & Impersonation -->
    <!-- ========================================================================= -->
    <div id="section-roles-security" x-show="matchesSearch('الصلاحيات الأمان المستخدمين الأدوار تقمص الموظف أمان حماية User Role Permission Impersonate') && (selectedRole === 'all' || selectedRole === 'admin')" class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center text-lg font-black">11</span>
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-gray-900">الأدوار، الصلاحيات، الأمان، وميزة تقمص المستخدم (Security & Roles) 🔐</h2>
                    <p class="text-xs font-bold text-gray-500">حماية البيانات الطبية وتخصيص صلاحيات كل موظف وفق مسؤوليته</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('roles.index') }}" class="text-xs bg-red-50 hover:bg-red-100 text-red-700 font-bold px-3 py-1.5 rounded-xl transition">الأدوار</a>
                <a href="{{ route('system-users.index') }}" class="text-xs bg-red-600 hover:bg-red-700 text-white font-bold px-3 py-1.5 rounded-xl transition">المستخدمين</a>
            </div>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-2">
                    <strong class="text-gray-900 block font-black text-sm">مستويات الصلاحيات (RBAC)</strong>
                    <p class="text-gray-600 leading-relaxed">
                        يدعم النظام حزم صلاحيات تفصيلية (عرض، إنشاء، تعديل، حذف) مقسمة حسب التخصص: المدير العام، الطبيب، موظف الاستقبال، الصيدلي، فني المختبر، أخصائي الأشعة، والمحاسب.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-2">
                    <strong class="text-gray-900 block font-black text-sm">حماية الأدوار الحساسة</strong>
                    <p class="text-gray-600 leading-relaxed">
                        يمنع النظام برمجياً حذف أو تعطيل حساب أو دور <span class="text-red-600 font-bold">Super Admin</span> أو قيام المدير بتعطيل حسابه الشخصي لضمان استمرارية الوصول للنظام دائماً.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-2">
                    <strong class="text-gray-900 block font-black text-sm">تقمص المستخدم (Impersonation)</strong>
                    <p class="text-gray-600 leading-relaxed">
                        ميزة متقدمة تتيح لمدير النظام الدخول المؤقت بحساب أي موظف لمعاينة واجهته وحل مشكلاته دون طلب كلمة مروره، مع زر عائم للعودة للحساب الأصلي بنقرة واحدة.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 12: Audit Logs & Settings & Backups -->
    <!-- ========================================================================= -->
    <div id="section-audit-settings" x-show="matchesSearch('سجل الرقابة التدقيق التراجع الإعدادات النسخ الاحتياطي باك اب الشعار الضريبة الخط Audit Settings Backup') && (selectedRole === 'all' || selectedRole === 'admin')" class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center text-lg font-black">12</span>
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-gray-900">سجل التدقيق والرقابة، الإعدادات والنسخ الاحتياطي (Audit & Settings) ⚙️</h2>
                    <p class="text-xs font-bold text-gray-500">حماية النظام، رصد العمليات التاريخية، والنسخ الاحتياطي لقواعد البيانات والملفات</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('audit_logs.index') }}" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold px-3 py-1.5 rounded-xl transition">سجل التدقيق</a>
                <a href="{{ route('settings.index') }}" class="text-xs bg-[#0b4a60] hover:bg-teal-900 text-white font-bold px-3 py-1.5 rounded-xl transition">إعدادات النظام</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
            <div class="space-y-3 p-4 rounded-2xl bg-gray-50 border border-gray-100">
                <h4 class="font-black text-gray-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-teal-600"></i> سجل التدقيق والرقابة الشامل (Audit Logs):
                </h4>
                <p class="text-gray-600 leading-relaxed">
                    يوثق النظام كل عملية حفظ، تعديل، أو حذف تتم على مستوى العيادة:
                    <br>• من الموظف الذي قام بالعملية؟
                    <br>• متى حدثت بالساعة والدقيقة والثانية وعنوان الـ IP؟
                    <br>• مقارنة القيم السابقة والقيم الجديدة (Old vs New Data).
                    <br>• ميزة التراجع الذكي (Rollback) لاستعادة البيانات السابقة في حال حدوث خطأ غير مقصود.
                </p>
            </div>

            <div class="space-y-3 p-4 rounded-2xl bg-gray-50 border border-gray-100">
                <h4 class="font-black text-gray-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-down text-blue-600"></i> النسخ الاحتياطي وتخصيص الهوية:
                </h4>
                <p class="text-gray-600 leading-relaxed">
                    من خلال شاشة الإعدادات:
                    <br>• <strong>النسخ الاحتياطي:</strong> إنشاء نسخة احتياطية كاملة لقاعدة البيانات والملفات بنقرة زر وتنزيلها للحفظ في مكان آمن.
                    <br>• <strong>الهوية والشعار:</strong> رفع شعار العيادة (Logo) واسم المنشأة الذي يظهر في الفواتير والروشتات والتقارير.
                    <br>• <strong>الخط العربي:</strong> إمكانية اختيار الخط المفضل للواجهة (Cairo, Tajawal, Almarai, Alexandria).
                    <br>• <strong>الضريبة والعملة:</strong> تعيين نسبة الضريبة والعملة الرسمية.
                </p>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 13: WhatsApp Integration & Multi-Mode Messaging -->
    <!-- ========================================================================= -->
    <div id="section-whatsapp-integration" x-show="matchesSearch('واتساب whatsapp رسائل إشعارات تنبيهات باركود qr كود تذكير تأكيد')" class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-emerald-100 space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg font-black">
                    <i class="fa-brands fa-whatsapp text-2xl"></i>
                </span>
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-gray-900">13. منظومة إشعارات وربط الواتساب الكاملة (WhatsApp Integration)</h2>
                    <p class="text-xs font-bold text-gray-500">إرسال تنبيهات المواعيد وتأكيد الحجز ونتائج المختبر بثلاث طرق تشغيل ذكية</p>
                </div>
            </div>
            <span class="text-xs bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full font-bold">ميزة مضافة حديثاً</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200/80 space-y-2">
                <h4 class="font-black text-emerald-950 flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-qrcode text-emerald-600"></i> 1. الباركود المحلي (Baileys Engine):
                </h4>
                <p class="text-gray-700 leading-relaxed font-medium">
                    توليد رمز QR مشفر حقيقي محلياً معتمد من WhatsApp Multi-Device. يتم مسحه مرة واحدة من هاتف العيادة بدون أي وسيط ولا رسوم شهرية (0$).
                </p>
            </div>
            <div class="p-4 rounded-2xl bg-teal-50/60 border border-teal-200/80 space-y-2">
                <h4 class="font-black text-teal-950 flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-paper-plane text-teal-600"></i> 2. المراسلة المباشرة (Click-to-Chat):
                </h4>
                <p class="text-gray-700 leading-relaxed font-medium">
                    إرسال فوري بنقرة واحدة من جدول المواعيد وسجل المرضى يفتح محادثة واتساب الرسمية مباشرة مع المريض بالنص المخصص والبيانات معبأة تلقائياً.
                </p>
            </div>
            <div class="p-4 rounded-2xl bg-purple-50/60 border border-purple-200/80 space-y-2">
                <h4 class="font-black text-purple-950 flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-cloud text-purple-600"></i> 3. المزود الخارجي (UltraMsg API):
                </h4>
                <p class="text-gray-700 leading-relaxed font-medium">
                    ربط سحابي عبر بوابات الـ API الجاهزة. يدخل المدير معرف الجلسة والتوكن لتعمل الإشعارات سحابياً عبر خوادم UltraMsg.
                </p>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200 space-y-2 text-xs">
            <h4 class="font-black text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-file-lines text-teal-600"></i> القوالب التلقائية وسجل الإشعارات:
            </h4>
            <p class="text-gray-600 leading-relaxed">
                يدعم النظام 3 قوالب أساسية: <strong>تأكيد الحجز</strong>، <strong>تذكير الموعد القادم</strong>، و<strong>إشعار جاهزية نتائج التحاليل</strong> مع دعم المتغيرات الديناميكية مثل <code>{patient_name}</code> و <code>{doctor_name}</code> و <code>{time}</code>. كما تتوفر شاشة سجلات شاملة عبر <code>/settings/whatsapp-logs</code> لمتابعة كل رسالة وتوقيتها وحالة تسليمها.
            </p>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 14: Security Hardening & Rate Limiting -->
    <!-- ========================================================================= -->
    <div id="section-security-hardening" x-show="matchesSearch('أمان حماية ثغرات صلاحيات تخمين لوحة الإدارة تسجيل دخول rate limit super admin')" class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-rose-100 space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-700 flex items-center justify-center text-lg font-black">
                    <i class="fa-solid fa-shield-halved text-xl"></i>
                </span>
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-gray-900">14. الحماية الأمنية المتقدمة وإغلاق الثغرات (Security Hardening)</h2>
                    <p class="text-xs font-bold text-gray-500">حماية مسارات الإدارة الحساسة، منع التخمين، وتأمين حسابات المدراء</p>
                </div>
            </div>
            <span class="text-xs bg-rose-100 text-rose-800 px-3 py-1 rounded-full font-bold">أمان وترقية أمنية</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200 space-y-2">
                <h4 class="font-black text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-lock text-rose-600"></i> حماية مسارات الإدارة (Admin Route Protection):
                </h4>
                <p class="text-gray-600 leading-relaxed">
                    تم قصر مسارات النظام الحساسة: الإعدادات (<code>/settings</code>)، الصلاحيات (<code>/roles</code>)، إدارة الموظفين (<code>/system-users</code>)، وسجلات التدقيق (<code>/audit-logs</code>) حصرياً على دور <strong>Super Admin</strong>، مع إرجاع استجابة حظر 403 Forbidden فورية لأي دور آخر.
                </p>
            </div>
            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200 space-y-2">
                <h4 class="font-black text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-user-shield text-blue-600"></i> منع التخمين وحماية المدير العام:
                </h4>
                <p class="text-gray-600 leading-relaxed">
                    تفعيل محدد الطلبات الذكي (Rate Limiting) على شاشة الدخول لمنع هجمات التخمين الآلية، مع حظر تعطيل أو حذف حساب المدير العام في النظام نهائياً.
                </p>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 15: Double Booking Prevention -->
    <!-- ========================================================================= -->
    <div id="section-double-booking" x-show="matchesSearch('حجز مزدوج تضارب مواعيد أطباء double booking')" class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-amber-100 space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center text-lg font-black">
                    <i class="fa-solid fa-calendar-xmark text-xl"></i>
                </span>
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-gray-900">15. منع الحجز المزدوج وتضارب مواعيد الأطباء (Anti-Double Booking)</h2>
                    <p class="text-xs font-bold text-gray-500">حماية جداول الأطباء ومنع تسجيل أكثر من مريض في نفس التوقيت بدقة متناهية</p>
                </div>
            </div>
            <span class="text-xs bg-amber-100 text-amber-800 px-3 py-1 rounded-full font-bold">حماية الجداول</span>
        </div>

        <div class="p-4 rounded-2xl bg-amber-50/50 border border-amber-200 space-y-2 text-xs">
            <p class="text-gray-700 leading-relaxed font-medium">
                تم دمج آلية تحقق فورية في كل من <strong>لوحة حجز الاستقبال</strong> و<strong>بوابة الحجز الإلكتروني العامة</strong>، بحيث يقوم النظام بفحص ارتباط الطبيب بأي موعد قائم في نفس الدقيقة. في حال وجود حجز قائم، يتم رفض العملية وإرشاد المستخدم لاختيار خانة زمنية بديلة، مع تطبيق قفل المعاملات البرمجي لمنع الحجز المتزامن في أجزاء الثانية.
            </p>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 16: Hostinger Deployment Architecture -->
    <!-- ========================================================================= -->
    <div id="section-hostinger-guide" x-show="matchesSearch('هوستنجر hostinger استضافة رفع خادم vps نشر')" class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-indigo-100 space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-lg font-black">
                    <i class="fa-solid fa-server text-xl"></i>
                </span>
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-gray-900">16. الدليل التقني لتشغيل الواتساب على استضافة هوستنجر (Hostinger Setup)</h2>
                    <p class="text-xs font-bold text-gray-500">خيارات النشر على الاستضافة المشتركة والـ VPS وحلول تشغيل الباركود</p>
                </div>
            </div>
            <span class="text-xs bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full font-bold">دليل النشر</span>
        </div>

        <div class="space-y-3 text-xs">
            <p class="text-gray-700 leading-relaxed font-medium">
                يعمل نظام لارافيل بسلاسة تامة على كافة خطط هوستنجر. ولتشغيل خدمة الباركود المجانية (Baileys) بدون قيود الاستضافات المشتركة، يمكنك استخدام أحد هذه الحلول:
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200 space-y-2">
                    <h4 class="font-black text-indigo-900 flex items-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up text-indigo-600"></i> خيار 1: الخادم السحابي المجاني (Render / Railway):
                    </h4>
                    <p class="text-gray-600 leading-relaxed">
                        استضافة موقع العيادة على هوستنجر، ورفع كود الواتساب (whatsapp-service) على خدمة سحابية مجانية تدعم Node.js مثل Render.com ليبقى يعمل 24/7.
                    </p>
                </div>
                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200 space-y-2">
                    <h4 class="font-black text-indigo-900 flex items-center gap-2">
                        <i class="fa-solid fa-network-wired text-teal-600"></i> خيار 2: نفق الأمان المحلي (Cloudflare Tunnel):
                    </h4>
                    <p class="text-gray-600 leading-relaxed">
                        تشغيل الخدمة المحلية على كمبيوتر الاستقبال بالعيادة وربطها بموقع هوستنجر عبر نفق آمن مجاني (Cloudflare Tunnel) لنقل الإشعارات بدون تكاليف.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 17: Frequently Asked Questions & Troubleshooting -->
    <!-- ========================================================================= -->
    <div id="section-faq" x-show="matchesSearch('الأسئلة الشائعة حل المشاكل خطأ استرجاع مريض نقص مخزون حذف FAQ Troubleshooting')" class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-3xl p-6 md:p-8 shadow-sm border border-amber-200 space-y-6">
        <div class="flex items-center gap-3 border-b border-amber-200 pb-4">
            <span class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-lg font-black">
                <i class="fa-solid fa-question"></i>
            </span>
            <div>
                <h2 class="text-xl md:text-2xl font-black text-amber-950">الأسئلة الشائعة وحلول المواقف اليومية (FAQ & Troubleshooting)</h2>
                <p class="text-xs font-bold text-amber-800">إرشادات سريعة للتعامل مع الحالات التشغيلية والسريرية الشائعة</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div class="bg-white p-4 rounded-2xl border border-amber-200/70 space-y-1.5 shadow-sm">
                <strong class="font-black text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-circle-question text-amber-500"></i> كيف أسترجع مريضاً أو عيادة تم حذفها بالخطأ؟
                </strong>
                <p class="text-gray-600 leading-relaxed">
                    لا يتم حذف البيانات نهائياً في النظام بل تؤرشف تلقائياً (Soft Delete). ادخل لصفحة المرضى أو العيادات وفعّل فلتر "المؤرشفة"، ثم انقر على زر <span class="text-teal-700 font-bold">استعادة</span> ليعود الملف فوراً بكامل بياناته وتاريخه الطبي.
                </p>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-amber-200/70 space-y-1.5 shadow-sm">
                <strong class="font-black text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-circle-question text-amber-500"></i> ماذا أفعل إذا ظهرت رسالة أن كمية الدواء لا تكفي للصرف؟
                </strong>
                <p class="text-gray-600 leading-relaxed">
                    توجه إلى قسم الصيدلية <a href="{{ route('medicines.index') }}" class="text-rose-600 font-bold underline">الأدوية</a>، وابحث عن الصنف، ثم انقر على زر <span class="text-rose-700 font-bold">تعديل المخزون</span> وقم بإضافة الكمية المتوفرة الجديدة لفك التعليق وصرف الوصفة للمريض.
                </p>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-amber-200/70 space-y-1.5 shadow-sm">
                <strong class="font-black text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-circle-question text-amber-500"></i> كيف أسجل دفعة مالية على قسطين لنفس الفاتورة؟
                </strong>
                <p class="text-gray-600 leading-relaxed">
                    افتح الفاتورة، انقر على زر "تسجيل دفعة"، أدخل المبلغ المسدد اليوم، ثم احفظ. ستتحول الفاتورة إلى "مدفوعة جزئياً". عند حضور المريض لاحقاً، ادخل لنفس الفاتورة وسجل الدفعة المتبقية ليتحول الإشعار إلى "مدفوعة بالكامل".
                </p>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-amber-200/70 space-y-1.5 shadow-sm">
                <strong class="font-black text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-circle-question text-amber-500"></i> كيف أتعامل مع مطالبة تأمين تم رفضها من الشركة؟
                </strong>
                <p class="text-gray-600 leading-relaxed">
                    افتح المطالبة في قسم التأمين، وحدد الحالة "مرفوضة"، ثم دوّن سبب الرفض في الملاحظات (مثل: انتهاء صلاحية البوليصة أو عدم تغطية الإجراء). يمكنك لاحقاً تحويل المبلغ غير المغطى إلى فاتورة مباشرة على المريض.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection
