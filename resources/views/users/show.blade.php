@extends('layouts.app')

@section('title', 'الملف الشخصي للموظف: ' . $user->name)
@section('header', 'الملف الشامل للموظف')

@section('content')
<div x-data="{ activeTab: 'overview' }" class="space-y-6">
    
    <!-- Profile Header -->
    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 flex flex-col md:flex-row gap-6 md:gap-8 items-center md:items-start relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-teal-50 rounded-bl-full -z-10 opacity-50"></div>
        
        <div class="w-24 h-24 md:w-32 md:h-32 rounded-full border-4 border-white shadow-lg {{ $user->hasRole('طبيب') ? 'bg-blue-600' : 'bg-teal-600' }} text-white flex items-center justify-center text-4xl font-black shrink-0 overflow-hidden relative group">
            @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" class="w-full h-full object-cover">
            @else
                {{ mb_substr($user->name, 0, 1) }}
            @endif
        </div>

        <div class="flex-1 text-center md:text-right z-10">
            <h2 class="text-2xl font-black text-gray-900 mb-1 flex items-center justify-center md:justify-start gap-2">
                {{ $user->name }}
                @if($user->status === 'نشط')
                    <i class="fa-solid fa-circle-check text-green-500 text-sm" title="نشط"></i>
                @else
                    <i class="fa-solid fa-circle-xmark text-red-500 text-sm" title="موقوف"></i>
                @endif
            </h2>
            <p class="text-gray-500 font-mono text-sm mb-3" dir="ltr">{{ $user->email }} | {{ $user->phone ?? 'لا يوجد رقم جوال' }}</p>
            
            <div class="flex flex-wrap justify-center md:justify-start gap-2 mb-4">
                @forelse($user->roles as $role)
                    <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-lg text-xs font-bold border border-gray-200">
                        {{ $role->name === 'Super Admin' ? 'مدير النظام 👑' : $role->name }}
                    </span>
                @empty
                    <span class="bg-gray-50 text-gray-400 px-3 py-1 rounded-lg text-xs font-bold border border-gray-100">مستخدم عادي</span>
                @endforelse
                
                @if($user->hasRole('طبيب'))
                    <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-lg text-xs font-bold border border-blue-100"><i class="fa-solid fa-stethoscope"></i> {{ $user->specialization ?? 'طبيب عام' }}</span>
                @endif
            </div>
            
            <div class="flex gap-2 justify-center md:justify-start">
                <a href="{{ route('system-users.index') }}" class="bg-gray-50 hover:bg-gray-100 text-gray-700 px-4 py-2 rounded-xl text-sm font-bold border border-gray-200 transition">
                    <i class="fa-solid fa-arrow-right ml-1"></i> العودة
                </a>
            </div>
        </div>

        @if($user->hasRole('طبيب'))
        <div class="hidden lg:flex flex-col gap-3 shrink-0">
            <div class="bg-blue-50 border border-blue-100 p-3 rounded-xl text-center">
                <p class="text-[10px] font-black text-blue-400 uppercase">رقم الرخصة</p>
                <p class="font-mono font-bold text-blue-900" dir="ltr">{{ $user->license_number ?? 'N/A' }}</p>
            </div>
            <div class="bg-gray-50 border border-gray-100 p-3 rounded-xl text-center">
                <p class="text-[10px] font-black text-gray-400 uppercase">مدة الكشف</p>
                <p class="font-mono font-bold text-gray-900" dir="ltr">{{ $user->consultation_duration ?? 20 }} Min</p>
            </div>
        </div>
        @endif
    </div>

    <!-- Tabs Navigation -->
    <div class="flex flex-nowrap overflow-x-auto gap-2 border-b border-gray-200 custom-scrollbar pb-2">
        <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'bg-white text-teal-600 border-teal-200 shadow-sm' : 'bg-transparent text-gray-500 hover:bg-gray-50 border-transparent'" class="border px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-id-card"></i> نظرة عامة
        </button>
        <button @click="activeTab = 'permissions'" :class="activeTab === 'permissions' ? 'bg-white text-teal-600 border-teal-200 shadow-sm' : 'bg-transparent text-gray-500 hover:bg-gray-50 border-transparent'" class="border px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-shield-halved"></i> الصلاحيات الخاصة
        </button>
        <button @click="activeTab = 'logs'" :class="activeTab === 'logs' ? 'bg-white text-teal-600 border-teal-200 shadow-sm' : 'bg-transparent text-gray-500 hover:bg-gray-50 border-transparent'" class="border px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-clock-rotate-left"></i> سجل نشاط الموظف
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-r-4 border-green-500 text-green-700 p-4 rounded-xl flex items-center gap-3 shadow-sm">
            <i class="fa-solid fa-circle-check text-xl"></i>
            <p class="font-bold text-sm">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Overview Tab -->
    <div x-show="activeTab === 'overview'" x-transition.opacity class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @if($user->hasRole('طبيب'))
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <h3 class="font-black text-gray-800 mb-4 flex items-center gap-2 border-b border-gray-50 pb-3">
                    <i class="fa-solid fa-stethoscope text-blue-500"></i> بيانات الطبيب
                </h3>
                <ul class="space-y-4">
                    <li class="flex justify-between items-center text-sm">
                        <span class="font-bold text-gray-500">التخصص:</span>
                        <span class="font-black text-blue-700">{{ $user->specialization ?? 'غير محدد' }}</span>
                    </li>
                    <li class="flex justify-between items-center text-sm">
                        <span class="font-bold text-gray-500">رقم الرخصة الطبية:</span>
                        <span class="font-bold text-gray-800 font-mono" dir="ltr">{{ $user->license_number ?? 'غير محدد' }}</span>
                    </li>
                    <li class="flex justify-between items-center text-sm">
                        <span class="font-bold text-gray-500">مدة الكشف:</span>
                        <span class="font-bold text-gray-800">{{ $user->consultation_duration ?? 20 }} دقيقة</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="md:col-span-2">
            <div class="bg-white p-12 rounded-3xl shadow-sm border border-gray-100 text-center flex flex-col items-center justify-center h-full">
                <i class="fa-solid fa-chart-pie text-5xl text-gray-200 mb-4"></i>
                <h3 class="font-bold text-gray-700 text-lg mb-2">إحصائيات الطبيب (قريباً)</h3>
                <p class="text-sm text-gray-500 font-bold max-w-sm">ستظهر هنا إحصائيات دقيقة بعدد المرضى الذين عالجهم، المواعيد التي أنجزها، وإجمالي الإيرادات المرتبطة به.</p>
            </div>
        </div>
        @else
        <div class="md:col-span-3">
            <div class="bg-white p-12 rounded-3xl shadow-sm border border-gray-100 text-center flex flex-col items-center justify-center">
                <i class="fa-solid fa-chart-line text-5xl text-gray-200 mb-4"></i>
                <h3 class="font-bold text-gray-700 text-lg mb-2">لوحة إحصائيات الموظف</h3>
                <p class="text-sm text-gray-500 font-bold max-w-sm">نظام الإحصائيات الشامل قيد التطوير وسيتم ربطه بهذا الحساب قريباً.</p>
            </div>
        </div>
        @endif
    </div>

    <!-- Permissions Tab -->
    <div x-show="activeTab === 'permissions'" x-transition.opacity style="display: none;" class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100">
        <form action="{{ route('system_users.updatePermissions', $user->id) }}" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 border-b border-gray-50 pb-4">
                <div>
                    <h3 class="font-black text-gray-800 text-lg flex items-center gap-2">
                        <i class="fa-solid fa-shield text-teal-500"></i> الصلاحيات الاستثنائية المباشرة
                    </h3>
                    <p class="text-xs text-gray-500 font-bold mt-1">
                        يمكنك منح هذا الموظف صلاحيات محددة غير موجودة في دوره الوظيفي، أو سحبها منه.
                        <br><span class="text-blue-500">الصلاحيات الملونة بالأزرق موروثة من الدور الوظيفي ولا يمكن إزالتها من هنا.</span>
                    </p>
                </div>
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-sm transition flex items-center gap-2 whitespace-nowrap w-full md:w-auto justify-center">
                    <i class="fa-solid fa-save"></i> حفظ الصلاحيات الإضافية
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @php $userDirectPerms = $user->getDirectPermissions()->pluck('name')->toArray(); @endphp
                @foreach($allPermissions as $groupName => $perms)
                    <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
                        <h5 class="font-black text-sm text-teal-800 mb-4 border-b border-teal-100 pb-2">
                            قسم {{ trans('permissions.module_'.$groupName) === 'permissions.module_'.$groupName ? $groupName : trans('permissions.module_'.$groupName) }}
                        </h5>
                        <div class="space-y-3">
                            @foreach($perms as $perm)
                                @php $hasViaRole = $user->hasPermissionTo($perm->name) && !in_array($perm->name, $userDirectPerms); @endphp
                                <label class="flex items-center gap-3 {{ $hasViaRole ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer group' }}">
                                    <div class="relative flex items-center">
                                        <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" {{ in_array($perm->name, $userDirectPerms) ? 'checked' : '' }} {{ $hasViaRole ? 'disabled' : '' }} class="peer sr-only">
                                        <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer {{ $hasViaRole ? 'bg-blue-300' : 'peer-checked:bg-teal-500' }} peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all transition-colors"></div>
                                    </div>
                                    <span class="text-xs font-bold {{ $hasViaRole ? 'text-blue-800' : 'text-gray-600 group-hover:text-gray-800 transition' }}">
                                        {{ trans('permissions.' . $perm->name) === 'permissions.' . $perm->name ? $perm->name : trans('permissions.' . $perm->name) }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </form>
    </div>

    <!-- Logs Tab -->
    <div x-show="activeTab === 'logs'" x-transition.opacity style="display: none;" class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-black text-gray-800 text-lg">آخر 50 نشاط في النظام</h3>
            <a href="{{ route('audit_logs.index', ['search' => $user->name]) }}" class="text-teal-600 text-xs font-bold hover:underline">عرض كل السجل</a>
        </div>
        
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-right text-sm whitespace-nowrap">
                <thead class="bg-gray-50 text-gray-500 font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">الوقت</th>
                        <th class="px-6 py-4">الحركة</th>
                        <th class="px-6 py-4">القسم المتأثر</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($activities as $log)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4 text-xs font-mono text-gray-500" dir="ltr">
                            {{ $log->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $eventColor = match($log->event) {
                                    'created' => 'text-green-600 bg-green-50 border border-green-100',
                                    'updated' => 'text-blue-600 bg-blue-50 border border-blue-100',
                                    'deleted' => 'text-red-600 bg-red-50 border border-red-100',
                                    'rollback' => 'text-purple-600 bg-purple-50 border border-purple-100',
                                    'login' => 'text-emerald-600 bg-emerald-50 border border-emerald-100',
                                    'logout' => 'text-amber-600 bg-amber-50 border border-amber-100',
                                    default => 'text-gray-600 bg-gray-50 border border-gray-100'
                                };
                                $arEvent = match($log->event) {
                                    'created' => 'إضافة',
                                    'updated' => 'تعديل',
                                    'deleted' => 'حذف',
                                    'rollback' => 'تراجع استعادة',
                                    'login' => 'تسجيل دخول',
                                    'logout' => 'تسجيل خروج',
                                    default => $log->event
                                };
                            @endphp
                            <span class="{{ $eventColor }} px-2 py-1 rounded-md text-[10px] font-black">{{ $arEvent }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-700 font-bold text-xs">
                            @if(in_array($log->event, ['login', 'logout']))
                                <span class="text-gray-400">نظام المصادقة</span>
                            @else
                                {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-400 font-bold text-sm">
                            <i class="fa-solid fa-clock-rotate-left text-3xl mb-3 block text-gray-300"></i>
                            لا يوجد سجل نشاط لهذا الموظف حتى الآن.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
