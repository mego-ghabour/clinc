<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor']);

        // Default to today if no specific date or search is provided
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('appointment_date', $request->date);
        } elseif (!$request->has('search') && !$request->has('status') && !$request->has('all_dates')) {
            $query->whereDate('appointment_date', date('Y-m-d'));
        }

        if ($request->has('doctor_id') && $request->doctor_id != '') {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->has('status') && $request->status != '' && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('appointment_number', 'like', "%{$search}%")
                  ->orWhereHas('patient', function($q2) use ($search) {
                      $q2->where('first_name', 'like', "%{$search}%")
                         ->orWhere('family_name', 'like', "%{$search}%")
                         ->orWhere('mrn', 'like', "%{$search}%")
                         ->orWhere('mobile', 'like', "%{$search}%");
                  });
            });
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
                              ->orderBy('appointment_time', 'asc')
                              ->paginate(15)
                              ->withQueryString();
                              
        // For the Create Modal
        $patients = Patient::select('id', 'first_name', 'family_name', 'mrn')->get();
        
        // Fetch only active doctors using Spatie roles (like in DoctorController)
        $doctors = User::whereHas('roles', function($q) {
            $q->where('name', 'like', '%طبيب%')->orWhere('name', 'like', '%Doctor%');
        })->where('status', 'نشط')->select('id', 'name')->get();

        return view('appointments.index', compact('appointments', 'patients', 'doctors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'service' => 'required|string',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
        ]);

        $data = $request->all();
        $data['status'] = 'مجدول'; // Default status for new appointments

        Appointment::create($data);

        return back()->with('success', 'تم حجز الموعد بنجاح.');
    }

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $appointment->update($request->only('status'));

        return back()->with('success', 'تم تحديث حالة الموعد بنجاح.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return back()->with('success', 'تم حذف الموعد بنجاح.');
    }
}
