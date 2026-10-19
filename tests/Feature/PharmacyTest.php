<?php

namespace Tests\Feature;

use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PharmacyTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_medicines(): void
    {
        $this->authenticateSuperAdmin();
        $med = Medicine::create([
            'name' => 'Panadol Extra 500mg',
            'code' => 'MED-TEST-01',
            'category' => 'مسكنات',
            'dosage_form' => 'أقراص',
            'unit_price' => 20.00,
            'stock_quantity' => 100,
            'reorder_level' => 10,
            'is_active' => true,
        ]);

        $response = $this->get(route('medicines.index'));

        $response->assertStatus(200);
        $response->assertSee('Panadol Extra 500mg');
        $response->assertSee('MED-TEST-01');
    }

    public function test_can_create_new_medicine(): void
    {
        $this->authenticateSuperAdmin();

        $response = $this->post(route('medicines.store'), [
            'name' => 'Augmentin 1g',
            'code' => 'AUG-1000',
            'category' => 'مضادات حيوية',
            'dosage_form' => 'أقراص',
            'strength' => '1000mg',
            'unit_price' => 65.00,
            'cost_price' => 45.00,
            'stock_quantity' => 50,
            'reorder_level' => 10,
            'is_active' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('medicines', [
            'code' => 'AUG-1000',
            'name' => 'Augmentin 1g',
            'stock_quantity' => 50,
        ]);
        $this->assertDatabaseHas('inventory_transactions', [
            'type' => 'رصيد افتتاحي',
            'quantity' => 50,
        ]);
    }

    public function test_can_adjust_medicine_stock(): void
    {
        $this->authenticateSuperAdmin();
        $med = Medicine::create([
            'name' => 'Brufen 400mg',
            'code' => 'BRU-400',
            'category' => 'مسكنات',
            'dosage_form' => 'أقراص',
            'unit_price' => 22.00,
            'stock_quantity' => 40,
            'reorder_level' => 10,
            'is_active' => true,
        ]);

        $response = $this->post(route('medicines.adjust-stock', $med->id), [
            'type' => 'إضافة رصيد',
            'quantity' => 30,
            'notes' => 'شحنة توريد جديدة',
        ]);

        $response->assertRedirect();
        $med->refresh();
        $this->assertEquals(70, $med->stock_quantity);
        $this->assertDatabaseHas('inventory_transactions', [
            'medicine_id' => $med->id,
            'type' => 'إضافة رصيد',
            'quantity' => 30,
            'balance_after' => 70,
        ]);
    }

    public function test_doctor_can_create_prescription(): void
    {
        $doctor = $this->authenticateDoctor();
        $patient = Patient::factory()->create();
        $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
        $med = Medicine::create([
            'name' => 'Panadol',
            'code' => 'PAN-01',
            'category' => 'مسكنات',
            'dosage_form' => 'أقراص',
            'unit_price' => 15,
            'stock_quantity' => 50,
            'reorder_level' => 10,
            'is_active' => true,
        ]);

        $response = $this->post(route('prescriptions.store'), [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'visit_id' => $visit->id,
            'from_visit' => '1',
            'notes' => 'تناول الدواء بعد الأكل',
            'items' => [
                [
                    'medicine_id' => $med->id,
                    'medicine_name' => 'Panadol 500mg',
                    'dosage' => 'قرص واحد',
                    'frequency' => 'كل 8 ساعات',
                    'duration' => '3 أيام',
                    'quantity' => 1,
                    'instructions' => 'عند اللزوم',
                ],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('prescriptions', [
            'patient_id' => $patient->id,
            'visit_id' => $visit->id,
            'status' => 'معلقة',
        ]);
        $this->assertDatabaseHas('prescription_items', [
            'medicine_id' => $med->id,
            'quantity' => 1,
        ]);
    }

    public function test_pharmacist_can_dispense_prescription_and_stock_is_deducted(): void
    {
        $pharmacist = $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create();
        $med = Medicine::create([
            'name' => 'Amoxicillin 500mg',
            'code' => 'AMOX-500',
            'category' => 'مضادات',
            'dosage_form' => 'كبسولات',
            'unit_price' => 30,
            'stock_quantity' => 20,
            'reorder_level' => 5,
            'is_active' => true,
        ]);

        $prescription = Prescription::create([
            'patient_id' => $patient->id,
            'status' => 'معلقة',
        ]);

        $item = PrescriptionItem::create([
            'prescription_id' => $prescription->id,
            'medicine_id' => $med->id,
            'medicine_name' => 'Amoxicillin 500mg',
            'quantity' => 2,
            'dosage' => 'كبسولة كل 8 ساعات',
            'is_dispensed' => false,
        ]);

        $response = $this->post(route('prescriptions.dispense', $prescription->id));

        $response->assertRedirect();
        $prescription->refresh();
        $med->refresh();
        $item->refresh();

        $this->assertEquals('مصروفة', $prescription->status);
        $this->assertNotNull($prescription->dispensed_at);
        $this->assertEquals($pharmacist->id, $prescription->pharmacist_id);
        $this->assertTrue((bool)$item->is_dispensed);
        $this->assertEquals(18, $med->stock_quantity); // 20 - 2 = 18

        $this->assertDatabaseHas('inventory_transactions', [
            'medicine_id' => $med->id,
            'type' => 'صرف وصفة',
            'quantity' => -2,
            'balance_after' => 18,
        ]);
    }

    public function test_can_view_printable_prescription(): void
    {
        $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create(['first_name' => 'فهد', 'family_name' => 'الشمري']);
        $prescription = Prescription::create([
            'patient_id' => $patient->id,
            'status' => 'مصروفة',
        ]);
        PrescriptionItem::create([
            'prescription_id' => $prescription->id,
            'medicine_name' => 'Cataflam 50mg',
            'dosage' => 'قرص بعد الأكل',
            'quantity' => 1,
        ]);

        $response = $this->get(route('prescriptions.print', $prescription->id));

        $response->assertStatus(200);
        $response->assertSee('فهد الشمري');
        $response->assertSee('Cataflam 50mg');
        $response->assertSee('℞');
    }
}
