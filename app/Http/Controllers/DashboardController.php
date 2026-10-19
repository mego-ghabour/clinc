<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\LabRequest;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\RadiologyRequest;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Operational Counts
        $patientsCount = Patient::count();
        $doctorsCount = User::whereHas('roles', function ($q) {
            $q->where('name', 'like', '%Doctor%')
              ->orWhere('name', 'like', '%طبيب%');
        })->where('status', 'نشط')->count();

        // Today's Appointments & Queue
        $todayAppointments = Appointment::whereDate('appointment_date', $today)->count();
        $waitingCount = Appointment::whereDate('appointment_date', $today)
            ->where('status', 'تم الحضور')
            ->count();
        $completedAppointmentsToday = Appointment::whereDate('appointment_date', $today)
            ->where('status', 'مكتمل')
            ->count();
        $todayVisits = Visit::whereDate('visit_date', $today)->count();

        // Financial KPIs
        $todayRevenue = (float) Payment::whereDate('payment_date', $today)->sum('amount');
        $monthRevenue = (float) Payment::whereYear('payment_date', $today->year)
            ->whereMonth('payment_date', $today->month)
            ->sum('amount');
        
        $unpaidInvoicesSum = (float) Invoice::whereIn('status', ['غير مدفوعة', 'مدفوعة جزئياً'])
            ->get()
            ->sum(function ($inv) {
                return max(0, (float)$inv->total - (float)$inv->paid_amount);
            });

        // Diagnostic, Lab, Pharmacy & Insurance KPIs
        $pendingLabs = LabRequest::whereIn('status', ['معلق', 'تم سحب العينة', 'قيد التحليل'])->count();
        $pendingRadiology = RadiologyRequest::where('status', 'pending')->count();
        $pendingPrescriptions = \App\Models\Prescription::where('status', 'معلقة')->count();
        $pendingClaims = \App\Models\InsuranceClaim::whereIn('status', ['معلقة', 'موافقة مبدئية'])->count();
        $lowStockMedicines = \App\Models\Medicine::lowStock()->count();

        // 7-Day Revenue & Appointments Trend for Chart.js
        $trendLabels = [];
        $revenueTrend = [];
        $appointmentsTrend = [];

        $arabicDays = [
            'Sunday' => 'الأحد',
            'Monday' => 'الإثنين',
            'Tuesday' => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday' => 'الخميس',
            'Friday' => 'الجمعة',
            'Saturday' => 'السبت',
        ];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dayName = $arabicDays[$date->format('l')] ?? $date->format('D');
            $trendLabels[] = $dayName . ' ' . $date->format('d/m');

            $revenueTrend[] = (float) Payment::whereDate('payment_date', $date)->sum('amount');
            $appointmentsTrend[] = Appointment::whereDate('appointment_date', $date)->count();
        }

        // Payment Methods Breakdown (Doughnut Chart)
        $paymentMethodsData = Payment::selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();

        $paymentLabels = array_keys($paymentMethodsData);
        $paymentTotals = array_values($paymentMethodsData);

        // If no payment data yet, supply meaningful defaults for Chart representation
        if (empty($paymentLabels)) {
            $paymentLabels = ['نقداً (Cash)', 'بطاقة / شبكة', 'تحويل بنكي'];
            $paymentTotals = [0, 0, 0];
        }

        // Appointments Today Status Breakdown
        $statusCounts = Appointment::whereDate('appointment_date', $today)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Recent Appointments Today
        $recentAppointments = Appointment::with(['patient', 'doctor'])
            ->whereDate('appointment_date', $today)
            ->orderByRaw("CASE WHEN status = 'تم الحضور' THEN 1 WHEN status = 'مجدول' THEN 2 ELSE 3 END")
            ->orderBy('appointment_time', 'asc')
            ->take(5)
            ->get();

        // Recent Payments
        $recentPayments = Payment::with('invoice.patient')
            ->latest('id')
            ->take(5)
            ->get();

        // Recent Activities
        $recentActivities = Activity::with('causer')->latest()->take(6)->get();

        return view('dashboard', compact(
            'patientsCount',
            'doctorsCount',
            'todayAppointments',
            'waitingCount',
            'completedAppointmentsToday',
            'todayVisits',
            'todayRevenue',
            'monthRevenue',
            'unpaidInvoicesSum',
            'pendingLabs',
            'pendingRadiology',
            'pendingPrescriptions',
            'pendingClaims',
            'lowStockMedicines',
            'trendLabels',
            'revenueTrend',
            'appointmentsTrend',
            'paymentLabels',
            'paymentTotals',
            'statusCounts',
            'recentAppointments',
            'recentPayments',
            'recentActivities'
        ));
    }
}
