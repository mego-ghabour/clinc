<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\Medicine;
use App\Models\Patient;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemGuideTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $this->adminUser = User::factory()->create([
            'status' => 'نشط',
        ]);
        $this->adminUser->assignRole($role);
    }

    public function test_guest_cannot_view_system_guide()
    {
        $response = $this->get(route('guide.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_system_guide()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('guide.index'));

        $response->assertStatus(200);
        $response->assertSee('الدليل الشامل لاستخدام وتشغيل النظام');
        $response->assertSee('خريطة رحلة المريض ودورة العمل السريرية والإدارية');
        $response->assertSee('قسم المختبر والتحاليل الطبية');
        $response->assertSee('قسم الصيدلية وإدارة المخزون الدوائي');
        $response->assertSee('التأمين الطبي وإدارة المطالبات والشركات');
        $response->assertSee('المالية، الفواتير، الدفعات والضرائب');
        $response->assertSee('سجل التدقيق والرقابة');
        $response->assertSee('الأسئلة الشائعة وحلول المواقف اليومية');
    }

    public function test_system_guide_displays_system_metrics()
    {
        $this->actingAs($this->adminUser);

        Patient::factory()->create();
        Clinic::factory()->create();

        $response = $this->get(route('guide.index'));
        $response->assertStatus(200);
        $response->assertViewHas('systemStats');
    }

    public function test_can_download_system_guide_doc()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('guide.download.doc'));
        $response->assertStatus(200);
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.doc', $response->headers->get('Content-Disposition'));
    }
}
