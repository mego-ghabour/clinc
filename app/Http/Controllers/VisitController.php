<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public function index(Request $request)
    {
        $query = Visit::with(['patient', 'doctor'])->latest('visit_date');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search != '') {
            $query->whereHas('patient', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('mrn', 'like', '%' . $request->search . '%');
            });
        }

        $visits = $query->paginate(15)->withQueryString();
        
        // Data for Add Modal
        $patients = Patient::select('id', 'name', 'mrn')->get();
        $doctors = User::whereHas('roles', function($q) {
            $q->where('name', 'like', '%Ø·Ø¨ÙŠØ¨%')->orWhere('name', 'like', '%Doctor%');
        })->where('status', 'Ù†Ø´Ø·')->get();

        return view('visits.index', compact('visits', 'patients', 'doctors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'visit_date' => 'required|date',
            'type' => 'required|string',
            'status' => 'required|string',
        ]);

        Visit::create($request->all());

        return back()->with('success', 'ØªÙ… ØªØ³Ø¬ÙŠÙ„ Ø§Ù„Ø²ÙŠØ§Ø±Ø© ÙˆÙØªØ­ Ø§Ù„Ù…Ù„Ù Ø§Ù„Ø·Ø¨ÙŠ Ø¨Ù†Ø¬Ø§Ø­.');
    }

    public function show(Visit $visit)
    {
        $visit->load(['patient', 'doctor']);
        return view('visits.show', compact('visit'));
    }

        public function update(Request $request, Visit $visit)
    {
        $visit->update($request->all());

        if ($visit->appointment_id) {
            $appointment = \App\Models\Appointment::find($visit->appointment_id);
            if ($appointment) {
                if ($visit->status === 'مكتملة') {
                    $appointment->update(['status' => 'مكتمل']);
                }
            }
        }

        return back()->with('success', 'تم تحديث الملف الطبي بنجاح.');
    }

    public function destroy(Visit $visit)
    {
        $visit->delete();
        return back()->with('success', 'ØªÙ… Ø­Ø°Ù Ø§Ù„Ø²ÙŠØ§Ø±Ø© Ø¨Ù†Ø¬Ø§Ø­.');
    }
}

