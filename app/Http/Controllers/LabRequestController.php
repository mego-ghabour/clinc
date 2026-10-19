<?php

namespace App\Http\Controllers;

use App\Models\LabRequest;
use App\Models\LabRequestItem;
use App\Models\LabTest;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = LabRequest::with(['patient', 'doctor', 'items'])
            ->latest();

        // Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Priority Filter
        if ($request->filled('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        // Search Filter (Request Number, Patient Name, MRN)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                  ->orWhereHas('patient', function ($p) use ($search) {
                      $p->where('first_name', 'like', "%{$search}%")
                        ->orWhere('family_name', 'like', "%{$search}%")
                        ->orWhere('mrn', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $labRequests = $query->paginate(15)->withQueryString();

        // Statistics
        $today = now()->startOfDay();
        $stats = [
            'total_today' => LabRequest::whereDate('created_at', $today)->count(),
            'pending' => LabRequest::where('status', 'معلق')->count(),
            'in_progress' => LabRequest::whereIn('status', ['تم سحب العينة', 'قيد التحليل'])->count(),
            'completed_today' => LabRequest::where('status', 'مكتمل')->whereDate('completed_at', $today)->count(),
        ];

        // Auxiliary data for modal
        $availableTests = LabTest::where('is_active', true)->orderBy('category')->orderBy('name')->get();
        $patients = Patient::select('id', 'first_name', 'family_name', 'mrn')->get();
        $doctors = User::whereHas('roles', function ($q) {
            $q->where('name', 'like', '%طبيب%')->orWhere('name', 'like', '%Doctor%');
        })->where('status', 'نشط')->get();

        return view('lab.index', compact('labRequests', 'stats', 'availableTests', 'patients', 'doctors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:users,id',
            'visit_id' => 'nullable|exists:visits,id',
            'priority' => 'required|in:عادي,عاجل,حرج',
            'clinical_notes' => 'nullable|string|max:1000',
            'test_ids' => 'required|array|min:1',
            'test_ids.*' => 'exists:lab_tests,id',
        ], [
            'patient_id.required' => 'يرجى اختيار المريض.',
            'priority.required' => 'يرجى تحديد أولوية الفحص.',
            'test_ids.required' => 'يرجى اختيار فحص مخبري واحد على الأقل.',
            'test_ids.min' => 'يرجى اختيار فحص مخبري واحد على الأقل.',
        ]);

        DB::beginTransaction();
        try {
            $labRequest = LabRequest::create([
                'patient_id' => $request->patient_id,
                'doctor_id' => $request->doctor_id ?? auth()->id(),
                'visit_id' => $request->visit_id,
                'priority' => $request->priority,
                'clinical_notes' => $request->clinical_notes,
                'status' => 'معلق',
            ]);

            $tests = LabTest::whereIn('id', $request->test_ids)->get();
            foreach ($tests as $test) {
                LabRequestItem::create([
                    'lab_request_id' => $labRequest->id,
                    'lab_test_id' => $test->id,
                    'test_name' => $test->name,
                    'normal_range' => $test->normal_range,
                    'unit' => $test->unit,
                ]);
            }

            DB::commit();

            if ($request->has('from_visit')) {
                return back()->with('success', "تم إصدار طلب التحاليل المخبرية بنجاح برقم: {$labRequest->request_number}");
            }

            return redirect()->route('lab-requests.show', $labRequest->id)
                ->with('success', "تم إنشاء طلب التحاليل بنجاح برقم {$labRequest->request_number}");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حفظ طلب التحاليل: ' . $e->getMessage())->withInput();
        }
    }

    public function show(LabRequest $labRequest)
    {
        $labRequest->load(['patient', 'doctor', 'technician', 'visit', 'items.labTest']);
        return view('lab.show', compact('labRequest'));
    }

    public function updateStatus(Request $request, LabRequest $labRequest)
    {
        $request->validate([
            'status' => 'required|in:معلق,تم سحب العينة,قيد التحليل,مكتمل,ملغي',
        ]);

        $data = ['status' => $request->status];

        if ($request->status === 'تم سحب العينة' && is_null($labRequest->sample_collected_at)) {
            $data['sample_collected_at'] = now();
        }

        if ($request->status === 'مكتمل') {
            $data['completed_at'] = now();
            $data['technician_id'] = auth()->id();
        }

        $labRequest->update($data);

        return back()->with('success', 'تم تحديث حالة طلب التحليل بنجاح.');
    }

    public function updateResults(Request $request, LabRequest $labRequest)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.result_value' => 'nullable|string|max:255',
            'items.*.is_abnormal' => 'nullable|boolean',
            'items.*.notes' => 'nullable|string|max:500',
            'action' => 'required|in:save_draft,finalize',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->items as $itemId => $itemData) {
                $item = LabRequestItem::where('lab_request_id', $labRequest->id)
                    ->where('id', $itemId)
                    ->first();

                if ($item) {
                    $item->update([
                        'result_value' => $itemData['result_value'] ?? null,
                        'is_abnormal' => isset($itemData['is_abnormal']) ? (bool)$itemData['is_abnormal'] : false,
                        'notes' => $itemData['notes'] ?? null,
                    ]);
                }
            }

            if ($request->action === 'finalize') {
                $labRequest->update([
                    'status' => 'مكتمل',
                    'completed_at' => now(),
                    'technician_id' => auth()->id(),
                ]);
                $msg = 'تم اعتماد نتائج الفحوصات المخبرية وإغلاق الطلب بنجاح.';
            } else {
                if ($labRequest->status === 'معلق' || $labRequest->status === 'تم سحب العينة') {
                    $labRequest->update(['status' => 'قيد التحليل']);
                }
                $msg = 'تم حفظ مسودة نتائج الفحوصات بنجاح.';
            }

            DB::commit();
            return back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حفظ النتائج: ' . $e->getMessage());
        }
    }

    public function printReport(LabRequest $labRequest)
    {
        $labRequest->load(['patient', 'doctor', 'technician', 'visit', 'items.labTest']);
        
        $settings = Setting::all()->pluck('value', 'key');

        return view('lab.print', compact('labRequest', 'settings'));
    }

    public function destroy(LabRequest $labRequest)
    {
        $labRequest->delete();
        return redirect()->route('lab-requests.index')->with('success', 'تم حذف طلب التحليل بنجاح.');
    }
}
