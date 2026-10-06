<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'mrn', 'first_name', 'father_name', 'grandfather_name', 'family_name', 
        'gender', 'date_of_birth', 'id_type', 'id_number', 'mobile', 
        'nationality', 'city', 'district', 'blood_type', 'emergency_contact'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($patient) {
            if (empty($patient->mrn)) {
                // Generate a unique MRN like MRN-000001
                $lastPatient = self::orderBy('id', 'desc')->first();
                $nextId = $lastPatient ? $lastPatient->id + 1 : 1;
                $patient->mrn = 'MRN-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->father_name} {$this->grandfather_name} {$this->family_name}");
    }
}
