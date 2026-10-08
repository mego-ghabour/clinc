<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function store(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
        ]);

        if ($request->amount > $invoice->balance) {
            return back()->withErrors(['amount' => 'المبلغ المدفوع أكبر من الرصيد المتبقي على الفاتورة.']);
        }

        DB::transaction(function () use ($request, $invoice) {
            // Record Payment
            Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_date' => now(),
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
            ]);

            // Update Invoice Paid Amount
            $invoice->paid_amount += $request->amount;
            
            // Update Status
            if ($invoice->paid_amount >= $invoice->total) {
                $invoice->status = 'paid';
            } elseif ($invoice->paid_amount > 0) {
                $invoice->status = 'partially_paid';
            }
            
            $invoice->save();
        });

        return back()->with('success', 'تم تسجيل الدفعة بنجاح وتحديث رصيد الفاتورة.');
    }

    public function destroy(Payment $payment)
    {
        DB::transaction(function () use ($payment) {
            $invoice = $payment->invoice;
            
            // Revert Invoice Paid Amount
            $invoice->paid_amount -= $payment->amount;
            
            // Update Status
            if ($invoice->paid_amount <= 0) {
                $invoice->status = 'unpaid';
            } elseif ($invoice->paid_amount < $invoice->total) {
                $invoice->status = 'partially_paid';
            }
            
            $invoice->save();
            $payment->delete();
        });

        return back()->with('success', 'تم إلغاء الدفعة واسترجاع رصيد الفاتورة.');
    }
}
