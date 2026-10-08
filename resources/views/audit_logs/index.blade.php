@extends('layouts.app')

@section('title', 'سجل التدقيق الأمني (Audit Trail)')
@section('header', 'سجل النشاطات')

@section('content')
<div x-data="{ showFilters: false }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-800 mb-1 flex items-center gap-2">
                <i class="fa-solid fa-shield-halved text-teal-600"></i> سجل التدقيق والنشاطات
            </h1>
            <p class="text-gray-500 text-sm font-bold">مراقبة حية لكل ما يحدث داخل النظام للحفاظ على الأمان.</p>
        </div>
        <div class="flex gap-2">
            <button @click="showFilters = !showFilters" class="bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 px-5 py-2.5 rounded-xl text-sm font-bold transition shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-filter"></i> الفلاتر المتقدمة
            </button>
            <button class="bg-gray-800 hover:bg-black text-white px-5 py-2.5 rounded-xl text-sm font-bold transition shadow-sm flex items-center gap-2" onclick="window.print()">
                <i class="fa-solid fa-print"></i> طباعة التقرير
            </button>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex gap-4 mb-6 border-b border-gray-200 print:hidden">
        <a href="{{ route('audit_logs.index', ['tab' => 'all']) }}" class="px-6 py-3 text-sm font-bold border-b-2 transition {{ $tab === 'all' ? 'border-teal-600 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-800' }} flex items-center gap-2">
            <i class="fa-solid fa-database"></i> سجل عمليات الموظفين والداتا
        </a>
        <a href="{{ route('audit_logs.index', ['tab' => 'auth']) }}" class="px-6 py-3 text-sm font-bold border-b-2 transition {{ $tab === 'auth' ? 'border-teal-600 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-800' }} flex items-center gap-2">
            <i class="fa-solid fa-fingerprint"></i> مراقبة تسجيل الدخول والخروج (IP Tracking)
        </a>
    </div>

    <!-- Advanced Filters Form -->
    <div x-show="showFilters" x-transition style="display: none;" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('audit_logs.index') }}" method="GET">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                
                <div class="lg:col-span-1">
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">بحث عام</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم الموظف..." class="w-full px-4 py-2.5 bg-gray-50 border border-transparent focus:border-teal-custom/50 focus:bg-white rounded-xl text-sm transition">
                </div>

                @if($tab === 'all')
                <div class="lg:col-span-1">
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">نوع العملية</label>
                    <select name="event_type" class="w-full px-4 py-2.5 bg-gray-50 border border-transparent focus:border-teal-custom/50 focus:bg-white rounded-xl text-sm transition font-bold text-gray-700 cursor-pointer">
                        <option value="all">الكل</option>
                        <option value="created" {{ request('event_type') == 'created' ? 'selected' : '' }}>إضافة (إنشاء)</option>
                        <option value="updated" {{ request('event_type') == 'updated' ? 'selected' : '' }}>تعديل (تحديث)</option>
                        <option value="deleted" {{ request('event_type') == 'deleted' ? 'selected' : '' }}>حذف</option>
                        <option value="rollback" {{ request('event_type') == 'rollback' ? 'selected' : '' }}>تراجع (استعادة)</option>
                    </select>
                </div>

                <div class="lg:col-span-1">
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">القسم المتأثر</label>
                    <select name="subject_type" class="w-full px-4 py-2.5 bg-gray-50 border border-transparent focus:border-teal-custom/50 focus:bg-white rounded-xl text-sm transition font-bold text-gray-700 cursor-pointer">
                        <option value="all">الكل</option>
                        <option value="Patient" {{ request('subject_type') == 'Patient' ? 'selected' : '' }}>المرضى</option>
                        <option value="User" {{ request('subject_type') == 'User' ? 'selected' : '' }}>المستخدمين (الأطباء والموظفين)</option>
                        <option value="Clinic" {{ request('subject_type') == 'Clinic' ? 'selected' : '' }}>العيادات</option>
                        <option value="Setting" {{ request('subject_type') == 'Setting' ? 'selected' : '' }}>الإعدادات</option>
                    </select>
                </div>
                @else
                <div class="lg:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">نوع العملية</label>
                    <select name="event_type" class="w-full px-4 py-2.5 bg-gray-50 border border-transparent focus:border-teal-custom/50 focus:bg-white rounded-xl text-sm transition font-bold text-gray-700 cursor-pointer">
                        <option value="all">الكل</option>
                        <option value="login" {{ request('event_type') == 'login' ? 'selected' : '' }}>تسجيل دخول</option>
                        <option value="logout" {{ request('event_type') == 'logout' ? 'selected' : '' }}>تسجيل خروج</option>
                    </select>
                </div>
                @endif

                <div class="lg:col-span-1">
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">من تاريخ</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-transparent focus:border-teal-custom/50 focus:bg-white rounded-xl text-sm transition">
                </div>

                <div class="lg:col-span-1 flex gap-2">
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase">إلى تاريخ</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-transparent focus:border-teal-custom/50 focus:bg-white rounded-xl text-sm transition">
                    </div>
                    <button type="submit" class="bg-teal-custom text-white w-12 h-[42px] rounded-xl flex items-center justify-center shadow-sm hover:bg-teal-700 transition self-end">
                        <i class="fa-solid fa-search"></i>
                    </button>
                    @if(request()->hasAny(['search', 'event_type', 'subject_type', 'date_from', 'date_to']))
                    <a href="{{ route('audit_logs.index', ['tab' => $tab]) }}" class="bg-red-50 text-red-500 w-12 h-[42px] rounded-xl flex items-center justify-center hover:bg-red-100 transition self-end">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                    @endif
                </div>

            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6 relative">
        <!-- Print Header -->
        <div class="hidden print:block text-center py-6 border-b border-gray-200">
            <h2 class="text-2xl font-black mb-2">{{ $tab === 'auth' ? 'تقرير حركة دخول وخروج الموظفين' : 'تقرير التدقيق الأمني للعمليات' }}</h2>
            <p class="text-gray-500">تم طباعته في: {{ now()->format('Y-m-d H:i') }} بواسطة {{ auth()->user()->name }}</p>
        </div>

        <div class="overflow-x-auto">
            @if($tab === 'all')
                <!-- DATA AUDIT TABLE -->
                <table class="w-full text-right text-sm">
                    <thead class="bg-gray-50 text-gray-600 font-bold border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">التاريخ والوقت</th>
                            <th class="px-6 py-4">الموظف (الفاعل)</th>
                            <th class="px-6 py-4 w-1/2">تفاصيل الإجراء (الوصف البشري)</th>
                            <th class="px-6 py-4 text-center">البيانات / التراجع</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-gray-700">
                        @forelse($logs as $log)
                        @php
                            $subjectName = class_basename($log->subject_type);
                            $arSubject = match($subjectName) {
                                'Patient' => 'المريض',
                                'User' => 'حساب الموظف',
                                'Clinic' => 'العيادة',
                                'Appointment' => 'الموعد',
                                'Setting' => 'إعدادات النظام',
                                default => $subjectName
                            };
                            
                            $recordName = '';
                            if ($log->subject) {
                                $recordName = $log->subject->name ?? $log->subject->title ?? $log->subject->full_name ?? "#" . $log->subject_id;
                            } else {
                                $recordName = "سجل محذوف (#$log->subject_id)";
                            }

                            $causerName = $log->causer->name ?? 'النظام الآلي';
                            $humanText = "";
                            
                            if ($log->event === 'created') {
                                $humanText = "قام <span class='font-bold'>$causerName</span> <strong>بإضافة</strong> $arSubject جديد باسم <span class='text-teal-700 font-bold'>($recordName)</span>";
                            } elseif ($log->event === 'deleted') {
                                $humanText = "قام <span class='font-bold'>$causerName</span> <strong class='text-red-600'>بحذف</strong> $arSubject <span class='text-teal-700 font-bold'>($recordName)</span> من النظام";
                            } elseif ($log->event === 'rollback') {
                                $humanText = "قام <span class='font-bold'>$causerName</span> <strong class='text-purple-600'>بالتراجع عن تعديل</strong> وإرجاع بيانات $arSubject <span class='text-teal-700 font-bold'>($recordName)</span> لحالتها السابقة";
                            } elseif ($log->event === 'updated') {
                                if (class_basename($log->subject_type) === 'Setting') {
                                    $settingKey = $log->subject->key ?? 'إعداد';
                                    $arSettingName = match($settingKey) {
                                        'app_name' => 'اسم العيادة/النظام',
                                        'app_font' => 'نوع الخط المعتمد',
                                        'app_logo' => 'شعار النظام الأساسي (اللوجو)',
                                        'app_favicon' => 'أيقونة المتصفح',
                                        default => $settingKey
                                    };
                                    $humanText = "قام <span class='font-bold'>$causerName</span> <strong>بتغيير إعدادات النظام</strong> وتحديداً قام بتعديل <span class='text-teal-700 font-bold'>($arSettingName)</span>";
                                } else {
                                    $changedFieldsText = "";
                                    if (isset($log->properties['attributes'])) {
                                        $keys = array_keys($log->properties['attributes']);
                                        $keys = array_filter($keys, fn($k) => !in_array($k, ['updated_at', 'created_at', 'last_login_at', 'remember_token']));
                                        
                                        if (count($keys) > 0) {
                                            $translatedKeys = array_map(function($k) {
                                                $t = trans('attributes.' . $k);
                                                return $t === 'attributes.' . $k ? $k : $t;
                                            }, $keys);
                                            
                                            if (count($translatedKeys) <= 3) {
                                                $changedFieldsText = "<span class='text-blue-600'>(" . implode('، ', $translatedKeys) . ")</span>";
                                            } else {
                                                $changedFieldsText = "<span class='text-blue-600'>(تعديلات جذرية على عدة حقول)</span>";
                                            }
                                        }
                                    }
                                    $humanText = "قام <span class='font-bold'>$causerName</span> <strong>بتحديث بيانات</strong> $arSubject <span class='text-teal-700 font-bold'>($recordName)</span> وتحديداً قام بتعديل $changedFieldsText";
                                }
                            }

                            $canRollback = in_array($log->event, ['updated', 'deleted']);
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4" dir="ltr">
                                <div class="text-xs font-bold text-gray-400 mb-0.5">{{ $log->created_at->format('Y-m-d') }}</div>
                                <div class="font-mono font-bold text-gray-700">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold shrink-0 overflow-hidden">
                                        @if($log->causer && $log->causer->avatar)
                                            <img src="{{ asset('storage/' . $log->causer->avatar) }}" class="w-full h-full object-cover">
                                        @else
                                            {{ mb_substr($log->causer->name ?? 'ن', 0, 1) }}
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800 text-sm">{{ $log->causer->name ?? 'النظام الآلي' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1.5">
                                    <div class="text-sm text-gray-700 leading-relaxed bg-gray-50 border border-gray-100 px-3 py-2 rounded-lg">
                                        {!! $humanText !!}
                                    </div>
                                    @if($log->event === 'rollback')
                                        <p class="text-xs text-purple-600 font-bold mt-1">
                                            <i class="fa-solid fa-clock-rotate-left"></i> {{ $log->description }}
                                        </p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2 print:hidden" x-data="{ openJSON: false }">
                                    <button @click="openJSON = true" class="w-9 h-9 rounded-lg bg-gray-50 border border-gray-100 text-blue-600 hover:bg-blue-50 flex items-center justify-center transition tooltip" title="مشاهدة الجدول التفصيلي للقيم">
                                        <i class="fa-solid fa-list-check text-sm"></i>
                                    </button>
                                    
                                    @if($canRollback)
                                    <form action="{{ route('audit_logs.rollback', $log->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في التراجع عن هذا التعديل؟')">
                                        @csrf
                                        <button type="submit" class="w-9 h-9 rounded-lg bg-purple-50 text-purple-600 hover:bg-purple-100 flex items-center justify-center transition tooltip" title="تراجع (إلغاء التعديل/الحذف)">
                                            <i class="fa-solid fa-rotate-left text-sm"></i>
                                        </button>
                                    </form>
                                    @else
                                    <div class="w-9 h-9 rounded-lg bg-gray-50 text-gray-300 flex items-center justify-center cursor-not-allowed tooltip" title="لا يمكن التراجع عن هذه العملية">
                                        <i class="fa-solid fa-ban text-[10px]"></i>
                                    </div>
                                    @endif

                                    <!-- Details Modal -->
                                    <div x-show="openJSON" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="openJSON = false"></div>
                                        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl z-10 overflow-hidden text-right border border-gray-100">
                                            <div class="bg-gray-50 p-5 border-b border-gray-100 flex justify-between items-center">
                                                <div>
                                                    <h3 class="font-black text-gray-800 text-sm flex items-center gap-2">
                                                        <i class="fa-solid fa-file-invoice text-teal-600"></i> تفاصيل بيانات الإجراء
                                                    </h3>
                                                </div>
                                                <button type="button" @click="openJSON = false" class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 hover:bg-gray-300 flex items-center justify-center transition">
                                                    <i class="fa-solid fa-xmark text-xs"></i>
                                                </button>
                                            </div>
                                            <div class="p-6">
                                                @if(isset($log->properties['attributes']))
                                                    <table class="w-full text-right text-sm border-collapse">
                                                        <thead>
                                                            <tr class="bg-gray-100 text-gray-600">
                                                                <th class="p-4 font-bold border-b border-gray-200 rounded-tr-xl w-1/3">الحقل المعدل</th>
                                                                @if(isset($log->properties['old']))
                                                                <th class="p-4 font-bold border-b border-gray-200 text-red-600 w-1/3">القيمة السابقة</th>
                                                                @endif
                                                                <th class="p-4 font-bold border-b border-gray-200 text-green-600 w-1/3 {{ !isset($log->properties['old']) ? 'rounded-tl-xl' : '' }}">القيمة الجديدة</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-100">
                                                            @foreach($log->properties['attributes'] as $key => $newValue)
                                                                @if(!in_array($key, ['updated_at', 'created_at', 'last_login_at', 'remember_token']))
                                                                <tr class="hover:bg-gray-50 transition">
                                                                    <td class="p-4 font-bold text-gray-700 bg-gray-50/50">
                                                                        {{ trans('attributes.' . $key) !== 'attributes.' . $key ? trans('attributes.' . $key) : $key }}
                                                                    </td>
                                                                    @if(isset($log->properties['old']))
                                                                    <td class="p-4 text-red-500 text-sm" dir="auto">
                                                                        {{ $log->properties['old'][$key] ?? '—' }}
                                                                    </td>
                                                                    @endif
                                                                    <td class="p-4 text-green-600 font-bold text-sm" dir="auto">
                                                                        {{ $newValue ?? '—' }}
                                                                    </td>
                                                                </tr>
                                                                @endif
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                @else
                                                    <div class="text-center py-8 text-gray-400">
                                                        <i class="fa-solid fa-folder-open text-4xl mb-3 text-gray-200"></i>
                                                        <p class="font-bold">العملية لا تحتوي على تغييرات في الجداول.</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center text-gray-500">
                                <i class="fa-solid fa-shield-blank text-5xl mb-4 text-gray-300"></i>
                                <p class="font-bold">لم يتم تسجيل أي نشاطات ضمن هذا النطاق.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                <!-- AUTH AUDIT TABLE (Logins & IP Tracking) -->
                <table class="w-full text-right text-sm">
                    <thead class="bg-gray-50 text-gray-600 font-bold border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">التاريخ والوقت</th>
                            <th class="px-6 py-4">الموظف</th>
                            <th class="px-6 py-4">نوع الحركة</th>
                            <th class="px-6 py-4">معلومات الجهاز وبصمة الاتصال (IP & Device)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-gray-700">
                        @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4" dir="ltr">
                                <div class="text-xs font-bold text-gray-400 mb-0.5">{{ $log->created_at->format('Y-m-d') }}</div>
                                <div class="font-mono font-bold text-gray-700">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm font-bold shrink-0 overflow-hidden border border-gray-200">
                                        @if($log->causer && $log->causer->avatar)
                                            <img src="{{ asset('storage/' . $log->causer->avatar) }}" class="w-full h-full object-cover">
                                        @else
                                            {{ mb_substr($log->causer->name ?? 'ن', 0, 1) }}
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800">{{ $log->causer->name ?? 'مجهول' }}</div>
                                        <div class="text-xs text-gray-500 font-mono" dir="ltr">{{ $log->causer->username ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($log->event === 'login')
                                    <span class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 border border-emerald-100 px-3 py-1.5 rounded-lg text-xs font-bold">
                                        <i class="fa-solid fa-arrow-right-to-bracket"></i> تسجيل دخول ناجح
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 bg-amber-50 text-amber-700 border border-amber-100 px-3 py-1.5 rounded-lg text-xs font-bold">
                                        <i class="fa-solid fa-arrow-right-from-bracket"></i> تسجيل خروج
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="bg-gray-50 border border-gray-100 rounded-lg p-3 inline-block w-full max-w-sm">
                                    <div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-200">
                                        <span class="text-[10px] font-bold text-gray-500 uppercase">IP Address</span>
                                        <span class="font-mono text-sm font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded" dir="ltr">
                                            {{ $log->properties['ip'] ?? 'غير متوفر' }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-600 leading-relaxed font-mono" dir="ltr" title="{{ $log->properties['user_agent'] ?? 'غير متوفر' }}">
                                        @php
                                            $ua = $log->properties['user_agent'] ?? 'غير متوفر';
                                            $icon = 'fa-desktop';
                                            if (stripos($ua, 'iPhone') !== false || stripos($ua, 'Android') !== false) $icon = 'fa-mobile-screen';
                                            if (stripos($ua, 'iPad') !== false) $icon = 'fa-tablet-screen-button';
                                        @endphp
                                        <i class="fa-solid {{ $icon }} text-gray-400 mr-1"></i>
                                        {{ Str::limit($ua, 60) }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center text-gray-500">
                                <i class="fa-solid fa-user-clock text-5xl mb-4 text-gray-300"></i>
                                <p class="font-bold">لم يتم تسجيل أي عمليات دخول أو خروج بعد.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- Pagination -->
    @if($logs->hasPages())
        <div class="flex justify-center print:hidden">
            {{ $logs->links() }}
        </div>
    @endif
</div>

<style>
    @media print {
        body { background: white; }
        aside, header, .print\:hidden { display: none !important; }
        main { padding: 0 !important; margin: 0 !important; }
        .shadow-sm, .rounded-2xl, .border { box-shadow: none !important; border-radius: 0 !important; border-color: #000 !important; }
    }
</style>
@endsection
