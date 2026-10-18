<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Services\BackupService;

class SettingController extends Controller
{
    public function index(BackupService $backupService)
    {
        $settings = Setting::all()->keyBy('key');
        $backups = $backupService->getBackups();
        $dbDriver = config('database.default');

        return view('settings.index', compact('settings', 'backups', 'dbDriver'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        // Handle checkbox for backup_auto_enabled if not present in request
        if ($request->has('from_backup_tab')) {
            $data['backup_auto_enabled'] = $request->has('backup_auto_enabled') ? '1' : '0';
            unset($data['from_backup_tab']);
        }

        foreach ($data as $key => $value) {
            if ($request->hasFile($key)) {
                $path = $request->file($key)->store('settings', 'public');
                Setting::updateOrCreate(['key' => $key], ['value' => $path, 'type' => 'image']);
            } else {
                Setting::updateOrCreate(['key' => $key], ['value' => $value, 'type' => 'text']);
            }
        }

        return back()->with('success', 'تم حفظ الإعدادات بنجاح');
    }

    public function createBackup(Request $request, BackupService $backupService)
    {
        $type = $request->input('type', 'db'); // 'db' or 'full'
        $result = $backupService->createBackup($type);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->withErrors(['backup' => $result['message']]);
    }

    public function downloadBackup($filename, BackupService $backupService)
    {
        $path = $backupService->getBackupPath($filename);

        if (!$path) {
            return back()->withErrors(['backup' => 'الملف المطلوب غير موجود أو غير صالح.']);
        }

        return response()->download($path, $filename);
    }

    public function deleteBackup($filename, BackupService $backupService)
    {
        $deleted = $backupService->deleteBackup($filename);

        if ($deleted) {
            return back()->with('success', 'تم حذف النسخة الاحتياطية بنجاح.');
        }

        return back()->withErrors(['backup' => 'تعذر حذف النسخة الاحتياطية أو الملف غير موجود.']);
    }
}
