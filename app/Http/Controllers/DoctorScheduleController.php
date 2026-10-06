<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    public function index()
    {
        $doctors = \App\Models\User::where('role', 'طبيب')->with('clinic')->get();
        $selectedDoctor = null;

        return view('doctor_schedules.index', compact('doctors', 'selectedDoctor'));
    }

    public function show($id)
    {
        $doctors = \App\Models\User::where('role', 'طبيب')->with('clinic')->get();
        $selectedDoctor = \App\Models\User::where('role', 'طبيب')->with('clinic')->findOrFail($id);

        return view('doctor_schedules.index', compact('doctors', 'selectedDoctor'));
    }
}
