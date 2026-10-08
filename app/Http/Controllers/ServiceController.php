<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('name')->get();
        return view('services.index', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        Service::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return back()->with('success', 'تم إضافة الخدمة بنجاح.');
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $service->update([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return back()->with('success', 'تم تحديث الخدمة بنجاح.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return back()->with('success', 'تم حذف الخدمة بنجاح.');
    }
}
