<?php

namespace App\Http\Controllers;

use App\Models\DoctorLeave;
use Illuminate\Http\Request;

class DoctorLeaveController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'nullable|string'
        ]);

        DoctorLeave::create($request->all());

        return back()->with('success', 'تم تسجيل إجازة الطبيب بنجاح.');
    }

    public function destroy(DoctorLeave $doctor_leaf)
    {
        // the param is $doctor_leaf because of laravel singularization of leaves -> leaf
        $doctor_leaf->delete();
        return back()->with('success', 'تم حذف الإجازة بنجاح.');
    }
}
