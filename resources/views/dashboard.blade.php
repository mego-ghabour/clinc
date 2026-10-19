@extends('layouts.app')

@section('title', 'لوحة التحكم والمؤشرات الحية - Clinc')
@section('header', 'لوحة مؤشرات الأداء الحية والتقارير المالية')

@section('content')
<div class="space-y-6">

    <!-- Welcome & Live Status Banner -->
    <div class="bg-gradient-to-l from-teal-800 via-teal-700 to-teal-600 rounded-3xl p-6 md:p-8 text-white flex flex-col md:flex-row justify-between items-center shadow-lg relative overflow-hidden gap-6">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-teal-400 rounded-full mix-blend-multiply filter blur-3xl opacity-40"></div>
        
        <div class="relative z-10 text-center md:text-right">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-teal-100 text-xs font-bold mb-3 border border-white/20">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                نظام العيادة متصل ومحدث لحظياً (Live EMR)
            </div>
            <h1 class="text-2xl md:text-3xl font-black mb-2">مرحباً بك مجدداً، {{ auth()->user()->name }}! 👋</h1>
            <p class="text-teal-100 text-xs md:text-sm font-bold max-w-xl leading-relaxed">
                ملخص حي ومؤشرات الأداء للعيادات، حركة الاستقبال، التدفقات المالية والمختبرات الطبية.
            </p>
            <div class="mt-3.5">
                <a href="{{ route('guide.index') }}" class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/15 hover:bg-white/25 text-white rounded-xl text-xs font-bold transition border border-white/25 backdrop-blur-sm shadow-sm group">
                    <i class="fa-solid fa-book-open-reader text-amber-300"></i>
                    <span>تصفح دليل وشرح النظام الشامل لجميع الأقسام</span>
                    <i class="fa-solid fa-arrow-left text-[10px] mr-1 group-hover:-translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>

        <div class="relative z-10 flex flex-row md:flex-col gap-3 w-full md:w-auto">
            <div class="bg-white/10 px-5 py-3 rounded-2xl backdrop-blur-sm border border-white/20 text-center flex-1 md:flex-none">
                <p class="text-[11px] text-teal-200 font-bold mb-0.5">تاريخ اليوم</p>
                <p class="text-lg md:text-xl font-black font-mono" dir="ltr">{{ now()->format('Y-m-d') }}</p>
            </div>
            <div class="bg-white/10 px-5 py-3 rounded-2xl backdrop-blur-sm border border-white/20 text-center flex-1 md:flex-none">
                <p class="text-[11px] text-teal-200 font-bold mb-0.5">إيراد الشهر الحالي</p>
                <p class="text-lg md:text-xl font-black font-mono text-emerald-300" dir="ltr">{{ number_format($monthRevenue, 2) }} <span class="text-xs">ر.س</span></p>
            </div>
        </div>
    </div>

    <!-- Live KPI Summary Cards (4 Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">
        
        <!-- KPI 1: Patients & Doctors -->
        <a href="{{ route('patients.index') }}" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between group hover:border-blue-300 hover:shadow-md transition">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400">إجمالي المرضى</p>
                    <h4 class="text-2xl font-black text-gray-800">{{ number_format($patientsCount) }}</h4>
                    <span class="text-[10px] text-teal-600 font-bold"><i class="fa-solid fa-user-doctor ml-0.5"></i> {{ $doctorsCount }} طبيب نشط</span>
                </div>
            </div>
            <i class="fa-solid fa-chevron-left text-gray-300 group-hover:text-blue-500 transition text-sm"></i>
        </a>

        <!-- KPI 2: Today Appointments & Queue -->
        <a href="{{ route('queues.index') }}" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between group hover:border-amber-300 hover:shadow-md transition">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400">مواعيد اليوم</p>
                    <h4 class="text-2xl font-black text-gray-800">{{ $todayAppointments }}</h4>
                    <span class="text-[10px] text-amber-600 font-bold"><i class="fa-solid fa-clock ml-0.5"></i> {{ $waitingCount }} في صالة الانتظار</span>
                </div>
            </div>
            <i class="fa-solid fa-chevron-left text-gray-300 group-hover:text-amber-500 transition text-sm"></i>
        </a>

        <!-- KPI 3: Today Revenue & Unpaid -->
        <a href="{{ route('invoices.index') }}" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between group hover:border-emerald-300 hover:shadow-md transition">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400">المحصل اليوم</p>
                    <h4 class="text-2xl font-black text-gray-800 font-mono">{{ number_format($todayRevenue, 0) }} <span class="text-xs font-sans text-gray-500">ر.س</span></h4>
                    <span class="text-[10px] text-rose-500 font-bold"><i class="fa-solid fa-receipt ml-0.5"></i> {{ number_format($unpaidInvoicesSum, 0) }} ر.س مستحق</span>
                </div>
            </div>
            <i class="fa-solid fa-chevron-left text-gray-300 group-hover:text-emerald-500 transition text-sm"></i>
        </a>

        <!-- KPI 4: Lab & Radiology Requests -->
        <a href="{{ route('lab-requests.index') }}" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between group hover:border-teal-300 hover:shadow-md transition">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-flask-vial"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400">التحاليل والأشعة النشطة</p>
                    <h4 class="text-2xl font-black text-gray-800">{{ $pendingLabs + $pendingRadiology }}</h4>
                    <span class="text-[10px] text-teal-600 font-bold">{{ $pendingLabs }} مختبر | {{ $pendingRadiology }} أشعة</span>
                </div>
            </div>
            <i class="fa-solid fa-chevron-left text-gray-300 group-hover:text-teal-500 transition text-sm"></i>
        </a>

    </div>

    <!-- Quick Operational Status Pills -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="{{ route('prescriptions.index', ['status' => 'معلقة']) }}" class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-2xs hover:border-emerald-300 transition flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-pills"></i>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 font-bold block">وصفات بانتظار الصرف</span>
                    <strong class="text-sm font-black text-gray-800">{{ $pendingPrescriptions }}</strong>
                </div>
            </div>
            <i class="fa-solid fa-chevron-left text-gray-300 text-xs"></i>
        </a>

        <a href="{{ route('medicines.index', ['stock_status' => 'low_stock']) }}" class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-2xs hover:border-amber-300 transition flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 font-bold block">أدوية قريبة من النفاد</span>
                    <strong class="text-sm font-black {{ $lowStockMedicines > 0 ? 'text-amber-600' : 'text-gray-800' }}">{{ $lowStockMedicines }}</strong>
                </div>
            </div>
            <i class="fa-solid fa-chevron-left text-gray-300 text-xs"></i>
        </a>

        <a href="{{ route('insurance-claims.index', ['status' => 'معلقة']) }}" class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-2xs hover:border-blue-300 transition flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 font-bold block">مطالبات تأمين معلقة</span>
                    <strong class="text-sm font-black text-gray-800">{{ $pendingClaims }}</strong>
                </div>
            </div>
            <i class="fa-solid fa-chevron-left text-gray-300 text-xs"></i>
        </a>

        <a href="{{ route('lab-requests.index', ['status' => 'معلق']) }}" class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-2xs hover:border-teal-300 transition flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-vial"></i>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 font-bold block">تحاليل بانتظار العينة</span>
                    <strong class="text-sm font-black text-gray-800">{{ $pendingLabs }}</strong>
                </div>
            </div>
            <i class="fa-solid fa-chevron-left text-gray-300 text-xs"></i>
        </a>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Chart 1: 7-Day Revenue Trend (2 Columns) -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6 flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4">
                <div>
                    <h3 class="font-black text-gray-800 text-base flex items-center gap-2">
                        <i class="fa-solid fa-chart-line text-teal-600"></i>
                        مسار الإيرادات والمواعيد اليومية (آخر 7 أيام)
                    </h3>
                    <p class="text-xs text-gray-400 font-bold mt-0.5">مقارنة التدفق المالي المحصل الفعلي وإجمالي المواعيد المسجلة</p>
                </div>
                <div class="flex items-center gap-3 text-xs font-bold">
                    <span class="inline-flex items-center gap-1.5 text-teal-700">
                        <span class="w-3 h-3 rounded-full bg-teal-600 inline-block"></span>
                        الإيراد (ر.س)
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-amber-600">
                        <span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span>
                        المواعيد
                    </span>
                </div>
            </div>

            <div class="relative h-64 md:h-72 w-full">
                <canvas id="revenueTrendChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Payment Methods Distribution (1 Column) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6 flex flex-col justify-between">
            <div>
                <h3 class="font-black text-gray-800 text-base flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-teal-600"></i>
                    توزيع طرق الدفع والتحصيل
                </h3>
                <p class="text-xs text-gray-400 font-bold mt-0.5">نسب الدفع بالبطاقة، النقدي والتحويل</p>
            </div>

            <div class="relative h-56 w-full my-auto flex items-center justify-center">
                <canvas id="paymentMethodChart"></canvas>
            </div>

            <div class="pt-3 border-t border-gray-100 text-center">
                <p class="text-[11px] font-bold text-gray-400">إجمالي مدفوعات مسجلة بالنظام</p>
                <p class="text-sm font-black text-gray-800 font-mono">{{ number_format(array_sum($paymentTotals), 2) }} ر.س</p>
            </div>
        </div>

    </div>

    <!-- Live Feeds: Appointments Today & Recent Financial Transactions & Audit Log -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Column 1: Today's Appointments & Queue -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-100">
                <h3 class="font-black text-gray-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-user-clock text-amber-500"></i>
                    مواعيد صالة الانتظار اليوم
                </h3>
                <a href="{{ route('queues.index') }}" class="text-xs font-bold text-teal-600 hover:underline">شاشة الطابور</a>
            </div>

            <div class="space-y-3">
                @forelse($recentAppointments as $apt)
                <div class="p-3 rounded-xl bg-gray-50/70 border border-gray-100 flex items-center justify-between hover:bg-gray-100/70 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-teal-100/60 text-teal-700 font-bold flex items-center justify-center text-xs shrink-0">
                            {{ mb_substr($apt->patient->name ?? 'م', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-800">{{ $apt->patient->name ?? '—' }}</p>
                            <p class="text-[10px] text-gray-400">د. {{ $apt->doctor->name ?? 'طبيب عام' }}</p>
                        </div>
                    </div>
                    <div class="text-left">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $apt->status == 'تم الحضور' ? 'bg-amber-100 text-amber-700 animate-pulse' : ($apt->status == 'مكتمل' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700') }}">
                            {{ $apt->status }}
                        </span>
                        <p class="text-[10px] text-gray-400 font-mono mt-0.5" dir="ltr">{{ $apt->appointment_time }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-400 text-xs font-bold">
                    <i class="fa-regular fa-calendar-xmark text-2xl mb-1 text-gray-300 block"></i>
                    لا توجد مواعيد متبقية لليوم
                </div>
                @endforelse
            </div>
        </div>

        <!-- Column 2: Recent Payments -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-100">
                <h3 class="font-black text-gray-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-emerald-600"></i>
                    أحدث المقبوضات المالية
                </h3>
                <a href="{{ route('invoices.index') }}" class="text-xs font-bold text-teal-600 hover:underline">كافة الفواتير</a>
            </div>

            <div class="space-y-3">
                @forelse($recentPayments as $payment)
                <div class="p-3 rounded-xl bg-gray-50/70 border border-gray-100 flex items-center justify-between hover:bg-gray-100/70 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 font-bold flex items-center justify-center text-xs shrink-0">
                            <i class="fa-solid fa-money-bill-wave"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-800">{{ $payment->invoice->patient->name ?? 'مريض عام' }}</p>
                            <p class="text-[10px] text-gray-400 font-mono">{{ $payment->payment_method ?? 'نقداً' }}</p>
                        </div>
                    </div>
                    <div class="text-left">
                        <p class="text-xs font-black text-emerald-600 font-mono" dir="ltr">+{{ number_format($payment->amount, 2) }} ر.س</p>
                        <p class="text-[10px] text-gray-400 font-mono">{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('m-d') : '—' }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-400 text-xs font-bold">
                    <i class="fa-solid fa-receipt text-2xl mb-1 text-gray-300 block"></i>
                    لا توجد مدفوعات مسجلة حديثاً
                </div>
                @endforelse
            </div>
        </div>

        <!-- Column 3: Live Audit Log -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-100">
                <h3 class="font-black text-gray-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-teal-600"></i>
                    سجل النشاطات والأمان
                </h3>
                <a href="{{ route('audit_logs.index') }}" class="text-xs font-bold text-teal-600 hover:underline">السجل الكامل</a>
            </div>
            
            <div class="space-y-3 max-h-[320px] overflow-y-auto custom-scrollbar pr-1">
                @forelse($recentActivities as $log)
                <div class="flex items-start gap-2.5 p-2 rounded-xl hover:bg-gray-50 transition">
                    <div class="w-7 h-7 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center shrink-0 text-xs font-bold overflow-hidden border border-gray-200">
                        @if($log->causer && $log->causer->avatar)
                            <img src="{{ asset('storage/' . $log->causer->avatar) }}" class="w-full h-full object-cover">
                        @else
                            {{ mb_substr($log->causer->name ?? 'م', 0, 1) }}
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-700 leading-snug">
                            <span class="font-bold text-gray-900">{{ $log->causer->name ?? 'النظام' }}</span>
                            @if(in_array($log->event, ['login', 'logout']))
                                <span class="font-bold text-teal-600">{{ $log->event === 'login' ? 'سجل دخول' : 'سجل خروج' }}</span>
                            @else
                                <span class="text-gray-500">قام بـ</span>
                                <span class="font-bold text-teal-700">
                                    {{ match($log->event) { 'created' => 'إضافة', 'updated' => 'تعديل', 'deleted' => 'حذف', 'rollback' => 'استرجاع', default => $log->event } }}
                                </span>
                                في 
                                <span class="font-bold text-gray-700">
                                    {{ match(class_basename($log->subject_type)) {
                                        'Setting' => 'الإعدادات',
                                        'User' => 'المستخدمين',
                                        'Patient' => 'المرضى',
                                        'Appointment' => 'المواعيد',
                                        'Visit' => 'الزيارات',
                                        'LabRequest' => 'المختبر',
                                        'Invoice' => 'الفواتير',
                                        default => class_basename($log->subject_type ?? 'سجل')
                                    } }}
                                </span>
                            @endif
                        </p>
                        <span class="text-[10px] text-gray-400 font-mono">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-400 text-xs font-bold">
                    لا توجد نشاطات مسجلة
                </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Revenue & Appointments Trend Line/Bar Chart
    const trendCtx = document.getElementById('revenueTrendChart')?.getContext('2d');
    if (trendCtx) {
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($trendLabels) !!},
                datasets: [
                    {
                        label: 'الإيرادات المحصلة (ر.س)',
                        data: {!! json_encode($revenueTrend) !!},
                        borderColor: '#0d9488',
                        backgroundColor: 'rgba(13, 148, 136, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y',
                        pointBackgroundColor: '#0d9488',
                        pointRadius: 4,
                    },
                    {
                        label: 'عدد المواعيد',
                        data: {!! json_encode($appointmentsTrend) !!},
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0.4,
                        yAxisID: 'y1',
                        pointBackgroundColor: '#f59e0b',
                        pointRadius: 3,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        rtl: true,
                        callbacks: {
                            label: function(context) {
                                if (context.dataset.yAxisID === 'y') {
                                    return context.dataset.label + ': ' + Number(context.parsed.y).toLocaleString() + ' ر.س';
                                }
                                return context.dataset.label + ': ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Tajawal, Cairo', size: 11 } }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        grid: { color: 'rgba(241, 245, 249, 1)' },
                        ticks: {
                            font: { family: 'Tajawal, Cairo', size: 10 },
                            callback: function(value) { return value + ' ر.س'; }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: {
                            precision: 0,
                            font: { family: 'Tajawal, Cairo', size: 10 }
                        }
                    }
                }
            }
        });
    }

    // 2. Payment Methods Doughnut Chart
    const payCtx = document.getElementById('paymentMethodChart')?.getContext('2d');
    if (payCtx) {
        new Chart(payCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($paymentLabels) !!},
                datasets: [{
                    data: {!! json_encode($paymentTotals) !!},
                    backgroundColor: [
                        '#0d9488', // Teal
                        '#3b82f6', // Blue
                        '#8b5cf6', // Purple
                        '#f59e0b', // Amber
                        '#10b981', // Emerald
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        rtl: true,
                        labels: {
                            font: { family: 'Tajawal, Cairo', size: 11 },
                            boxWidth: 12,
                            padding: 12
                        }
                    },
                    tooltip: {
                        rtl: true,
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.label + ': ' + Number(context.raw).toLocaleString() + ' ر.س';
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });
    }
});
</script>
@endpush
