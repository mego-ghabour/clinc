<?php

namespace Tests\Feature;

use App\Models\InsuranceClaim;
use App\Models\InsuranceCompany;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\PatientInsurance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InsuranceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_insurance_companies(): void
    {
        $this->authenticateSuperAdmin();
        $company = InsuranceCompany::create([
            'name' => 'شركة بوبا للتأمين',
            'code' => 'BUPA-TEST',
            'contact_person' => 'قسم الموافقات',
            'discount_percentage' => 15,
            'is_active' => true,
        ]);

        $response = $this->get(route('insurance-companies.index'));

        $response->assertStatus(200);
        $response->assertSee('شركة بوبا للتأمين');
        $response->assertSee('BUPA-TEST');
    }

    public function test_can_create_insurance_company(): void
    {
        $this->authenticateSuperAdmin();

        $response = $this->post(route('insurance-companies.store'), [
            'name' => 'التعاونية للتأمين الطبي',
            'code' => 'TAW-01',
            'contact_person' => 'مدير الحسابات',
            'phone' => '92000000',
            'discount_percentage' => 10,
            'is_active' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('insurance_companies', [
            'code' => 'TAW-01',
            'name' => 'التعاونية للتأمين الطبي',
        ]);
    }

    public function test_can_record_patient_insurance_policy(): void
    {
        $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create();
        $company = InsuranceCompany::create([
            'name' => 'ميدغلف للتأمين',
            'code' => 'MED-TEST',
            'is_active' => true,
        ]);

        $response = $this->post(route('patient-insurances.store'), [
            'patient_id' => $patient->id,
            'insurance_company_id' => $company->id,
            'policy_number' => 'POL-998877',
            'card_number' => 'CARD-123456',
            'class' => 'Class VIP',
            'copay_percentage' => 10.00,
            'max_copay_amount' => 50.00,
            'expiry_date' => now()->addYear()->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('patient_insurances', [
            'patient_id' => $patient->id,
            'policy_number' => 'POL-998877',
            'card_number' => 'CARD-123456',
            'copay_percentage' => 10.00,
        ]);
    }

    public function test_can_list_and_create_insurance_claim(): void
    {
        $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create();
        $company = InsuranceCompany::create([
            'name' => 'تكافل الراجحي',
            'code' => 'RAJ-TEST',
            'is_active' => true,
        ]);

        $response = $this->post(route('insurance-claims.store'), [
            'patient_id' => $patient->id,
            'insurance_company_id' => $company->id,
            'claim_date' => now()->format('Y-m-d'),
            'total_amount' => 500.00,
            'patient_copay' => 100.00,
            'insurance_share' => 400.00,
            'notes' => 'كشف استشاري وفحوصات',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('insurance_claims', [
            'patient_id' => $patient->id,
            'total_amount' => 500.00,
            'patient_copay' => 100.00,
            'insurance_share' => 400.00,
            'status' => 'معلقة',
        ]);

        $claim = InsuranceClaim::first();
        $this->assertNotNull($claim->claim_number);

        // List claims
        $listResponse = $this->get(route('insurance-claims.index'));
        $listResponse->assertStatus(200);
        $listResponse->assertSee($claim->claim_number);
    }

    public function test_can_approve_and_settle_insurance_claim(): void
    {
        $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create();
        $company = InsuranceCompany::create([
            'name' => 'بوبا',
            'code' => 'BUPA-02',
            'is_active' => true,
        ]);
        $claim = InsuranceClaim::create([
            'patient_id' => $patient->id,
            'insurance_company_id' => $company->id,
            'claim_date' => now(),
            'total_amount' => 300,
            'patient_copay' => 60,
            'insurance_share' => 240,
            'status' => 'معلقة',
        ]);

        // 1. Approve
        $approveResponse = $this->post(route('insurance-claims.update-status', $claim->id), [
            'status' => 'مقبولة',
        ]);
        $approveResponse->assertRedirect();
        $claim->refresh();
        $this->assertEquals('مقبولة', $claim->status);

        // 2. Settle (Paid by insurance company)
        $settleResponse = $this->post(route('insurance-claims.update-status', $claim->id), [
            'status' => 'مسددة',
        ]);
        $settleResponse->assertRedirect();
        $claim->refresh();
        $this->assertEquals('مسددة', $claim->status);
        $this->assertNotNull($claim->settled_at);
    }

    public function test_can_reject_claim_with_reason(): void
    {
        $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create();
        $company = InsuranceCompany::create([
            'name' => 'التعاونية',
            'code' => 'TAW-02',
            'is_active' => true,
        ]);
        $claim = InsuranceClaim::create([
            'patient_id' => $patient->id,
            'insurance_company_id' => $company->id,
            'claim_date' => now(),
            'total_amount' => 400,
            'patient_copay' => 80,
            'insurance_share' => 320,
            'status' => 'معلقة',
        ]);

        $response = $this->post(route('insurance-claims.update-status', $claim->id), [
            'status' => 'مرفوضة',
            'rejection_reason' => 'الخدمة غير مغطاة ضمن الباقة المحددة في الوثيقة',
        ]);

        $response->assertRedirect();
        $claim->refresh();
        $this->assertEquals('مرفوضة', $claim->status);
        $this->assertEquals('الخدمة غير مغطاة ضمن الباقة المحددة في الوثيقة', $claim->rejection_reason);
    }

    public function test_can_view_printable_insurance_claim(): void
    {
        $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create(['first_name' => 'سلطان', 'family_name' => 'القحطاني']);
        $company = InsuranceCompany::create([
            'name' => 'ميدغلف للرعاية',
            'code' => 'MED-03',
            'is_active' => true,
        ]);
        $claim = InsuranceClaim::create([
            'patient_id' => $patient->id,
            'insurance_company_id' => $company->id,
            'claim_date' => now(),
            'total_amount' => 450,
            'patient_copay' => 90,
            'insurance_share' => 360,
            'status' => 'مقبولة',
        ]);

        $response = $this->get(route('insurance-claims.print', $claim->id));

        $response->assertStatus(200);
        $response->assertSee('نموذج مطالبة تأمين طبي معتمد');
        $response->assertSee('سلطان القحطاني');
        $response->assertSee('ميدغلف للرعاية');
        $response->assertSee('360.00');
    }
}
