@extends('layouts.app')

@section('title', 'سجل إشعارات الواتساب - Clinc')
@section('header', 'سجل إشعارات ورسائل الواتساب')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl shadow-sm border border-emerald-100">
                <i class="fa-brands fa-whatsapp"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-gray-800">سجل إشعارات ورسائل الواتساب</h2>
                <p class="text-xs text-gray-500 font-bold mt-1">تتبع كافة رسائل تأكيد المواعيد، التذكيرات، إشعارات نتائج التحاليل وجاهزية الصيدلية.</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('settings.index') }}" class="px-4 py-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs font-bold transition flex items-center gap-2">
                <i class="fa-solid fa-sliders"></i> إعدادات البوابة والقوالب
            </a>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
        <form method="GET" action="{{ route('whatsapp.logs') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">بحث برقم الهاتف أو المحتوى</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث برقم الجوال أو اسم المريض..." class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-medium focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">نوع الإشعار</label>
                <select name="type" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-bold">
                    <option value="all">جميع الأنواع</option>
                    <option value="booking_confirmation" {{ request('type') == 'booking_confirmation' ? 'selected' : '' }}>تأكيد موعد</option>
                    <option value="reminder" {{ request('type') == 'reminder' ? 'selected' : '' }}>تذكير بموعد</option>
                    <option value="lab_ready" {{ request('type') == 'lab_ready' ? 'selected' : '' }}>نتائج مختبر</option>
                    <option value="rx_ready" {{ request('type') == 'rx_ready' ? 'selected' : '' }}>صرف دواء</option>
                    <option value="test" {{ request('type') == 'test' ? 'selected' : '' }}>رسالة اختبارية</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">حالة التسليم</label>
                <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-bold">
                    <option value="all">جميع الحالات</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>تم الإرسال (Sent)</option>
                    <option value="simulated" {{ request('status') == 'simulated' ? 'selected' : '' }}>محاكاة ناجحة (Simulated)</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فشل (Failed)</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white font-bold py-2.5 rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-filter"></i> تصفية
                </button>
                <a href="{{ route('whatsapp.logs') }}" class="px-3 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold transition">
                    إلغاء
                </a>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-gray-50 text-gray-500 font-black border-b border-gray-100">
                    <tr>
                        <th class="p-4">المستلم</th>
                        <th class="p-4">المريض المرتبط</th>
                        <th class="p-4">النوع</th>
                        <th class="p-4">محتوى الرسالة</th>
                        <th class="p-4 text-center">الحالة</th>
                        <th class="p-4 text-center">الوقت والتاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/60 transition">
                            <td class="p-4 font-mono font-bold text-teal-800" dir="ltr">
                                <a href="https://wa.me/{{ $log->recipient_phone }}" target="_blank" class="hover:underline flex items-center gap-1.5 text-emerald-700">
                                    <i class="fa-brands fa-whatsapp text-sm"></i> +{{ $log->recipient_phone }}
                                </a>
                            </td>
                            <td class="p-4 font-bold text-gray-900">
                                @if($log->patient)
                                    <a href="{{ route('patients.show', $log->patient) }}" class="text-teal-700 hover:underline">
                                        {{ $log->patient->first_name }} {{ $log->patient->family_name }}
                                    </a>
                                @else
                                    <span class="text-gray-400">غير محدد</span>
                                @endif
                            </td>
                            <td class="p-4 font-bold">
                                @switch($log->type)
                                    @case('booking_confirmation')
                                        <span class="text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md">تأكيد حجز</span>
                                        @break
                                    @case('reminder')
                                        <span class="text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md">تذكير بالموعد</span>
                                        @break
                                    @case('lab_ready')
                                        <span class="text-purple-700 bg-purple-50 px-2 py-0.5 rounded-md">نتائج مخبرية</span>
                                        @break
                                    @case('rx_ready')
                                        <span class="text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md">جاهزية وصفة</span>
                                        @break
                                    @default
                                        <span class="text-gray-700 bg-gray-100 px-2 py-0.5 rounded-md">{{ $log->type }}</span>
                                @endswitch
                            </td>
                            <td class="p-4 max-w-md">
                                <p class="line-clamp-2 text-gray-600 leading-relaxed font-medium" title="{{ $log->message }}">
                                    {{ $log->message }}
                                </p>
                            </td>
                            <td class="p-4 text-center">
                                {!! $log->status_badge !!}
                            </td>
                            <td class="p-4 text-center font-mono text-gray-400 text-[11px]" dir="ltr">
                                {{ $log->created_at->format('Y-m-d H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-400 font-bold">
                                <i class="fa-regular fa-paper-plane text-4xl mb-3 block text-gray-300"></i>
                                لا توجد رسائل واتساب مسجلة حتى الآن.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
