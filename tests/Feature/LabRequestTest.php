<?php

namespace Tests\Feature;

use App\Models\LabRequest;
use App\Models\LabRequestItem;
use App\Models\LabTest;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LabRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_lab_requests(): void
    {
        $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create(['first_name' => 'سارة', 'family_name' => 'الغامدي']);
        $labRequest = LabRequest::create([
            'patient_id' => $patient->id,
            'priority' => 'عادي',
            'status' => 'معلق',
        ]);

        $response = $this->get(route('lab-requests.index'));

        $response->assertStatus(200);
        $response->assertSee('سارة الغامدي');
        $response->assertSee($labRequest->request_number);
    }

    public function test_can_filter_lab_requests_by_status(): void
    {
        $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create();
        
        $pendingReq = LabRequest::create([
            'patient_id' => $patient->id,
            'priority' => 'عادي',
            'status' => 'معلق',
        ]);
        $completedReq = LabRequest::create([
            'patient_id' => $patient->id,
            'priority' => 'عادي',
            'status' => 'مكتمل',
        ]);

        $response = $this->get(route('lab-requests.index', ['status' => 'معلق']));
        $response->assertStatus(200);
        $response->assertSee($pendingReq->request_number);
        $response->assertDontSee($completedReq->request_number);
    }

    public function test_can_create_lab_request_with_tests(): void
    {
        $user = $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create();
        $test1 = LabTest::create([
            'name' => 'CBC Complete Blood Count',
            'code' => 'CBC-01',
            'category' => 'أمراض الدم',
            'price' => 50,
            'is_active' => true,
        ]);
        $test2 = LabTest::create([
            'name' => 'Fasting Blood Glucose',
            'code' => 'FBG-01',
            'category' => 'كيمياء الدم',
            'price' => 30,
            'is_active' => true,
        ]);

        $response = $this->post(route('lab-requests.store'), [
            'patient_id' => $patient->id,
            'priority' => 'عاجل',
            'clinical_notes' => 'المريض صائم منذ 10 ساعات',
            'test_ids' => [$test1->id, $test2->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('lab_requests', [
            'patient_id' => $patient->id,
            'priority' => 'عاجل',
            'status' => 'معلق',
        ]);

        $labRequest = LabRequest::first();
        $this->assertEquals(2, $labRequest->items()->count());
        $this->assertDatabaseHas('lab_request_items', [
            'lab_request_id' => $labRequest->id,
            'test_name' => 'CBC Complete Blood Count',
        ]);
    }

    public function test_cannot_create_lab_request_without_tests(): void
    {
        $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create();

        $response = $this->post(route('lab-requests.store'), [
            'patient_id' => $patient->id,
            'priority' => 'عادي',
            'test_ids' => [],
        ]);

        $response->assertSessionHasErrors('test_ids');
        $this->assertEquals(0, LabRequest::count());
    }

    public function test_can_update_status_and_records_sample_timestamp(): void
    {
        $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create();
        $labRequest = LabRequest::create([
            'patient_id' => $patient->id,
            'priority' => 'عادي',
            'status' => 'معلق',
        ]);

        $response = $this->post(route('lab-requests.update-status', $labRequest->id), [
            'status' => 'تم سحب العينة',
        ]);

        $response->assertRedirect();
        $labRequest->refresh();
        $this->assertEquals('تم سحب العينة', $labRequest->status);
        $this->assertNotNull($labRequest->sample_collected_at);
    }

    public function test_technician_can_enter_results_and_finalize_lab_request(): void
    {
        $technician = $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create();
        $labRequest = LabRequest::create([
            'patient_id' => $patient->id,
            'priority' => 'عادي',
            'status' => 'قيد التحليل',
        ]);

        $item1 = LabRequestItem::create([
            'lab_request_id' => $labRequest->id,
            'test_name' => 'Fasting Glucose',
            'normal_range' => '70 - 100',
            'unit' => 'mg/dL',
        ]);

        $response = $this->post(route('lab-requests.update-results', $labRequest->id), [
            'action' => 'finalize',
            'items' => [
                $item1->id => [
                    'result_value' => '135',
                    'is_abnormal' => '1',
                    'notes' => 'سكر صائم مرتفع',
                ],
            ],
        ]);

        $response->assertRedirect();
        $labRequest->refresh();
        $item1->refresh();

        $this->assertEquals('مكتمل', $labRequest->status);
        $this->assertNotNull($labRequest->completed_at);
        $this->assertEquals($technician->id, $labRequest->technician_id);

        $this->assertEquals('135', $item1->result_value);
        $this->assertTrue((bool)$item1->is_abnormal);
        $this->assertEquals('سكر صائم مرتفع', $item1->notes);
    }

    public function test_can_view_printable_lab_report(): void
    {
        $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create(['first_name' => 'محمد', 'family_name' => 'العتيبي']);
        $labRequest = LabRequest::create([
            'patient_id' => $patient->id,
            'priority' => 'عادي',
            'status' => 'مكتمل',
        ]);
        LabRequestItem::create([
            'lab_request_id' => $labRequest->id,
            'test_name' => 'HbA1c السكر التراكمي',
            'result_value' => '6.2',
            'normal_range' => '4.5 - 5.7',
            'unit' => '%',
            'is_abnormal' => true,
        ]);

        $response = $this->get(route('lab-requests.print', $labRequest->id));

        $response->assertStatus(200);
        $response->assertSee('تقرير التحاليل الطبية');
        $response->assertSee('محمد العتيبي');
        $response->assertSee('HbA1c السكر التراكمي');
        $response->assertSee('6.2');
    }

    public function test_doctor_can_order_lab_request_from_visit(): void
    {
        $doctor = $this->authenticateDoctor();
        $patient = Patient::factory()->create();
        $visit = Visit::factory()->create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
        ]);
        $test = LabTest::create([
            'name' => 'Serum Creatinine',
            'code' => 'CREAT',
            'category' => 'وظائف الكلى',
            'price' => 45,
            'is_active' => true,
        ]);

        $response = $this->post(route('lab-requests.store'), [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'visit_id' => $visit->id,
            'from_visit' => '1',
            'priority' => 'عادي',
            'clinical_notes' => 'فحص وظائف الكلى قبل البدء بالعلاج',
            'test_ids' => [$test->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('lab_requests', [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'visit_id' => $visit->id,
        ]);
    }
}
