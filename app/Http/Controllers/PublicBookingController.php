<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clinic;
use App\Models\User;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PublicBookingController extends Controller
{
    public function index()
    {
        $clinics = Clinic::where('status', 'نشطة')->get();
        // Assuming consultation fee is a specific service, or we just pull a default consultation service
        $consultationService = Service::where('name', 'like', '%كشف%')->orWhere('name', 'like', '%استشارة%')->first();
        $fee = $consultationService ? $consultationService->price : 150; // Fallback to 150

        return view('booking.index', compact('clinics', 'fee'));
    }

    public function getDoctors(Clinic $clinic)
    {
        $doctors = User::role('طبيب')
            ->where('clinic_id', $clinic->id)
            ->where('status', 'نشط')
            ->select('id', 'name', 'specialization', 'avatar')
            ->get();
            
        return response()->json($doctors);
    }

    public function getAvailableSlots(User $doctor, Request $request)
    {
        $request->validate(['date' => 'required|date']);
        $date = Carbon::parse($request->date);
        
        // Carbon: 0 = Sunday, 1 = Monday ... 6 = Saturday
        // We match this exactly with day_of_week in DB
        $dayOfWeek = $date->dayOfWeek; 
        
        $schedule = DoctorSchedule::where('doctor_id', $doctor->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_working', true)
            ->first();

        if (!$schedule || empty($schedule->shifts)) {
            return response()->json([]); // No schedule for this day
        }

        $duration = $doctor->consultation_duration ?? $schedule->consultation_duration ?? 20;
        
        $slots = [];

        // Check booked appointments for this doctor on this date
        $bookedSlots = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $date->format('Y-m-d'))
            ->whereNotIn('status', ['ملغي'])
            ->pluck('appointment_time')
            ->map(function ($time) {
                return Carbon::parse($time)->format('H:i:s');
            })
            ->toArray();

        foreach ($schedule->shifts as $shift) {
            $startTime = Carbon::parse($date->format('Y-m-d') . ' ' . $shift['start']);
            $endTime = Carbon::parse($date->format('Y-m-d') . ' ' . $shift['end']);

            while ($startTime->copy()->addMinutes($duration)->lte($endTime)) {
                $timeString = $startTime->format('H:i:s');
                
                if (!in_array($timeString, $bookedSlots)) {
                    $slots[] = $startTime->format('H:i');
                }
                $startTime->addMinutes($duration);
            }
        }

        return response()->json($slots);
    }

    public function store(Request $request)
    {
        $request->validate([
            // Patient data
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|string',
            'id_type' => 'required|string',
            'id_number' => 'required|string',
            'gender' => 'required|in:ذكر,أنثى',
            
            // Appointment data
            'doctor_id' => 'required|exists:users,id',
            'clinic_id' => 'required|exists:clinics,id',
            'date' => 'required|date',
            'time' => 'required',
        ]);

        DB::beginTransaction();
        try {
            // 1. Find or create patient by ID Number or Phone
            $patient = Patient::where('id_number', $request->id_number)
                              ->orWhere('phone', $request->phone)
                              ->first();

            if (!$patient) {
                // Generate a random MRN
                $mrn = 'MRN-' . strtoupper(substr(uniqid(), -6));
                
                $patient = Patient::create([
                    'name' => $request->first_name . ' ' . $request->last_name,
                    'mrn' => $mrn,
                    'phone' => $request->phone,
                    'id_number' => $request->id_number,
                    'gender' => $request->gender,
                    // id_type is not in the db by default, we can skip or append to notes
                    'notes' => 'نوع الهوية: ' . $request->id_type,
                    'date_of_birth' => '1990-01-01' // Default/dummy since form doesn't ask
                ]);
            } else {
                // Update name just in case
                $patient->update([
                    'name' => $request->first_name . ' ' . $request->last_name,
                ]);
            }

            // 2. Create the Appointment (Status Scheduled)
            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $request->doctor_id,
                'clinic_id' => $request->clinic_id,
                'appointment_date' => $request->date,
                'appointment_time' => $request->time,
                'status' => 'مجدول', // Scheduled, not in reception yet
                'type' => 'كشف',
                'notes' => 'حجز أونلاين عبر البوابة'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم الحجز بنجاح',
                'appointment_id' => $appointment->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الحجز: ' . $e->getMessage()
            ], 500);
        }
    }
}
