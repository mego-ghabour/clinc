<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_services(): void
    {
        $this->authenticateSuperAdmin();
        $service = Service::factory()->create(['name' => 'تنظيف أسنان']);

        $response = $this->get(route('services.index'));

        $response->assertStatus(200);
        $response->assertSee('تنظيف أسنان');
    }

    public function test_can_create_new_service(): void
    {
        $this->authenticateSuperAdmin();

        $response = $this->post(route('services.store'), [
            'name' => 'فحص نظر شامل',
            'price' => 150.00,
            'description' => 'فحص قياس النظر وفحص قاع العين',
            'is_active' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('services', [
            'name' => 'فحص نظر شامل',
            'price' => 150.00,
            'is_active' => 1,
        ]);
    }

    public function test_service_validation_rejects_negative_price(): void
    {
        $this->authenticateSuperAdmin();

        $response = $this->post(route('services.store'), [
            'name' => 'خدمة غير صحيحة',
            'price' => -20,
        ]);

        $response->assertSessionHasErrors('price');
    }

    public function test_can_update_service(): void
    {
        $this->authenticateSuperAdmin();
        $service = Service::factory()->create(['price' => 100.00]);

        $response = $this->put(route('services.update', $service->id), [
            'name' => 'سعر محدث للخدمة',
            'price' => 125.00,
            'description' => 'تحديث',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'price' => 125.00,
        ]);
    }

    public function test_can_delete_service(): void
    {
        $this->authenticateSuperAdmin();
        $service = Service::factory()->create();

        $response = $this->delete(route('services.destroy', $service->id));

        $response->assertRedirect();
        $this->assertSoftDeleted('services', ['id' => $service->id]);
    }
}
