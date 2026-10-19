<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\InsuranceCompany;
use App\Models\LabTest;
use App\Models\Medicine;
use App\Models\Patient;
use Spatie\Permission\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class SystemGuideController extends Controller
{
    /**
     * Display the comprehensive system user guide and operational manual.
     */
    public function index()
    {
        $settings = Setting::pluck('value', 'key');
        
        $systemStats = [
            'total_patients' => Patient::count(),
            'total_doctors' => User::whereHas('roles', function ($q) {
                $q->where('name', 'like', '%Doctor%')
                  ->orWhere('name', 'like', '%طبيب%');
            })->count(),
            'total_clinics' => Clinic::count(),
            'total_lab_tests' => LabTest::count(),
            'total_medicines' => Medicine::count(),
            'total_insurances' => InsuranceCompany::count(),
            'total_roles' => Role::count(),
        ];

        return view('guide.index', compact('settings', 'systemStats'));
    }

    /**
     * Download the complete system operational guide as a Word document (.doc).
     */
    public function downloadDoc()
    {
        $filePath = public_path('downloads/دليل_شامل_لتشغيل_نظام_العيادات.doc');
        
        if (!file_exists($filePath)) {
            abort(404, 'ملف الدليل غير متوفر حالياً.');
        }

        return response()->download(
            $filePath,
            'دليل_شامل_لتشغيل_نظام_العيادات.doc',
            [
                'Content-Type' => 'application/msword; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="دليل_شامل_لتشغيل_نظام_العيادات.doc"',
            ]
        );
    }
}
