<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Prescription::with(['patient', 'doctor', 'pharmacist', 'items.medicine'])
            ->latest();

        // Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('prescription_number', 'like', "%{$search}%")
                  ->orWhereHas('patient', function ($p) use ($search) {
                      $p->where('first_name', 'like', "%{$search}%")
                        ->orWhere('family_name', 'like', "%{$search}%")
                        ->orWhere('mrn', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $prescriptions = $query->paginate(15)->withQueryString();

        // Stats
        $today = now()->startOfDay();
        $stats = [
            'total_today' => Prescription::whereDate('created_at', $today)->count(),
            'pending' => Prescription::where('status', 'معلقة')->count(),
            'dispensed_today' => Prescription::where('status', 'مصروفة')->whereDate('dispensed_at', $today)->count(),
        ];

        $patients = Patient::select('id', 'first_name', 'family_name', 'mrn')->get();
        $doctors = User::whereHas('roles', function ($q) {
            $q->where('name', 'like', '%طبيب%')->orWhere('name', 'like', '%Doctor%');
        })->where('status', 'نشط')->get();
        $availableMedicines = Medicine::where('is_active', true)->where('stock_quantity', '>', 0)->orderBy('name')->get();

        return view('pharmacy.prescriptions.index', compact('prescriptions', 'stats', 'patients', 'doctors', 'availableMedicines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:users,id',
            'visit_id' => 'nullable|exists:visits,id',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.medicine_name' => 'required|string|max:255',
            'items.*.dosage' => 'nullable|string|max:100',
            'items.*.frequency' => 'nullable|string|max:100',
            'items.*.duration' => 'nullable|string|max:100',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.instructions' => 'nullable|string|max:500',
        ], [
            'patient_id.required' => 'يرجى اختيار المريض.',
            'items.required' => 'يرجى إضافة دواء واحد على الأقل للوصفة.',
            'items.min' => 'يرجى إضافة دواء واحد على الأقل للوصفة.',
        ]);

        DB::beginTransaction();
        try {
            $prescription = Prescription::create([
                'patient_id' => $request->patient_id,
                'doctor_id' => $request->doctor_id ?? auth()->id(),
                'visit_id' => $request->visit_id,
                'status' => 'معلقة',
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $itemData) {
                $medicineId = $itemData['medicine_id'] ?? null;
                $medicineName = $itemData['medicine_name'];

                if ($medicineId) {
                    $med = Medicine::find($medicineId);
                    if ($med) {
                        $medicineName = $med->name . ' (' . ($med->strength ?? '') . ')';
                    }
                }

                PrescriptionItem::create([
                    'prescription_id' => $prescription->id,
                    'medicine_id' => $medicineId,
                    'medicine_name' => $medicineName,
                    'dosage' => $itemData['dosage'] ?? null,
                    'frequency' => $itemData['frequency'] ?? null,
                    'duration' => $itemData['duration'] ?? null,
                    'quantity' => $itemData['quantity'] ?? 1,
                    'instructions' => $itemData['instructions'] ?? null,
                    'is_dispensed' => false,
                ]);
            }

            DB::commit();

            if ($request->has('from_visit')) {
                return back()->with('success', "تم تحويل الوصفة الطبية لصيدلية العيادة بنجاح برقم: {$prescription->prescription_number}");
            }

            return redirect()->route('prescriptions.show', $prescription->id)
                ->with('success', "تم إنشاء الوصفة الطبية بنجاح برقم {$prescription->prescription_number}");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حفظ الوصفة: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Prescription $prescription)
    {
        $prescription->load(['patient', 'doctor', 'pharmacist', 'visit', 'items.medicine']);
        return view('pharmacy.prescriptions.show', compact('prescription'));
    }

    public function dispense(Request $request, Prescription $prescription)
    {
        if ($prescription->status === 'مصروفة') {
            return back()->with('info', 'هذه الوصفة تم صرفها مسبقاً.');
        }

        DB::beginTransaction();
        try {
            foreach ($prescription->items as $item) {
                if ($item->medicine_id) {
                    $medicine = Medicine::find($item->medicine_id);
                    if ($medicine) {
                        $qtyToDeduct = min($medicine->stock_quantity, $item->quantity);
                        $newStock = max(0, $medicine->stock_quantity - $item->quantity);
                        $medicine->update(['stock_quantity' => $newStock]);

                        InventoryTransaction::create([
                            'medicine_id' => $medicine->id,
                            'user_id' => auth()->id(),
                            'type' => 'صرف وصفة',
                            'quantity' => -$item->quantity,
                            'balance_after' => $newStock,
                            'reference' => $prescription->prescription_number,
                            'notes' => "صرف للمريض: {$prescription->patient->name}",
                        ]);
                    }
                }
                $item->update(['is_dispensed' => true]);
            }

            $prescription->update([
                'status' => 'مصروفة',
                'pharmacist_id' => auth()->id(),
                'dispensed_at' => now(),
            ]);

            DB::commit();
            return back()->with('success', 'تم صرف الوصفة الطبية وخصم الكميات من مستودع الصيدلية بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء صرف الأدوية: ' . $e->getMessage());
        }
    }

    public function print(Prescription $prescription)
    {
        $prescription->load(['patient', 'doctor', 'pharmacist', 'visit', 'items.medicine']);
        $settings = Setting::all()->pluck('value', 'key');
        return view('pharmacy.prescriptions.print', compact('prescription', 'settings'));
    }

    public function destroy(Prescription $prescription)
    {
        $prescription->delete();
        return redirect()->route('prescriptions.index')->with('success', 'تم حذف الوصفة الطبية بنجاح.');
    }
}
