<?php

namespace App\Http\Controllers;

use App\Models\InsuranceCompany;
use Illuminate\Http\Request;

class InsuranceCompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = InsuranceCompany::withCount(['patientInsurances', 'claims'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%");
            });
        }

        $companies = $query->paginate(15)->withQueryString();

        return view('insurance.companies.index', compact('companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:insurance_companies,code',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'اسم شركة التأمين مطلوب.',
            'code.required' => 'رمز شركة التأمين مطلوب.',
            'code.unique' => 'رمز شركة التأمين مسجل مسبقاً.',
        ]);

        InsuranceCompany::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'contact_person' => $request->contact_person,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'discount_percentage' => $request->discount_percentage ?? 0,
            'notes' => $request->notes,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'تمت إضافة شركة التأمين بنجاح.');
    }

    public function update(Request $request, InsuranceCompany $insuranceCompany)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:insurance_companies,code,' . $insuranceCompany->id,
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $insuranceCompany->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'contact_person' => $request->contact_person,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'discount_percentage' => $request->discount_percentage ?? $insuranceCompany->discount_percentage,
            'notes' => $request->notes,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'تم تحديث بيانات شركة التأمين بنجاح.');
    }

    public function destroy(InsuranceCompany $insuranceCompany)
    {
        $insuranceCompany->delete();
        return back()->with('success', 'تم حذف شركة التأمين بنجاح.');
    }
}
