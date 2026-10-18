<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_settings_page(): void
    {
        $this->authenticateSuperAdmin();

        $response = $this->get(route('settings.index'));

        $response->assertStatus(200);
        $response->assertSee('إعدادات');
    }

    public function test_can_update_settings(): void
    {
        $this->authenticateSuperAdmin();

        $response = $this->post(route('settings.update'), [
            'clinic_name' => 'مجمع الشفاء الطبي',
            'phone' => '0112345678',
            'patient_prefix' => 'PAT-',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('settings', [
            'key' => 'clinic_name',
            'value' => 'مجمع الشفاء الطبي',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'patient_prefix',
            'value' => 'PAT-',
        ]);
    }

    public function test_can_create_and_delete_backup(): void
    {
        $this->authenticateSuperAdmin();

        // Create backup
        $response = $this->post(route('settings.backups.create'), ['type' => 'db']);
        $response->assertRedirect();

        $backupDir = storage_path('app/backups');
        $files = File::files($backupDir);
        $this->assertNotEmpty($files);

        $filename = basename($files[0]);

        // Download backup
        $downloadResponse = $this->get(route('settings.backups.download', $filename));
        $downloadResponse->assertStatus(200);

        // Delete backup
        $deleteResponse = $this->delete(route('settings.backups.delete', $filename));
        $deleteResponse->assertRedirect();
        $this->assertFalse(File::exists($backupDir . '/' . $filename));
    }
}
