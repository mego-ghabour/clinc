<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'lab_request_id',
        'lab_test_id',
        'test_name',
        'result_value',
        'normal_range',
        'unit',
        'is_abnormal',
        'notes',
    ];

    protected $casts = [
        'is_abnormal' => 'boolean',
    ];

    public function labRequest()
    {
        return $this->belongsTo(LabRequest::class);
    }

    public function labTest()
    {
        return $this->belongsTo(LabTest::class);
    }
}
