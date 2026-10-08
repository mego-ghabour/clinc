<?php

namespace App\Http\Controllers;

use App\Models\RadiologyRequest;
use Illuminate\Http\Request;

class RadiologyRequestController extends Controller
{
    public function index()
    {
        // For the Radiology department dashboard
        $requests = RadiologyRequest::with(['patient', 'doctor'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 1 ELSE 2 END")
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('radiology.index', compact('requests'));
    }

    public function store(Request $request)
    {
        // Doctor requesting from EMR
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'visit_id' => 'required|exists:visits,id',
            'type' => 'required|string',
            'body_part' => 'nullable|string',
            'clinical_notes' => 'nullable|string',
        ]);

        RadiologyRequest::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'visit_id' => $request->visit_id,
            'type' => $request->type,
            'body_part' => $request->body_part,
            'clinical_notes' => $request->clinical_notes,
            'status' => 'pending',
        ]);

        return back()->with('success', 'تم إرسال طلب الأشعة إلى القسم المختص بنجاح.');
    }

    public function update(Request $request, RadiologyRequest $radiology)
    {
        // Radiologist uploading results
        $request->validate([
            'report_text' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // max 5MB
        ]);

        $data = [
            'report_text' => $request->report_text,
            'status' => 'completed',
            'radiologist_id' => auth()->id(),
            'completed_at' => now(),
        ];

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('radiology', 'public');
            $data['image_path'] = $path;
        }

        $radiology->update($data);

        return back()->with('success', 'تم حفظ تقرير الأشعة وإرفاق الصور بنجاح.');
    }

    public function show(RadiologyRequest $radiology)
    {
        $radiology->load(['patient', 'doctor', 'radiologist']);
        return view('radiology.show', compact('radiology'));
    }
}
