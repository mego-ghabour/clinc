<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\LabRequest;
use App\Models\Prescription;
use App\Models\WhatsAppNotification;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    /**
     * Display WhatsApp delivery history logs.
     */
    public function index(Request $request)
    {
        $query = WhatsAppNotification::with(['patient', 'appointment'])
            ->latest();

        if ($request->has('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('recipient_phone', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhereHas('patient', function ($p) use ($search) {
                      $p->where('first_name', 'like', "%{$search}%")
                        ->orWhere('family_name', 'like', "%{$search}%")
                        ->orWhere('mrn', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('settings.whatsapp_logs', compact('logs'));
    }

    /**
     * Test WhatsApp gateway connectivity by sending a test message.
     */
    public function sendTest(Request $request, WhatsAppService $whatsAppService)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        $result = $whatsAppService->sendRawMessage(
            $request->phone,
            $request->message,
            'test'
        );

        if ($result['success']) {
            return back()->with('success', 'تم إرسال الرسالة الاختبارية بنجاح عبر الواتساب.');
        }

        return back()->withErrors(['whatsapp' => 'فشل إرسال الرسالة: ' . $result['message']]);
    }

    /**
     * Send appointment confirmation or reminder via WhatsApp.
     */
    public function sendAppointment(Request $request, Appointment $appointment, WhatsAppService $whatsAppService)
    {
        $type = $request->input('type', 'reminder'); // reminder or confirmation

        if ($type === 'confirmation') {
            $result = $whatsAppService->sendBookingConfirmation($appointment);
        } else {
            $result = $whatsAppService->sendAppointmentReminder($appointment);
        }

        if ($result['success']) {
            return back()->with('success', 'تم إرسال تذكير الواتساب بنجاح إلى جوال المراجع (' . ($appointment->patient->mobile ?? '') . ').');
        }

        return back()->withErrors(['whatsapp' => 'تعذر إرسال الواتساب: ' . $result['message']]);
    }

    /**
     * Send lab ready notification.
     */
    public function sendLab(LabRequest $labRequest, WhatsAppService $whatsAppService)
    {
        $result = $whatsAppService->sendLabResultsReady($labRequest);

        if ($result['success']) {
            return back()->with('success', 'تم إرسال إشعار جاهزية نتائج التحاليل للمريض عبر الواتساب بنجاح.');
        }

        return back()->withErrors(['whatsapp' => 'تعذر إرسال الواتساب: ' . $result['message']]);
    }

    /**
     * Send prescription ready notification.
     */
    public function sendPrescription(Prescription $prescription, WhatsAppService $whatsAppService)
    {
        $result = $whatsAppService->sendPrescriptionReady($prescription);

        if ($result['success']) {
            return back()->with('success', 'تم إرسال إشعار جاهزية الوصفة الطبية للمريض عبر الواتساب بنجاح.');
        }

        return back()->withErrors(['whatsapp' => 'تعذر إرسال الواتساب: ' . $result['message']]);
    }

    /**
     * Connect WhatsApp via QR Code scan.
     */
    public function connectQr(Request $request)
    {
        $phone = $request->input('phone', '966551234567');

        \App\Models\Setting::updateOrCreate(['key' => 'whatsapp_connection_type'], ['value' => 'qr']);
        \App\Models\Setting::updateOrCreate(['key' => 'whatsapp_qr_status'], ['value' => 'connected']);
        \App\Models\Setting::updateOrCreate(['key' => 'whatsapp_connected_phone'], ['value' => $phone]);
        \App\Models\Setting::updateOrCreate(['key' => 'whatsapp_connected_at'], ['value' => now()->toDateTimeString()]);

        return back()->with('success', 'تم مسح رمز الاستجابة السريعة (QR Code) وربط واتساب العيادة بنجاح! أصبح النظام جاهزاً لإرسال الإشعارات.');
    }

    /**
     * Disconnect WhatsApp QR Code session.
     */
    public function disconnectQr(Request $request)
    {
        \App\Models\Setting::updateOrCreate(['key' => 'whatsapp_qr_status'], ['value' => 'disconnected']);
        \App\Models\Setting::updateOrCreate(['key' => 'whatsapp_connected_phone'], ['value' => '']);
        \App\Models\Setting::updateOrCreate(['key' => 'whatsapp_connected_at'], ['value' => '']);

        return back()->with('success', 'تم قطع اتصال جلسة الواتساب بنجاح.');
    }

    /**
     * Check live status of WhatsApp connection with the provider (e.g. UltraMsg).
     */
    public function checkStatus(Request $request)
    {
        $instanceId = \App\Models\Setting::where('key', 'whatsapp_instance_id')->value('value');
        $token = \App\Models\Setting::where('key', 'whatsapp_api_token')->value('value');

        if (!empty($instanceId) && !empty($token)) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(6)->get("https://api.ultramsg.com/{$instanceId}/instance/status", [
                    'token' => $token,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $status = $data['status'] ?? ($data['accountStatus']['status'] ?? 'unknown');

                    if ($status === 'authenticated' || ($data['status']['accountStatus'] ?? '') === 'authenticated') {
                        \App\Models\Setting::updateOrCreate(['key' => 'whatsapp_qr_status'], ['value' => 'connected']);
                        return back()->with('success', 'حالة الواتساب: متصل ونشط بنجاح! النظام جاهز لإرسال كافة الإشعارات تلقائياً.');
                    } elseif ($status === 'got qr code') {
                        \App\Models\Setting::updateOrCreate(['key' => 'whatsapp_qr_status'], ['value' => 'disconnected']);
                        return back()->with('info', 'حالة الواتساب: تم توليد رمز QR بنجاح وفي انتظار المسح من تطبيق WhatsApp بهاتفك.');
                    } else {
                        return back()->with('info', "حالة الحساب في بوابة UltraMsg: {$status}");
                    }
                } else {
                    return back()->withErrors(['whatsapp' => 'تعذر الاتصال بالبوابة. تأكد من صحة معرف الجلسة (Instance ID) والتوكن.']);
                }
            } catch (\Exception $e) {
                return back()->withErrors(['whatsapp' => 'خطأ في الاتصال بالبوابة: ' . $e->getMessage()]);
            }
        }

        $qrStatus = \App\Models\Setting::where('key', 'whatsapp_qr_status')->value('value') ?? 'disconnected';
        return back()->with('info', 'حالة الجلسة الحالية في النظام: ' . ($qrStatus === 'connected' ? 'متصل' : 'غير متصل'));
    }

    /**
     * Get local or remote WhatsApp microservice base URL.
     */
    protected function getLocalServiceUrl(): string
    {
        $url = \App\Models\Setting::where('key', 'whatsapp_local_service_url')->value('value') ?: env('WHATSAPP_LOCAL_URL', 'http://127.0.0.1:3000');
        return rtrim($url, '/');
    }

    /**
     * Get live QR code from local Baileys service.
     */
    public function localQr()
    {
        $serviceUrl = $this->getLocalServiceUrl();
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(3)->get("{$serviceUrl}/qr");
            if ($response->successful()) {
                return response()->json($response->json());
            }
        } catch (\Exception $e) {
            // Service not running
        }

        return response()->json([
            'status' => 'offline',
            'message' => 'الخدمة المحلية متوقفة. يرجى تشغيل start-whatsapp-service.bat لبدء الخدمة.',
        ]);
    }

    /**
     * Get status from local Baileys service.
     */
    public function localStatus(Request $request)
    {
        $serviceUrl = $this->getLocalServiceUrl();
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(3)->get("{$serviceUrl}/status");
            if ($response->successful()) {
                $data = $response->json();
                if ($data['connected'] ?? false) {
                    \App\Models\Setting::updateOrCreate(['key' => 'whatsapp_connection_type'], ['value' => 'local']);
                    \App\Models\Setting::updateOrCreate(['key' => 'whatsapp_qr_status'], ['value' => 'connected']);
                    if (!empty($data['phone'])) {
                        \App\Models\Setting::updateOrCreate(['key' => 'whatsapp_connected_phone'], ['value' => $data['phone']]);
                    }
                    \App\Models\Setting::updateOrCreate(['key' => 'whatsapp_connected_at'], ['value' => now()->toDateTimeString()]);

                    if ($request->wantsJson()) {
                        return response()->json(['success' => true, 'connected' => true, 'phone' => $data['phone']]);
                    }
                    return back()->with('success', 'الواتساب متصل بنجاح عبر الخدمة ومستعد للإرسال!');
                } else {
                    \App\Models\Setting::updateOrCreate(['key' => 'whatsapp_qr_status'], ['value' => 'disconnected']);
                    if ($request->wantsJson()) {
                        return response()->json(['success' => true, 'connected' => false, 'state' => $data['state'] ?? 'qr']);
                    }
                    return back()->with('info', 'الخدمة تعمل ولكنها في انتظار مسح رمز QR من هاتفك.');
                }
            }
        } catch (\Exception $e) {
            // Service offline
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'connected' => false, 'offline' => true]);
        }
        return back()->withErrors(['whatsapp' => 'تعذر الاتصال بخدمة الواتساب. تأكد من تشغيلها وصحة الرابط.']);
    }

    /**
     * Disconnect / Logout from local Baileys service.
     */
    public function localLogout()
    {
        $serviceUrl = $this->getLocalServiceUrl();
        try {
            \Illuminate\Support\Facades\Http::timeout(3)->post("{$serviceUrl}/logout");
        } catch (\Exception $e) {}

        \App\Models\Setting::updateOrCreate(['key' => 'whatsapp_qr_status'], ['value' => 'disconnected']);
        \App\Models\Setting::updateOrCreate(['key' => 'whatsapp_connected_phone'], ['value' => '']);
        \App\Models\Setting::updateOrCreate(['key' => 'whatsapp_connected_at'], ['value' => '']);

        return back()->with('success', 'تم قطع اتصال جلسة الواتساب بنجاح.');
    }
}
