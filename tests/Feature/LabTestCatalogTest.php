<?php

namespace Tests\Feature;

use App\Models\LabTest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LabTestCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_lab_tests(): void
    {
        $this->authenticateSuperAdmin();
        $test = LabTest::create([
            'name' => 'Vitamin D 25-OH',
            'code' => 'VIT-D',
            'category' => 'فيتامينات',
            'price' => 120,
            'is_active' => true,
        ]);

        $response = $this->get(route('lab-tests.index'));

        $response->assertStatus(200);
        $response->assertSee('Vitamin D 25-OH');
        $response->assertSee('VIT-D');
    }

    public function test_can_create_new_lab_test(): void
    {
        $this->authenticateSuperAdmin();

        $response = $this->post(route('lab-tests.store'), [
            'name' => 'Thyroid Stimulating Hormone',
            'code' => 'TSH',
            'category' => 'هرمونات',
            'price' => 90.00,
            'sample_type' => 'مصل دم',
            'normal_range' => '0.4 - 4.0',
            'unit' => 'mIU/L',
            'is_active' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('lab_tests', [
            'code' => 'TSH',
            'name' => 'Thyroid Stimulating Hormone',
            'category' => 'هرمونات',
        ]);
    }

    public function test_can_update_lab_test(): void
    {
        $this->authenticateSuperAdmin();
        $test = LabTest::create([
            'name' => 'Lipid Profile',
            'code' => 'LIPID',
            'category' => 'كيمياء الدم',
            'price' => 100,
            'is_active' => true,
        ]);

        $response = $this->put(route('lab-tests.update', $test->id), [
            'name' => 'Lipid Profile Comprehensive',
            'code' => 'LIPID',
            'category' => 'كيمياء الدم',
            'price' => 150.00,
            'is_active' => '1',
        ]);

        $response->assertRedirect();
        $test->refresh();
        $this->assertEquals('Lipid Profile Comprehensive', $test->name);
        $this->assertEquals(150.00, (float)$test->price);
    }

    public function test_can_soft_delete_lab_test(): void
    {
        $this->authenticateSuperAdmin();
        $test = LabTest::create([
            'name' => 'Old Test',
            'code' => 'OLD-01',
            'category' => 'أخرى',
            'price' => 50,
            'is_active' => true,
        ]);

        $response = $this->delete(route('lab-tests.destroy', $test->id));

        $response->assertRedirect();
        $this->assertSoftDeleted('lab_tests', ['id' => $test->id]);
    }
}
