<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class DoctorSchedule extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'doctor_id',
        'day_of_week',
        'shifts',
        'consultation_duration',
        'is_working'
    ];

    protected $casts = [
        'is_working' => 'boolean',
        'shifts' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}

