<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::withTrashed();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('mrn', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('family_name', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        $patients = $query->orderBy('id', 'desc')->paginate(15);
        
        return view('patients.index', compact('patients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'family_name' => 'required|string|max:255',
            'id_number' => 'nullable|string|max:50|unique:patients,id_number',
            'mobile' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'id_number.unique' => 'رقم الهوية مسجل مسبقاً لمريض آخر.',
        ]);

        $data = $request->all();
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars/patients', 'public');
        }

        Patient::create($data);

        return back()->with('success', 'تم إضافة المريض بنجاح');
    }

    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'family_name' => 'required|string|max:255',
            'id_number' => 'nullable|string|max:50|unique:patients,id_number,' . $patient->id,
            'mobile' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'id_number.unique' => 'رقم الهوية مسجل مسبقاً لمريض آخر.',
        ]);

        $data = $request->all();
        if ($request->hasFile('avatar')) {
            if ($patient->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($patient->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($patient->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars/patients', 'public');
        }

        $patient->update($data);

        return back()->with('success', 'تم تعديل بيانات المريض بنجاح');
    }

    public function destroy($id)
    {
        $patient = Patient::withTrashed()->findOrFail($id);

        if ($patient->trashed()) {
            // Check if patient has appointments, etc. (Can implement later)
            $patient->forceDelete();
            return back()->with('success', 'تم حذف ملف المريض نهائياً');
        }

        $patient->delete();
        return back()->with('success', 'تم أرشفة ملف المريض بنجاح');
    }

    public function restore($id)
    {
        $patient = Patient::withTrashed()->findOrFail($id);
        $patient->restore();
        
        return back()->with('success', 'تم استعادة ملف المريض بنجاح');
    }

    public function show(Patient $patient)
    {
        // Simple profile view for now
        return view('patients.show', compact('patient'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q') ?? $request->get('search');
        if (!$query) {
            return response()->json([]);
        }

        $patients = Patient::where('first_name', 'like', "%{$query}%")
            ->orWhere('family_name', 'like', "%{$query}%")
            ->orWhere('id_number', 'like', "%{$query}%")
            ->orWhere('mobile', 'like', "%{$query}%")
            ->orWhere('mrn', 'like', "%{$query}%")
            ->take(10)
            ->get(['id', 'first_name', 'family_name', 'mrn', 'mobile', 'avatar']);
        
        return response()->json($patients);
    }
}
