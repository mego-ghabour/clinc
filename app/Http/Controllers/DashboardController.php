<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
// use App\Models\Patient; // We will create this later when we do Patients module, for now we will check if it exists
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index()
    {
        // Safely check for Patients
        $patientsCount = 0;
        if (class_exists('App\Models\Patient')) {
            $patientsCount = \App\Models\Patient::count();
        }

        // Safely count active doctors (Roles might be named in English 'Doctor' or Arabic 'طبيب')
        // We will just count users who have any role that contains 'Doctor' or 'طبيب'
        $doctorsCount = User::whereHas('roles', function($q) {
            $q->where('name', 'like', '%Doctor%')
              ->orWhere('name', 'like', '%طبيب%');
        })->where('status', 'نشط')->count();

        // Appointments (Dummy for now until module is built)
        $todayAppointments = 0;
        
        // Revenue (Dummy for now)
        $todayRevenue = 0;

        // Recent Activities
        $recentActivities = Activity::with('causer')->latest()->take(6)->get();

        return view('dashboard', compact(
            'patientsCount', 
            'doctorsCount', 
            'todayAppointments', 
            'todayRevenue',
            'recentActivities'
        ));
    }
}
