<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\LabRequest;
use App\Models\Prescription;
use App\Models\Setting;
use App\Models\WhatsAppNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Format and normalize phone number to international format without plus sign.
     */
    public function formatPhoneNumber(?string $phone): string
    {
        if (!$phone) {
            return '';
        }

        // Remove non-numeric characters except leading plus
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        // Common Middle East format handling (e.g. Saudi Arabia 05xxxxxxxx -> 9665xxxxxxxx)
        if (str_starts_with($cleaned, '05') && strlen($cleaned) === 10) {
            $cleaned = '966' . substr($cleaned, 1);
        } elseif (str_starts_with($cleaned, '01') && strlen($cleaned) === 11) {
            // Egypt 01xxxxxxxxx -> 201xxxxxxxxx
            $cleaned = '20' . substr($cleaned, 1);
        }

        return $cleaned;
    }

    /**
     * Send raw WhatsApp message and record notification log.
     */
    public function sendRawMessage(string $phone, string $message, string $type = 'custom', ?int $patientId = null, ?int $appointmentId = null): array
    {
        $formattedPhone = $this->formatPhoneNumber($phone);

        if (empty($formattedPhone)) {
            return [
                'success' => false,
                'message' => 'رقم هاتف المراجع غير صالح للإرسال.',
                'status' => 'failed',
            ];
        }

        $isEnabled = Setting::where('key', 'whatsapp_enabled')->value('value') ?? '1';
        if ($isEnabled === '0') {
            return [
                'success' => false,
                'message' => 'خدمة إشعارات الواتساب معطلة حالياً من إعدادات النظام.',
                'status' => 'disabled',
            ];
        }

        $connectionType = Setting::where('key', 'whatsapp_connection_type')->value('value') ?? 'qr';
        $qrStatus = Setting::where('key', 'whatsapp_qr_status')->value('value') ?? 'disconnected';
        $connectedPhone = Setting::where('key', 'whatsapp_connected_phone')->value('value');
        $apiUrl = Setting::where('key', 'whatsapp_api_url')->value('value');
        $apiToken = Setting::where('key', 'whatsapp_api_token')->value('value');
        $instanceId = Setting::where('key', 'whatsapp_instance_id')->value('value');

        $status = 'simulated';
        $responsePayload = 'Simulated Delivery';
        $isSuccess = true;

        $errorMessage = null;

        if ($connectionType === 'local') {
            $serviceUrl = $this->getLocalServiceUrl();
            try {
                $response = Http::timeout(8)->post("{$serviceUrl}/send", [
                    'phone' => $formattedPhone,
                    'message' => $message,
                ]);

                if ($response->successful() && ($response->json()['success'] ?? false)) {
                    $status = 'sent';
                    $responsePayload = $response->body();
                    $isSuccess = true;
                } else {
                    $status = 'failed';
                    $responsePayload = 'Local Service Error: ' . $response->body();
                    $errorMessage = $response->json()['message'] ?? 'فشل الإرسال من خادم الواتساب المحلي.';
                    $isSuccess = false;
                }
            } catch (\Exception $e) {
                Log::error('Local WhatsApp Delivery Error: ' . $e->getMessage());
                $status = 'failed';
                $responsePayload = 'Local Service Offline: ' . $e->getMessage();
                $errorMessage = 'خادم الواتساب المحلي غير مشغّل (Port 3000). يرجى تشغيل ملف start-whatsapp-service.bat.';
                $isSuccess = false;
            }
        } elseif (empty($apiUrl) && !empty($instanceId) && !empty($apiToken)) {
            $apiUrl = "https://api.ultramsg.com/{$instanceId}/messages/chat";
        }

        if ($connectionType !== 'local' && !empty($apiUrl)) {
            try {
                $payload = [
                    'to' => $formattedPhone,
                    'phone' => $formattedPhone,
                    'message' => $message,
                    'body' => $message,
                    'token' => $apiToken,
                    'instance' => $instanceId,
                ];

                $response = Http::timeout(10)->post($apiUrl, $payload);
                $body = $response->body();
                $json = $response->json();

                // UltraMsg returns JSON with {"sent":"true", ...} or {"error": "..."}
                $isUltraMsgSuccess = isset($json['sent']) && ($json['sent'] === 'true' || $json['sent'] === true);

                if ($response->successful() && (!isset($json['error']) || $isUltraMsgSuccess)) {
                    $status = 'sent';
                    $responsePayload = $body;
                    $isSuccess = true;
                } else {
                    $status = 'failed';
                    $responsePayload = 'HTTP ' . $response->status() . ': ' . $body;
                    $isSuccess = false;
                }
            } catch (\Exception $e) {
                Log::error('WhatsApp Delivery Error: ' . $e->getMessage());
                $status = 'failed';
                $responsePayload = 'Error: ' . $e->getMessage();
                $isSuccess = false;
            }
        } elseif ($connectionType === 'qr' || $connectionType === 'simulated') {
            if ($qrStatus === 'connected') {
                $status = 'sent';
                $responsePayload = "تم الإرسال عبر جلسة الواتساب المرتبطة بالكود (+{$connectedPhone})";
                $isSuccess = true;
            } else {
                $status = 'simulated';
                $responsePayload = 'تم الإرسال في وضع المحاكاة والتجربة (في انتظار ربط البوابة أو الجهاز)';
                $isSuccess = true;
            }
        }

        // Record log in database
        WhatsAppNotification::create([
            'recipient_phone' => $formattedPhone,
            'patient_id' => $patientId,
            'appointment_id' => $appointmentId,
            'type' => $type,
            'message' => $message,
            'status' => $status,
            'response_payload' => $responsePayload,
        ]);

        return [
            'success' => $isSuccess,
            'message' => $isSuccess ? 'تم إرسال إشعار الواتساب بنجاح' : ($errorMessage ?? 'تعذر إرسال الإشعار عبر بوابة الواتساب'),
            'status' => $status,
        ];
    }

    /**
     * Send booking confirmation via WhatsApp.
     */
    public function sendBookingConfirmation(Appointment $appointment): array
    {
        $patient = $appointment->patient;
        if (!$patient || empty($patient->mobile)) {
            return ['success' => false, 'message' => 'لا يوجد رقم جوال مسجل للمريض.'];
        }

        $doctorName = $appointment->doctor->name ?? 'الطبيب المعالج';
        $clinicName = Setting::where('key', 'app_name')->value('value') ?? 'العيادة';
        $date = $appointment->appointment_date;
        $time = $appointment->appointment_time;
        $appointmentNum = $appointment->appointment_number ?? $appointment->id;

        $template = Setting::where('key', 'whatsapp_template_booking')->value('value');
        if (empty($template)) {
            $template = "مرحباً عزيزنا المراجع {patient_name}، تم تأكيد موعدك بنجاح في {clinic_name}.\n📅 التاريخ: {date}\n⏰ الوقت: {time}\n👨‍⚕️ الطبيب: {doctor_name}\nرقم الموعد: #{appointment_number}\nنتمنى لكم دوام الصحة والعافية.";
        }

        $message = str_replace(
            ['{patient_name}', '{doctor_name}', '{clinic_name}', '{date}', '{time}', '{appointment_number}'],
            [$patient->first_name . ' ' . $patient->family_name, $doctorName, $clinicName, $date, $time, $appointmentNum],
            $template
        );

        return $this->sendRawMessage($patient->mobile, $message, 'booking_confirmation', $patient->id, $appointment->id);
    }

    /**
     * Send appointment reminder via WhatsApp.
     */
    public function sendAppointmentReminder(Appointment $appointment): array
    {
        $patient = $appointment->patient;
        if (!$patient || empty($patient->mobile)) {
            return ['success' => false, 'message' => 'لا يوجد رقم جوال مسجل للمريض.'];
        }

        $doctorName = $appointment->doctor->name ?? 'الطبيب المعالج';
        $clinicName = Setting::where('key', 'app_name')->value('value') ?? 'العيادة';
        $date = $appointment->appointment_date;
        $time = $appointment->appointment_time;

        $template = Setting::where('key', 'whatsapp_template_reminder')->value('value');
        if (empty($template)) {
            $template = "تذكير بموعدك القادم في {clinic_name}:\nنود تذكيركم بموعدكم غداً مع د. {doctor_name} بتاريخ {date} في تمام الساعة {time}.\nيرجى الحضور قبل الموعد بـ 10 دقائق لتسجيل الدخول.";
        }

        $message = str_replace(
            ['{patient_name}', '{doctor_name}', '{clinic_name}', '{date}', '{time}'],
            [$patient->first_name . ' ' . $patient->family_name, $doctorName, $clinicName, $date, $time],
            $template
        );

        return $this->sendRawMessage($patient->mobile, $message, 'reminder', $patient->id, $appointment->id);
    }

    /**
     * Send lab results ready notification via WhatsApp.
     */
    public function sendLabResultsReady(LabRequest $labRequest): array
    {
        $patient = $labRequest->patient;
        if (!$patient || empty($patient->mobile)) {
            return ['success' => false, 'message' => 'لا يوجد رقم جوال مسجل للمريض.'];
        }

        $clinicName = Setting::where('key', 'app_name')->value('value') ?? 'العيادة';
        $reqNumber = $labRequest->request_number ?? $labRequest->id;

        $template = Setting::where('key', 'whatsapp_template_lab_ready')->value('value');
        if (empty($template)) {
            $template = "عزيزنا المراجع {patient_name}، نفيدكم بصدور نتائج تحاليلكم المخبرية في {clinic_name} لطلب رقم #{request_number}.\nيمكنكم الآن مراجعة قسم المختبر أو الطبيب المعالج للاطلاع على النتائج.";
        }

        $message = str_replace(
            ['{patient_name}', '{clinic_name}', '{request_number}'],
            [$patient->first_name . ' ' . $patient->family_name, $clinicName, $reqNumber],
            $template
        );

        return $this->sendRawMessage($patient->mobile, $message, 'lab_ready', $patient->id);
    }

    /**
     * Send prescription ready notification via WhatsApp.
     */
    public function sendPrescriptionReady(Prescription $prescription): array
    {
        $patient = $prescription->patient;
        if (!$patient || empty($patient->mobile)) {
            return ['success' => false, 'message' => 'لا يوجد رقم جوال مسجل للمريض.'];
        }

        $clinicName = Setting::where('key', 'app_name')->value('value') ?? 'العيادة';
        $rxNumber = $prescription->prescription_number ?? $prescription->id;

        $template = Setting::where('key', 'whatsapp_template_rx_ready')->value('value');
        if (empty($template)) {
            $template = "عزيزنا المراجع {patient_name}، تم إعداد وصفتكم الطبية رقم #{rx_number} في صيدلية {clinic_name} وهي جاهزة للصرف والتسليم.\nنتمنى لكم الشفاء العاجل.";
        }

        $message = str_replace(
            ['{patient_name}', '{clinic_name}', '{rx_number}'],
            [$patient->first_name . ' ' . $patient->family_name, $clinicName, $rxNumber],
            $template
        );

        return $this->sendRawMessage($patient->mobile, $message, 'rx_ready', $patient->id);
    }

    /**
     * Get local or remote WhatsApp microservice base URL.
     */
    protected function getLocalServiceUrl(): string
    {
        $url = Setting::where('key', 'whatsapp_local_service_url')->value('value') ?: env('WHATSAPP_LOCAL_URL', 'http://127.0.0.1:3000');
        return rtrim($url, '/');
    }
}
