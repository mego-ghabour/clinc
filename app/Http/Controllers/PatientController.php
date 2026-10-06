<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\Patient::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('mrn', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('family_name', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
        }

        $patients = $query->orderBy('id', 'desc')->paginate(15);
        
        return view('patients.index', compact('patients'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'family_name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:20',
            // add more validation as needed
        ]);

        \App\Models\Patient::create($request->all());

        return back()->with('success', 'تم إضافة المريض بنجاح');
    }

    public function show(\App\Models\Patient $patient)
    {
        return view('patients.show', compact('patient'));
    }
}
