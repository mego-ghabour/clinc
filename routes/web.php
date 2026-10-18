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
Route::post('login', [AuthController::class, 'login'])->name('login.post');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return redirect()->route('login');
});

// Public Booking Portal
Route::get('book', [\App\Http\Controllers\PublicBookingController::class, 'index'])->name('booking.index');
Route::post('book', [\App\Http\Controllers\PublicBookingController::class, 'store'])->name('booking.store');
Route::get('api/public/clinics/{clinic}/doctors', [\App\Http\Controllers\PublicBookingController::class, 'getDoctors']);
Route::get('api/public/doctors/{doctor}/slots', [\App\Http\Controllers\PublicBookingController::class, 'getAvailableSlots']);

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/booking', function () {
        return view('booking');
    });

    // Patients API
    Route::get('/api/patients/search', [PatientController::class, 'search'])->name('patients.search');

    // Clinics
    Route::resource('clinics', ClinicController::class)->except(['create', 'edit']);
    Route::post('clinics/{clinic}/restore', [ClinicController::class, 'restore'])->name('clinics.restore');
    Route::post('clinics/{clinic}/assign-doctor', [ClinicController::class, 'assignDoctor'])->name('clinics.assign_doctor');

    // Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/backups/create', [SettingController::class, 'createBackup'])->name('settings.backups.create');
    Route::get('settings/backups/{filename}/download', [SettingController::class, 'downloadBackup'])->name('settings.backups.download');
    Route::delete('settings/backups/{filename}', [SettingController::class, 'deleteBackup'])->name('settings.backups.delete');

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

    // Roles & System Users
    Route::resource('roles', RoleController::class)->except(['create', 'show', 'edit']);
    Route::resource('system-users', SystemUserController::class)->except(['create', 'edit']);
    Route::post('system-users/{id}/permissions', [SystemUserController::class, 'updatePermissions'])->name('system_users.updatePermissions');
    Route::patch('system-users/{id}/toggle-status', [SystemUserController::class, 'toggleStatus'])->name('system_users.toggleStatus');

    // Impersonation Routes
    Route::impersonate();

    // Audit Logs
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit_logs.index');
    Route::post('audit-logs/{id}/rollback', [AuditLogController::class, 'rollback'])->name('audit_logs.rollback');

    Route::resource('visits', VisitController::class);

    // Doctor Schedules
    Route::resource('doctor-schedules', DoctorScheduleController::class)->only(['index', 'show', 'store']);
    Route::resource('doctor-leaves', \App\Http\Controllers\DoctorLeaveController::class)->parameters(['doctor-leaves' => 'doctor_leaf'])->only(['store', 'destroy']);
    Route::resource('services', \App\Http\Controllers\ServiceController::class)->except(['create', 'show', 'edit']);
    Route::resource('invoices', \App\Http\Controllers\InvoiceController::class);
    Route::post('invoices/{invoice}/payments', [\App\Http\Controllers\PaymentController::class, 'store'])->name('payments.store');
    Route::delete('payments/{payment}', [\App\Http\Controllers\PaymentController::class, 'destroy'])->name('payments.destroy');
    Route::resource('radiology', \App\Http\Controllers\RadiologyRequestController::class)->except(['create', 'edit']);
});











