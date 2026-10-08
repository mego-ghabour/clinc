<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Patient extends Model
{
    use SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    
    protected $fillable = [
        'mrn', 'avatar', 'first_name', 'father_name', 'grandfather_name', 'family_name', 
        'gender', 'date_of_birth', 'id_type', 'id_number', 'mobile', 
        'nationality', 'city', 'district', 'blood_type', 'emergency_contact', 'notes'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($patient) {
            if (empty($patient->mrn)) {
                // Generate a unique MRN like MRN-000001
                $lastPatient = self::withTrashed()->orderBy('id', 'desc')->first();
                $nextId = $lastPatient ? $lastPatient->id + 1 : 1;
                $patient->mrn = 'MRN-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->father_name} {$this->grandfather_name} {$this->family_name}");
    }

    public function getNameAttribute()
    {
        return trim("{$this->first_name} {$this->family_name}");
    }

    public function getAgeStringAttribute()
    {
        if (!$this->date_of_birth) return 'العمر غير مسجل';
        $dob = \Carbon\Carbon::parse($this->date_of_birth);
        $now = \Carbon\Carbon::now();
        $diff = $dob->diff($now);
        
        $ageStr = [];
        if ($diff->y > 0) $ageStr[] = $diff->y . ' سنة';
        if ($diff->m > 0) $ageStr[] = $diff->m . ' شهر';
        
        if (empty($ageStr) && $diff->d > 0) {
            return $diff->d . ' يوم';
        }
        
        return empty($ageStr) ? 'حديث الولادة' : implode(' و ', $ageStr);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }
}
