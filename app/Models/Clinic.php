<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Clinic extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = ['name', 'icon', 'color', 'code', 'status', 'notes', 'daily_quota', 'start_time', 'end_time', 'head_doctor_id', 'sort_order'];

    public function doctors()
    {
        return $this->hasMany(User::class); // assuming any user assigned to clinic is a doctor or staff
    }

    public function headDoctor()
    {
        return $this->belongsTo(User::class, 'head_doctor_id');
    }
}
