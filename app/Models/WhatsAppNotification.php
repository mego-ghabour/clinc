<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppNotification extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_notifications';

    protected $fillable = [
        'recipient_phone',
        'patient_id',
        'appointment_id',
        'type',
        'message',
        'status',
        'response_payload',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'sent' => '<span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">تم الإرسال</span>',
            'simulated' => '<span class="px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">محاكاة ناجحة</span>',
            'failed' => '<span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">فشل الإرسال</span>',
            default => '<span class="px-2 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800">' . $this->status . '</span>',
        };
    }
}
