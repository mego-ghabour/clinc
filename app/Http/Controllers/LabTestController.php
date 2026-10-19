<?php

namespace App\Http\Controllers;

use App\Models\LabTest;
use Illuminate\Http\Request;

class LabTestController extends Controller
{
    public function index(Request $request)
    {
        $query = LabTest::latest();

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $tests = $query->paginate(20)->withQueryString();
        $categories = LabTest::select('category')->distinct()->pluck('category');

        return view('lab.tests', compact('tests', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:lab_tests,code',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'sample_type' => 'nullable|string|max:100',
            'normal_range' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'اسم الفحص مطلوب.',
            'code.required' => 'كود الفحص مطلوب.',
            'code.unique' => 'كود الفحص مسجل مسبقاً.',
            'category.required' => 'يرجى تحديد تصنيف الفحص.',
            'price.required' => 'يرجى تحديد سعر الفحص.',
        ]);

        LabTest::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'category' => $request->category,
            'price' => $request->price,
            'sample_type' => $request->sample_type,
            'normal_range' => $request->normal_range,
            'unit' => $request->unit,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'تمت إضافة الفحص المخبري بنجاح.');
    }

    public function update(Request $request, LabTest $labTest)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:lab_tests,code,' . $labTest->id,
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'sample_type' => 'nullable|string|max:100',
            'normal_range' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $labTest->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'category' => $request->category,
            'price' => $request->price,
            'sample_type' => $request->sample_type,
            'normal_range' => $request->normal_range,
            'unit' => $request->unit,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'تم تحديث بيانات الفحص المخبري بنجاح.');
    }

    public function destroy(LabTest $labTest)
    {
        $labTest->delete();
        return back()->with('success', 'تم حذف الفحص المخبري بنجاح.');
    }
}
