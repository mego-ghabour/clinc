<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class InsuranceClaim extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'claim_number',
        'invoice_id',
        'patient_id',
        'insurance_company_id',
        'doctor_id',
        'claim_date',
        'total_amount',
        'patient_copay',
        'insurance_share',
        'status',
        'rejection_reason',
        'notes',
        'settled_at',
    ];

    protected $casts = [
        'claim_date' => 'date',
        'total_amount' => 'decimal:2',
        'patient_copay' => 'decimal:2',
        'insurance_share' => 'decimal:2',
        'settled_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->claim_number)) {
                $lastId = self::withTrashed()->orderBy('id', 'desc')->first()?->id ?? 0;
                $model->claim_number = 'CLM-' . date('Y') . '-' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function insuranceCompany()
    {
        return $this->belongsTo(InsuranceCompany::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
