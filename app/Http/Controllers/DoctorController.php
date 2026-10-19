<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Clinic;
use App\Models\Appointment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        // Get users with role 'طبيب' or 'Doctor'
        $query = User::whereHas('roles', function($q) {
            $q->where('name', 'like', '%طبيب%')->orWhere('name', 'like', '%Doctor%');
        })->with('clinic');

        // Text Search
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('specialization', 'like', '%' . $request->search . '%')
                  ->orWhere('license_number', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        // Status Filter
        if ($request->has('status') && $request->status != 'all') {
            if ($request->status == 'present') {
                $query->where('is_present', true)->where('status', 'نشط');
            } elseif ($request->status == 'absent') {
                $query->where('is_present', false)->where('status', 'نشط');
            } elseif ($request->status == 'suspended') {
                $query->where('status', 'موقوف');
            }
        } else {
            // Default "All" view shows only active doctors (present + absent)
            $query->where('status', 'نشط');
        }

        $doctors = $query->paginate(12)->withQueryString();

        // Calculate counts
        $totalDoctors = User::whereHas('roles', function($q) {
            $q->where('name', 'like', '%طبيب%')->orWhere('name', 'like', '%Doctor%');
        })->where('status', 'نشط')->count();
        $totalNurses = User::whereHas('roles', function($q) {
            $q->where('name', 'like', '%ممرض%')->orWhere('name', 'like', '%Nurse%');
        })->where('status', 'نشط')->count();
        $onLeave = User::whereHas('roles', function($q) {
            $q->where('name', 'like', '%طبيب%')->orWhere('name', 'like', '%Doctor%');
        })->where('is_present', false)->where('status', 'نشط')->count();
        $present = User::whereHas('roles', function($q) {
            $q->where('name', 'like', '%طبيب%')->orWhere('name', 'like', '%Doctor%');
        })->where('is_present', true)->where('status', 'نشط')->count();
        $suspendedCount = User::whereHas('roles', function($q) {
            $q->where('name', 'like', '%طبيب%')->orWhere('name', 'like', '%Doctor%');
        })->where('status', 'موقوف')->count();
        
        $clinics = Clinic::all();

        return view('doctors.index', compact('doctors', 'totalDoctors', 'totalNurses', 'onLeave', 'present', 'suspendedCount', 'clinics'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|string',
            'clinic_id' => 'required|exists:clinics,id',
            'specialization' => 'required|string',
            'license_number' => 'nullable|string',
            'consultation_duration' => 'required|integer',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $username = 'doc_' . uniqid();

        $data = [
            'name' => $request->name,
            'email' => $request->email ?? $username . '@local.system',
            'username' => $username,
            'password' => bcrypt('password123'),
            'status' => 'نشط',
            'phone' => $request->phone,
            'clinic_id' => $request->clinic_id,
            'specialization' => $request->specialization,
            'license_number' => $request->license_number,
            'consultation_duration' => $request->consultation_duration,
            'is_present' => true,
        ];

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars/doctors', 'public');
        }

        $user = User::create($data);
        $user->assignRole('طبيب');

        return back()->with('success', 'تم إضافة الطبيب بنجاح.');
    }

    public function show($id)
    {
        $doctor = User::with(['clinic', 'doctorSchedules', 'doctorLeaves'])->findOrFail($id);
        
        $clinics = Clinic::all();
        $recentAppointments = Appointment::where('doctor_id', $id)
            ->with('patient')
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->take(5)
            ->get();

        return view('doctors.show', compact('doctor', 'clinics', 'recentAppointments'));
    }

    public function update(Request $request, $id)
    {
        $doctor = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $doctor->id,
            'phone' => 'nullable|string',
            'clinic_id' => 'required|exists:clinics,id',
            'specialization' => 'required|string',
            'license_number' => 'nullable|string',
            'consultation_duration' => 'required|integer',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only([
            'name', 'email', 'phone', 'clinic_id', 'specialization', 'license_number', 'consultation_duration'
        ]);

        if ($request->hasFile('avatar')) {
            if ($doctor->avatar && Storage::disk('public')->exists($doctor->avatar)) {
                Storage::disk('public')->delete($doctor->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars/doctors', 'public');
        }

        $doctor->update($data);

        return back()->with('success', 'تم تحديث بيانات الطبيب بنجاح.');
    }

    public function toggleAccountStatus($id)
    {
        $doctor = User::findOrFail($id);
        
        if ($doctor->status === "نشط") {
            $doctor->update(["status" => "موقوف"]);
            return back()->with("success", "تم إيقاف حساب الطبيب بنجاح.");
        } else {
            $doctor->update(["status" => "نشط"]);
            return back()->with("success", "تم إعادة تفعيل حساب الطبيب.");
        }
    }

    public function toggleStatus($id)
    {
        $doctor = User::findOrFail($id);
        
        if ($doctor->is_present) {
            $doctor->update(['is_present' => false]);
            return back()->with('success', 'تم تسجيل الطبيب في إجازة/غائب.');
        } else {
            $doctor->update(['is_present' => true]);
            return back()->with('success', 'تم تسجيل الطبيب كحاضر.');
        }
    }
}
