<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\User::where('role', 'طبيب')->with('clinic');

        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('specialization', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'present') {
                $query->where('is_present', true);
            } elseif ($request->status == 'absent') {
                $query->where('is_present', false);
            }
        }

        $doctors = $query->get();

        // Calculate counts for header
        $totalDoctors = \App\Models\User::where('role', 'طبيب')->count();
        $totalNurses = \App\Models\User::where('role', 'تمريض')->count();
        $onLeave = \App\Models\User::where('role', 'طبيب')->where('is_present', false)->count();
        $present = \App\Models\User::where('role', 'طبيب')->where('is_present', true)->count();
        
        $clinics = \App\Models\Clinic::all();

        return view('doctors.index', compact('doctors', 'totalDoctors', 'totalNurses', 'onLeave', 'present', 'clinics'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'clinic_id' => 'required|exists:clinics,id',
            'specialization' => 'required|string',
            'license_number' => 'nullable|string',
            'consultation_duration' => 'required|integer',
        ]);

        $clinic = \App\Models\Clinic::find($request->clinic_id);

        $username = 'doc_' . uniqid();

        \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email ?? $username . '@local.system',
            'username' => $username,
            'password' => bcrypt('password123'),
            'role' => 'طبيب',
            'status' => 'نشط',
            'phone' => $request->phone,
            'clinic_id' => $request->clinic_id,
            'specialization' => $request->specialization,
            'license_number' => $request->license_number,
            'consultation_duration' => $request->consultation_duration,
            'is_present' => true,
        ]);

        return back()->with('success', 'تم إضافة الطبيب بنجاح');
    }

    public function show($id)
    {
        $doctor = \App\Models\User::with('clinic')->findOrFail($id);
        
        // Ensure user is a doctor
        if ($doctor->role !== 'طبيب') {
            abort(404);
        }

        $clinics = \App\Models\Clinic::all();

        return view('doctors.show', compact('doctor', 'clinics'));
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $doctor = \App\Models\User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'clinic_id' => 'required|exists:clinics,id',
            'specialization' => 'required|string',
            'license_number' => 'nullable|string',
            'consultation_duration' => 'required|integer',
        ]);

        $doctor->update($request->only([
            'name', 'email', 'phone', 'clinic_id', 'specialization', 'license_number', 'consultation_duration'
        ]));

        return back()->with('success', 'تم تحديث بيانات الطبيب بنجاح');
    }

    public function toggleStatus($id)
    {
        $doctor = \App\Models\User::findOrFail($id);
        
        if ($doctor->status === 'نشط') {
            $doctor->update(['status' => 'موقوف']);
            return back()->with('success', 'تم إيقاف حساب الطبيب بنجاح');
        } else {
            $doctor->update(['status' => 'نشط']);
            return back()->with('success', 'تم تفعيل حساب الطبيب بنجاح');
        }
    }

    public function updateAvatar(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $doctor = \App\Models\User::findOrFail($id);

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($doctor->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($doctor->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($doctor->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $doctor->update(['avatar' => $path]);
        }

        return back()->with('success', 'تم تحديث الصورة بنجاح');
    }
}
