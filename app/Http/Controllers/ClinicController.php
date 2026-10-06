<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClinicController extends Controller
{
    public function index()
    {
        $clinics = \App\Models\Clinic::withTrashed()->with(['doctors', 'headDoctor'])->orderBy('sort_order')->orderBy('id', 'desc')->get();
        // Load doctors to assign as head
        $doctors = \App\Models\User::where('role', 'doctor')->orWhere('clinic_id', '!=', null)->get(); 
        // Note: adjust the query above based on how you define doctors in users table
        return view('clinics.index', compact('clinics', 'doctors'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'icon_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'color' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:255',
            'status' => 'required|in:نشطة,متوقفة',
            'notes' => 'nullable|string',
            'daily_quota' => 'nullable|integer|min:1',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'head_doctor_id' => 'nullable|exists:users,id',
            'sort_order' => 'nullable|integer'
        ]);

        if ($request->hasFile('icon_file')) {
            $path = $request->file('icon_file')->store('clinics/icons', 'public');
            $data['icon'] = $path;
        }

        if (empty($data['code'])) {
            // Auto generate code like DEN-01
            $prefix = strtoupper(substr(md5(uniqid()), 0, 3));
            $lastClinic = \App\Models\Clinic::withTrashed()->latest('id')->first();
            $nextId = $lastClinic ? $lastClinic->id + 1 : 1;
            $data['code'] = $prefix . '-' . str_pad($nextId, 2, '0', STR_PAD_LEFT);
        }

        if (empty($data['color'])) {
            $data['color'] = '#14b8a6';
        }

        \App\Models\Clinic::create($data);

        return back()->with('success', 'تم إضافة العيادة بنجاح');
    }

    public function show($id)
    {
        $clinic = \App\Models\Clinic::withTrashed()->findOrFail($id);
        $clinic->load('doctors');
        $allDoctors = \App\Models\User::where('role', 'doctor')->orWhere('clinic_id', '!=', null)->get(); 
        return view('clinics.show', compact('clinic', 'allDoctors'));
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $clinic = \App\Models\Clinic::withTrashed()->findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'icon_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'color' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:255',
            'status' => 'required|in:نشطة,متوقفة',
            'notes' => 'nullable|string',
            'daily_quota' => 'nullable|integer|min:1',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'head_doctor_id' => 'nullable|exists:users,id',
            'sort_order' => 'nullable|integer'
        ]);

        if ($request->hasFile('icon_file')) {
            // Optional: delete old icon from storage if exists
            if ($clinic->icon && \Storage::disk('public')->exists($clinic->icon)) {
                \Storage::disk('public')->delete($clinic->icon);
            }
            $path = $request->file('icon_file')->store('clinics/icons', 'public');
            $data['icon'] = $path;
        }

        if (empty($data['color'])) {
            $data['color'] = '#14b8a6';
        }

        $clinic->update($data);

        return back()->with('success', 'تم تحديث بيانات العيادة بنجاح');
    }

    public function destroy($id)
    {
        $clinic = \App\Models\Clinic::withTrashed()->findOrFail($id);
        
        if ($clinic->trashed()) {
            // Force delete
            if ($clinic->doctors()->count() > 0) {
                return back()->withErrors(['error' => 'لا يمكن حذف هذه العيادة نهائياً لوجود أطباء مرتبطين بها. يرجى نقلهم أو حذفهم أولاً.']);
            }
            $clinic->forceDelete();
            return back()->with('success', 'تم حذف العيادة نهائياً');
        }

        if ($clinic->doctors()->count() > 0) {
            return back()->withErrors(['error' => 'لا يمكن أرشفة هذه العيادة لوجود أطباء مرتبطين بها. يرجى نقلهم أولاً.']);
        }
        
        $clinic->delete();
        return back()->with('success', 'تم أرشفة العيادة بنجاح');
    }
    
    public function restore($id)
    {
        $clinic = \App\Models\Clinic::withTrashed()->findOrFail($id);
        $clinic->restore();
        return back()->with('success', 'تم استعادة العيادة بنجاح');
    }

    public function assignDoctor(\Illuminate\Http\Request $request, $id)
    {
        $clinic = \App\Models\Clinic::withTrashed()->findOrFail($id);
        $request->validate([
            'doctor_id' => 'required|exists:users,id'
        ]);

        $doctor = \App\Models\User::find($request->doctor_id);
        $doctor->update(['clinic_id' => $clinic->id]);
        return back()->with('success', 'تم تعيين الطبيب للعيادة بنجاح');
    }
}
