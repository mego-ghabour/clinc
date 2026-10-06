<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\Appointment::with(['patient', 'doctor']);

        if ($request->has('date') && $request->date != '') {
            $query->whereDate('appointment_date', $request->date);
        } else {
            // default to today if not searching
            if (!$request->has('search')) {
                $query->whereDate('appointment_date', date('Y-m-d'));
            }
        }

        if ($request->has('doctor_id') && $request->doctor_id != '') {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('appointment_number', 'like', "%{$search}%")
                  ->orWhereHas('patient', function($q2) use ($search) {
                      $q2->where('first_name', 'like', "%{$search}%")
                         ->orWhere('family_name', 'like', "%{$search}%")
                         ->orWhere('mrn', 'like', "%{$search}%");
                  });
            });
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
                              ->orderBy('appointment_time', 'asc')
                              ->paginate(15);
                              
        $patients = \App\Models\Patient::select('id', 'first_name', 'family_name', 'mrn')->get();
        $doctors = \App\Models\User::where('role', 'طبيب')->select('id', 'name')->get();

        return view('appointments.index', compact('appointments', 'patients', 'doctors'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'service' => 'required|string',
            'appointment_date' => 'required|date',
        ]);

        // Default time or selected time (for now, just take from request if exists, else null)
        \App\Models\Appointment::create($request->all());

        return back()->with('success', 'تم حجز الموعد بنجاح');
    }
}
