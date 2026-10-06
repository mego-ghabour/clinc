<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Clinic extends Model
{
    use SoftDeletes;

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
