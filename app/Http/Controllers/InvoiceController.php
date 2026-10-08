<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Service;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::with(['patient', 'doctor'])->orderBy('created_at', 'desc')->paginate(15);
        return view('invoices.index', compact('invoices'));
    }

    public function create(Request $request)
    {
        $patients = Patient::orderBy('first_name')->get();
        $doctors = User::whereHas('roles', function($q) {
            $q->where('name', 'like', '%طبيب%')->orWhere('name', 'like', '%Doctor%');
        })->where('status', 'نشط')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();
        
        $selectedPatientId = $request->get('patient_id');

        return view('invoices.create', compact('patients', 'doctors', 'services', 'selectedPatientId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:users,id',
            'issue_date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        $subtotal = 0;
        
        $invoice = Invoice::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'issue_date' => $request->issue_date,
            'due_date' => $request->due_date ?? $request->issue_date,
            'notes' => $request->notes,
            'status' => 'unpaid',
        ]);

        foreach ($request->items as $item) {
            if (!empty($item['service_id']) || !empty($item['description'])) {
                $service = !empty($item['service_id']) ? Service::find($item['service_id']) : null;
                $description = $service ? $service->name : $item['description'];
                $unitPrice = $service ? $service->price : ($item['unit_price'] ?? 0);
                $qty = $item['quantity'] ?? 1;
                $total = $unitPrice * $qty;

                $invoice->items()->create([
                    'service_id' => $service ? $service->id : null,
                    'description' => $description,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'total' => $total,
                ]);

                $subtotal += $total;
            }
        }

        $discount = $request->discount ?? 0;
        $taxRate = 0.15; 
        $taxableAmount = $subtotal - $discount;
        $tax = $taxableAmount * $taxRate;
        $totalAmount = $taxableAmount + $tax;

        $invoice->update([
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'total' => $totalAmount,
            'paid_amount' => 0
        ]);

        return redirect()->route('invoices.show', $invoice->id)->with('success', 'تم إنشاء الفاتورة بنجاح. يمكنك الآن تحصيل الدفعة.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['patient', 'doctor', 'items', 'payments']);
        return view('invoices.show', compact('invoice'));
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->items()->delete();
        $invoice->payments()->delete();
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'تم إلغاء وحذف الفاتورة بنجاح.');
    }
}
