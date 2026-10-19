<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $query = Medicine::latest();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('generic_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Stock Status Filter
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low_stock') {
                $query->lowStock();
            } elseif ($request->stock_status === 'expiring_soon') {
                $query->expiringSoon(60);
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->where('stock_quantity', '<=', 0);
            }
        }

        $medicines = $query->paginate(15)->withQueryString();
        $categories = Medicine::select('category')->distinct()->pluck('category');

        // Statistics
        $stats = [
            'total_medicines' => Medicine::count(),
            'low_stock' => Medicine::lowStock()->count(),
            'expiring_soon' => Medicine::expiringSoon(60)->count(),
            'inventory_value' => (float) Medicine::select(DB::raw('SUM(stock_quantity * unit_price) as total'))->value('total'),
        ];

        return view('pharmacy.medicines.index', compact('medicines', 'categories', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:medicines,code',
            'generic_name' => 'nullable|string|max:255',
            'category' => 'required|string|max:100',
            'dosage_form' => 'required|string|max:100',
            'strength' => 'nullable|string|max:100',
            'unit_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'expiry_date' => 'nullable|date',
            'batch_number' => 'nullable|string|max:100',
            'manufacturer' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'اسم الدواء التجاري مطلوب.',
            'code.required' => 'كود الدواء مطلوب.',
            'code.unique' => 'كود الدواء مسجل مسبقاً.',
            'category.required' => 'يرجى تحديد تصنيف الدواء.',
            'unit_price.required' => 'يرجى تحديد سعر البيع.',
        ]);

        DB::beginTransaction();
        try {
            $medicine = Medicine::create([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'generic_name' => $request->generic_name,
                'category' => $request->category,
                'dosage_form' => $request->dosage_form,
                'strength' => $request->strength,
                'unit_price' => $request->unit_price,
                'cost_price' => $request->cost_price ?? 0,
                'stock_quantity' => $request->stock_quantity,
                'reorder_level' => $request->reorder_level ?? 10,
                'expiry_date' => $request->expiry_date,
                'batch_number' => $request->batch_number,
                'manufacturer' => $request->manufacturer,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true),
            ]);

            if ($medicine->stock_quantity > 0) {
                InventoryTransaction::create([
                    'medicine_id' => $medicine->id,
                    'user_id' => auth()->id(),
                    'type' => 'رصيد افتتاحي',
                    'quantity' => $medicine->stock_quantity,
                    'balance_after' => $medicine->stock_quantity,
                    'notes' => 'تسجيل الرصيد الافتتاحي للدواء',
                ]);
            }

            DB::commit();
            return back()->with('success', 'تمت إضافة الدواء بنجاح في دليل الصيدلية والمستودع.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إضافة الدواء: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, Medicine $medicine)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:medicines,code,' . $medicine->id,
            'generic_name' => 'nullable|string|max:255',
            'category' => 'required|string|max:100',
            'dosage_form' => 'required|string|max:100',
            'strength' => 'nullable|string|max:100',
            'unit_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'expiry_date' => 'nullable|date',
            'batch_number' => 'nullable|string|max:100',
            'manufacturer' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $medicine->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'generic_name' => $request->generic_name,
            'category' => $request->category,
            'dosage_form' => $request->dosage_form,
            'strength' => $request->strength,
            'unit_price' => $request->unit_price,
            'cost_price' => $request->cost_price ?? $medicine->cost_price,
            'reorder_level' => $request->reorder_level ?? $medicine->reorder_level,
            'expiry_date' => $request->expiry_date,
            'batch_number' => $request->batch_number,
            'manufacturer' => $request->manufacturer,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'تم تحديث بيانات الدواء بنجاح.');
    }

    public function adjustStock(Request $request, Medicine $medicine)
    {
        $request->validate([
            'type' => 'required|in:إضافة رصيد,تسوية جردية,إتلاف/منتهي',
            'quantity' => 'required|integer',
            'notes' => 'nullable|string|max:255',
        ]);

        $qtyChange = (int) $request->quantity;
        
        if ($request->type === 'إتلاف/منتهي' && $qtyChange > 0) {
            $qtyChange = -$qtyChange;
        }

        $newBalance = max(0, $medicine->stock_quantity + $qtyChange);

        DB::beginTransaction();
        try {
            $medicine->update(['stock_quantity' => $newBalance]);

            InventoryTransaction::create([
                'medicine_id' => $medicine->id,
                'user_id' => auth()->id(),
                'type' => $request->type,
                'quantity' => $qtyChange,
                'balance_after' => $newBalance,
                'notes' => $request->notes,
            ]);

            DB::commit();
            return back()->with('success', "تم تعديل رصيد المخزون للدواء {$medicine->name}. الرصيد الجديد: {$newBalance}");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تعديل المخزون: ' . $e->getMessage());
        }
    }

    public function destroy(Medicine $medicine)
    {
        $medicine->delete();
        return back()->with('success', 'تم حذف الدواء بنجاح من الصيدلية.');
    }
}
