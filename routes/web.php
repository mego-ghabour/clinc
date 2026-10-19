<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SystemUserController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DoctorScheduleController;
use App\Http\Controllers\VisitController;

// Auth Routes
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.post');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return redirect()->route('login');
});

// Public Booking Portal (Protected with rate limiting against bots)
Route::get('book', [\App\Http\Controllers\PublicBookingController::class, 'index'])->name('booking.index');
Route::post('book', [\App\Http\Controllers\PublicBookingController::class, 'store'])->middleware('throttle:10,1')->name('booking.store');
Route::get('api/public/clinics/{clinic}/doctors', [\App\Http\Controllers\PublicBookingController::class, 'getDoctors']);
Route::get('api/public/doctors/{doctor}/slots', [\App\Http\Controllers\PublicBookingController::class, 'getAvailableSlots']);

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/guide', [\App\Http\Controllers\SystemGuideController::class, 'index'])->name('guide.index');
    Route::get('/guide/download-doc', [\App\Http\Controllers\SystemGuideController::class, 'downloadDoc'])->name('guide.download.doc');

    Route::get('/booking', function () {
        return view('booking');
    });

    // WhatsApp Notification Actions
    Route::post('appointments/{appointment}/whatsapp', [\App\Http\Controllers\WhatsAppController::class, 'sendAppointment'])->name('appointments.whatsapp');
    Route::post('lab-requests/{lab_request}/whatsapp', [\App\Http\Controllers\WhatsAppController::class, 'sendLab'])->name('lab-requests.whatsapp');
    Route::post('prescriptions/{prescription}/whatsapp', [\App\Http\Controllers\WhatsAppController::class, 'sendPrescription'])->name('prescriptions.whatsapp');

    // Super Admin Protected Management Routes
    Route::middleware('role:Super Admin')->group(function () {
        // Settings & Backups
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('settings/backups/create', [SettingController::class, 'createBackup'])->name('settings.backups.create');
        Route::get('settings/backups/{filename}/download', [SettingController::class, 'downloadBackup'])->name('settings.backups.download');
        Route::delete('settings/backups/{filename}', [SettingController::class, 'deleteBackup'])->name('settings.backups.delete');

        // Roles & System Users
        Route::resource('roles', RoleController::class)->except(['create', 'show', 'edit']);
        Route::resource('system-users', SystemUserController::class)->except(['create', 'edit']);
        Route::post('system-users/{id}/permissions', [SystemUserController::class, 'updatePermissions'])->name('system_users.updatePermissions');
        Route::patch('system-users/{id}/toggle-status', [SystemUserController::class, 'toggleStatus'])->name('system_users.toggleStatus');

        // Audit Logs
        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit_logs.index');
        Route::post('audit-logs/{id}/rollback', [AuditLogController::class, 'rollback'])->name('audit_logs.rollback');

        // WhatsApp Logs & Settings
        Route::get('whatsapp/logs', [\App\Http\Controllers\WhatsAppController::class, 'index'])->name('whatsapp.logs');
        Route::post('whatsapp/test', [\App\Http\Controllers\WhatsAppController::class, 'sendTest'])->name('whatsapp.test');
        Route::post('whatsapp/qr/connect', [\App\Http\Controllers\WhatsAppController::class, 'connectQr'])->name('whatsapp.qr.connect');
        Route::post('whatsapp/qr/disconnect', [\App\Http\Controllers\WhatsAppController::class, 'disconnectQr'])->name('whatsapp.qr.disconnect');
        Route::get('whatsapp/status/check', [\App\Http\Controllers\WhatsAppController::class, 'checkStatus'])->name('whatsapp.status.check');
        Route::get('whatsapp/local/qr', [\App\Http\Controllers\WhatsAppController::class, 'localQr'])->name('whatsapp.local.qr');
        Route::get('whatsapp/local/status', [\App\Http\Controllers\WhatsAppController::class, 'localStatus'])->name('whatsapp.local.status');
        Route::post('whatsapp/local/logout', [\App\Http\Controllers\WhatsAppController::class, 'localLogout'])->name('whatsapp.local.logout');
    });

    // Patients API
    Route::get('/api/patients/search', [PatientController::class, 'search'])->name('patients.search');

    // Clinics
    Route::resource('clinics', ClinicController::class)->except(['create', 'edit']);
    Route::post('clinics/{clinic}/restore', [ClinicController::class, 'restore'])->name('clinics.restore');
    Route::post('clinics/{clinic}/assign-doctor', [ClinicController::class, 'assignDoctor'])->name('clinics.assign_doctor');

    // Patients
    Route::resource('patients', PatientController::class)->except(['create', 'edit']);
    Route::post('patients/{patient}/restore', [PatientController::class, 'restore'])->name('patients.restore');

    // Users (legacy maybe?)
    Route::resource('users', UserController::class)->only(['index', 'store']);

    // Appointments
    Route::resource('appointments', AppointmentController::class);
    Route::get('queues', [\App\Http\Controllers\QueueController::class, 'index'])->name('queues.index');
    Route::post('queues/{appointment}/checkin', [\App\Http\Controllers\QueueController::class, 'checkIn'])->name('queues.checkin');

    // Doctors
    Route::resource('doctors', DoctorController::class)->only(['index', 'store', 'show', 'update']);
    Route::patch('doctors/{id}/toggle-account-status', [DoctorController::class, 'toggleAccountStatus'])->name('doctors.toggleAccountStatus');
    Route::patch('doctors/{id}/toggle-status', [DoctorController::class, 'toggleStatus'])->name('doctors.toggleStatus');

    // Impersonation Routes
    Route::impersonate();

    Route::resource('visits', VisitController::class);

    // Doctor Schedules
    Route::resource('doctor-schedules', DoctorScheduleController::class)->only(['index', 'show', 'store']);
    Route::resource('doctor-leaves', \App\Http\Controllers\DoctorLeaveController::class)->parameters(['doctor-leaves' => 'doctor_leaf'])->only(['store', 'destroy']);
    Route::resource('services', \App\Http\Controllers\ServiceController::class)->except(['create', 'show', 'edit']);
    Route::resource('invoices', \App\Http\Controllers\InvoiceController::class);
    Route::post('invoices/{invoice}/payments', [\App\Http\Controllers\PaymentController::class, 'store'])->name('payments.store');
    Route::delete('payments/{payment}', [\App\Http\Controllers\PaymentController::class, 'destroy'])->name('payments.destroy');
    Route::resource('radiology', \App\Http\Controllers\RadiologyRequestController::class)->except(['create', 'edit']);

    // Laboratory Module
    Route::resource('lab-requests', \App\Http\Controllers\LabRequestController::class)->except(['create', 'edit']);
    Route::post('lab-requests/{lab_request}/status', [\App\Http\Controllers\LabRequestController::class, 'updateStatus'])->name('lab-requests.update-status');
    Route::post('lab-requests/{lab_request}/results', [\App\Http\Controllers\LabRequestController::class, 'updateResults'])->name('lab-requests.update-results');
    Route::get('lab-requests/{lab_request}/print', [\App\Http\Controllers\LabRequestController::class, 'printReport'])->name('lab-requests.print');
    Route::resource('lab-tests', \App\Http\Controllers\LabTestController::class)->except(['create', 'show', 'edit']);

    // Pharmacy & Inventory Module
    Route::resource('medicines', \App\Http\Controllers\MedicineController::class)->except(['create', 'show', 'edit']);
    Route::post('medicines/{medicine}/adjust-stock', [\App\Http\Controllers\MedicineController::class, 'adjustStock'])->name('medicines.adjust-stock');
    Route::resource('prescriptions', \App\Http\Controllers\PrescriptionController::class)->except(['create', 'edit']);
    Route::post('prescriptions/{prescription}/dispense', [\App\Http\Controllers\PrescriptionController::class, 'dispense'])->name('prescriptions.dispense');
    Route::get('prescriptions/{prescription}/print', [\App\Http\Controllers\PrescriptionController::class, 'print'])->name('prescriptions.print');

    // Insurance & Claims Module
    Route::resource('insurance-companies', \App\Http\Controllers\InsuranceCompanyController::class)->except(['create', 'show', 'edit']);
    Route::resource('patient-insurances', \App\Http\Controllers\PatientInsuranceController::class)->only(['store', 'update', 'destroy']);
    Route::resource('insurance-claims', \App\Http\Controllers\InsuranceClaimController::class)->except(['create', 'edit']);
    Route::post('insurance-claims/{insurance_claim}/status', [\App\Http\Controllers\InsuranceClaimController::class, 'updateStatus'])->name('insurance-claims.update-status');
    Route::get('insurance-claims/{insurance_claim}/print', [\App\Http\Controllers\InsuranceClaimController::class, 'print'])->name('insurance-claims.print');
});

// Storage Delivery Route (ensures images load on shared hosting / cPanel if symlink is missing or blocked)
Route::get('storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath)) {
        abort(404);
    }
    return response()->file($filePath, [
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*')->name('storage.file');











