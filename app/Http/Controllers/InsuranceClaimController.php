<?php

namespace App\Http\Controllers;

use App\Models\InsuranceClaim;
use App\Models\InsuranceCompany;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class InsuranceClaimController extends Controller
{
    public function index(Request $request)
    {
        $query = InsuranceClaim::with(['insuranceCompany', 'patient', 'doctor', 'invoice'])
            ->latest();

        // Company Filter
        if ($request->filled('insurance_company_id') && $request->insurance_company_id !== 'all') {
            $query->where('insurance_company_id', $request->insurance_company_id);
        }

        // Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('claim_number', 'like', "%{$search}%")
                  ->orWhereHas('patient', function ($p) use ($search) {
                      $p->where('first_name', 'like', "%{$search}%")
                        ->orWhere('family_name', 'like', "%{$search}%")
                        ->orWhere('mrn', 'like', "%{$search}%");
                  });
            });
        }

        $claims = $query->paginate(15)->withQueryString();
        $companies = InsuranceCompany::where('is_active', true)->orderBy('name')->get();

        // Stats
        $stats = [
            'total_claims' => InsuranceClaim::count(),
            'pending_claims' => InsuranceClaim::whereIn('status', ['معلقة', 'موافقة مبدئية'])->count(),
            'total_insurance_share' => (float) InsuranceClaim::sum('insurance_share'),
            'settled_amount' => (float) InsuranceClaim::where('status', 'مسددة')->sum('insurance_share'),
        ];

        $patients = Patient::select('id', 'first_name', 'family_name', 'mrn')->get();
        $doctors = User::whereHas('roles', function ($q) {
            $q->where('name', 'like', '%طبيب%')->orWhere('name', 'like', '%Doctor%');
        })->where('status', 'نشط')->get();
        $invoices = Invoice::latest()->take(20)->get();

        return view('insurance.claims.index', compact('claims', 'companies', 'stats', 'patients', 'doctors', 'invoices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'insurance_company_id' => 'required|exists:insurance_companies,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'doctor_id' => 'nullable|exists:users,id',
            'claim_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'patient_copay' => 'required|numeric|min:0',
            'insurance_share' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ], [
            'patient_id.required' => 'يرجى اختيار المريض.',
            'insurance_company_id.required' => 'يرجى اختيار شركة التأمين.',
            'total_amount.required' => 'المبلغ الإجمالي مطلوب.',
            'insurance_share.required' => 'مبلغ مطالبة التأمين مطلوب.',
        ]);

        $claim = InsuranceClaim::create([
            'patient_id' => $request->patient_id,
            'insurance_company_id' => $request->insurance_company_id,
            'invoice_id' => $request->invoice_id,
            'doctor_id' => $request->doctor_id ?? auth()->id(),
            'claim_date' => $request->claim_date,
            'total_amount' => $request->total_amount,
            'patient_copay' => $request->patient_copay,
            'insurance_share' => $request->insurance_share,
            'status' => 'معلقة',
            'notes' => $request->notes,
        ]);

        return redirect()->route('insurance-claims.show', $claim->id)
            ->with('success', "تم تسجيل مطالبة التأمين بنجاح برقم: {$claim->claim_number}");
    }

    public function show(InsuranceClaim $insuranceClaim)
    {
        $insuranceClaim->load(['insuranceCompany', 'patient.activeInsurance', 'doctor', 'invoice.items']);
        return view('insurance.claims.show', compact('insuranceClaim'));
    }

    public function updateStatus(Request $request, InsuranceClaim $insuranceClaim)
    {
        $request->validate([
            'status' => 'required|in:معلقة,موافقة مبدئية,مقبولة,مرفوضة,مسددة',
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $data = [
            'status' => $request->status,
            'rejection_reason' => $request->status === 'مرفوضة' ? $request->rejection_reason : null,
        ];

        if ($request->status === 'مسددة' && is_null($insuranceClaim->settled_at)) {
            $data['settled_at'] = now();
        }

        $insuranceClaim->update($data);

        return back()->with('success', 'تم تحديث حالة مطالبة التأمين بنجاح.');
    }

    public function print(InsuranceClaim $insuranceClaim)
    {
        $insuranceClaim->load(['insuranceCompany', 'patient', 'doctor', 'invoice.items']);
        $settings = Setting::all()->pluck('value', 'key');
        return view('insurance.claims.print', compact('insuranceClaim', 'settings'));
    }

    public function destroy(InsuranceClaim $insuranceClaim)
    {
        $insuranceClaim->delete();
        return redirect()->route('insurance-claims.index')->with('success', 'تم حذف مطالبة التأمين بنجاح.');
    }
}
