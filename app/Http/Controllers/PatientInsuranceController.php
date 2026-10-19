<?php

namespace App\Http\Controllers;

use App\Models\PatientInsurance;
use Illuminate\Http\Request;

class PatientInsuranceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'insurance_company_id' => 'required|exists:insurance_companies,id',
            'policy_number' => 'required|string|max:100',
            'card_number' => 'required|string|max:100',
            'class' => 'required|string|max:50',
            'copay_percentage' => 'required|numeric|min:0|max:100',
            'max_copay_amount' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date',
        ]);

        PatientInsurance::create([
            'patient_id' => $request->patient_id,
            'insurance_company_id' => $request->insurance_company_id,
            'policy_number' => $request->policy_number,
            'card_number' => $request->card_number,
            'class' => $request->class,
            'copay_percentage' => $request->copay_percentage,
            'max_copay_amount' => $request->max_copay_amount,
            'expiry_date' => $request->expiry_date,
            'is_active' => true,
        ]);

        return back()->with('success', 'تم تسجيل وثيقة التأمين للمريض بنجاح.');
    }

    public function update(Request $request, PatientInsurance $patientInsurance)
    {
        $request->validate([
            'insurance_company_id' => 'required|exists:insurance_companies,id',
            'policy_number' => 'required|string|max:100',
            'card_number' => 'required|string|max:100',
            'class' => 'required|string|max:50',
            'copay_percentage' => 'required|numeric|min:0|max:100',
            'max_copay_amount' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $patientInsurance->update([
            'insurance_company_id' => $request->insurance_company_id,
            'policy_number' => $request->policy_number,
            'card_number' => $request->card_number,
            'class' => $request->class,
            'copay_percentage' => $request->copay_percentage,
            'max_copay_amount' => $request->max_copay_amount,
            'expiry_date' => $request->expiry_date,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'تم تحديث بيانات وثيقة التأمين بنجاح.');
    }

    public function destroy(PatientInsurance $patientInsurance)
    {
        $patientInsurance->delete();
        return back()->with('success', 'تم حذف وثيقة التأمين بنجاح.');
    }
}
