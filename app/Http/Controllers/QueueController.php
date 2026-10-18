<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Visit;
use App\Models\User;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function index(Request $request)
    {
        // Get today's appointments that are active (Not Cancelled)
        $todayAppointments = Appointment::with(['patient', 'doctor.clinic'])
            ->whereDate('appointment_date', date('Y-m-d'))
            ->whereIn('status', ['مجدول', 'مؤكد', 'تم الحضور'])
            ->orderBy('appointment_time', 'asc')
            ->get();

        // Separate them into two lists: Expected (لم يحضر بعد) and Waiting (في الطابور)
        $expected = $todayAppointments->whereIn('status', ['مجدول', 'مؤكد']);
        $waiting = $todayAppointments->where('status', 'تم الحضور');

        // Also fetch active doctors for the filter or display
        $doctors = User::whereHas('roles', function($q) {
            $q->where('name', 'like', '%طبيب%')->orWhere('name', 'like', '%Doctor%');
        })->where('status', 'نشط')->get();

        return view('queues.index', compact('expected', 'waiting', 'doctors'));
    }

    public function checkIn(Request $request, Appointment $appointment)
    {
        // Mark the appointment as Arrived
        $appointment->update(['status' => 'تم الحضور']);
        
        // Automatically create an empty Visit (EMR) draft for this patient and doctor
        // so the nurse/doctor can start writing in it.
        $visitExists = Visit::where('appointment_id', $appointment->id)->exists();
        
        if (!$visitExists) {
            Visit::create([
                'patient_id' => $appointment->patient_id,
                'doctor_id' => $appointment->doctor_id,
                'appointment_id' => $appointment->id,
                'visit_date' => date('Y-m-d H:i:s'),
                'type' => 'كشف جديد',
                'status' => 'قيد الانتظار' // Waiting for doctor/nurse
            ]);
        }

        return back()->with('success', 'تم تسجيل حضور المريض وإضافته لطابور الانتظار بنجاح.');
    }
}
