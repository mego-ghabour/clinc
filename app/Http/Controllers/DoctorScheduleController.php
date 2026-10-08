<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    public function index()
    {
        $doctors = User::whereHas('roles', function($q) {
            $q->where('name', 'like', '%طبيب%')->orWhere('name', 'like', '%Doctor%');
        })->where('status', 'نشط')->get();
        
        $selectedDoctor = null;

        return view('doctor_schedules.index', compact('doctors', 'selectedDoctor'));
    }

    public function show($id)
    {
        $doctors = User::whereHas('roles', function($q) {
            $q->where('name', 'like', '%طبيب%')->orWhere('name', 'like', '%Doctor%');
        })->where('status', 'نشط')->get();
        
        $selectedDoctor = User::whereHas('roles', function($q) {
            $q->where('name', 'like', '%طبيب%')->orWhere('name', 'like', '%Doctor%');
        })->findOrFail($id);

        $schedules = DoctorSchedule::where('doctor_id', $id)->get()->keyBy('day_of_week');
        $leaves = \App\Models\DoctorLeave::where('doctor_id', $id)->orderBy('from_date', 'asc')->get();

        return view('doctor_schedules.index', compact('doctors', 'selectedDoctor', 'schedules', 'leaves'));
    }

    public function store(Request $request)
    {
        $doctorId = $request->doctor_id;
        $schedules = $request->schedules; // Array of day data

        foreach ($schedules as $day => $data) {
            
            // Clean up empty shifts if they were submitted
            $validShifts = [];
            if (isset($data['shifts']) && is_array($data['shifts'])) {
                foreach ($data['shifts'] as $shift) {
                    if (!empty($shift['start']) && !empty($shift['end'])) {
                        $validShifts[] = $shift;
                    }
                }
            }
            
            $isWorking = isset($data['is_working']) && count($validShifts) > 0;

            DoctorSchedule::updateOrCreate(
                ['doctor_id' => $doctorId, 'day_of_week' => $day],
                [
                    'is_working' => $isWorking,
                    'shifts' => $isWorking ? $validShifts : null,
                    'consultation_duration' => $data['consultation_duration'] ?? 15,
                ]
            );
        }

        return back()->with('success', 'تم تحديث أوقات العمل والفترات بنجاح.');
    }
}
