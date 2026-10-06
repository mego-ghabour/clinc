<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = \App\Models\Setting::all()->keyBy('key');
        return view('settings.index', compact('settings'));
    }

    public function update(\Illuminate\Http\Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            if ($request->hasFile($key)) {
                $path = $request->file($key)->store('settings', 'public');
                \App\Models\Setting::updateOrCreate(['key' => $key], ['value' => $path, 'type' => 'image']);
            } else {
                \App\Models\Setting::updateOrCreate(['key' => $key], ['value' => $value, 'type' => 'text']);
            }
        }

        return back()->with('success', 'تم حفظ الإعدادات بنجاح');
    }
}
