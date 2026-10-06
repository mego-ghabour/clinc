<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::post('/', function (\Illuminate\Http\Request $request) {
    // Here we can later add actual authentication logic
    $username = $request->input('username');
    
    // For now, redirect to dashboard
    return redirect('/dashboard')->with('username', $username);
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/booking', function () {
    return view('booking');
});

Route::resource('clinics', \App\Http\Controllers\ClinicController::class)->except(['create', 'edit']);
Route::post('clinics/{clinic}/restore', [\App\Http\Controllers\ClinicController::class, 'restore'])->name('clinics.restore');
Route::post('clinics/{clinic}/assign-doctor', [\App\Http\Controllers\ClinicController::class, 'assignDoctor'])->name('clinics.assign_doctor');
Route::get('settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
Route::post('settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
Route::resource('patients', \App\Http\Controllers\PatientController::class)->only(['index', 'store', 'show']);
Route::resource('users', \App\Http\Controllers\UserController::class)->only(['index', 'store']);
Route::resource('appointments', \App\Http\Controllers\AppointmentController::class)->only(['index', 'store']);
Route::resource('doctors', \App\Http\Controllers\DoctorController::class)->only(['index', 'store', 'show', 'update']);
Route::post('doctors/{id}/avatar', [\App\Http\Controllers\DoctorController::class, 'updateAvatar'])->name('doctors.avatar');
Route::post('doctors/{id}/toggle-status', [\App\Http\Controllers\DoctorController::class, 'toggleStatus'])->name('doctors.toggle_status');
Route::resource('doctor-schedules', \App\Http\Controllers\DoctorScheduleController::class)->only(['index', 'show']);
