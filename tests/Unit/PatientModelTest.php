<?php

namespace Tests\Unit;

use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_name_and_full_name_accessors(): void
    {
        $patient = Patient::factory()->create([
            'first_name' => 'أحمد',
            'father_name' => 'محمد',
            'grandfather_name' => 'علي',
            'family_name' => 'الغامدي',
        ]);

        $this->assertEquals('أحمد الغامدي', $patient->name);
        $this->assertEquals('أحمد محمد علي الغامدي', $patient->full_name);
    }

    public function test_patient_age_string_accessor(): void
    {
        $dob = now()->subYears(25)->subMonths(3)->format('Y-m-d');
        $patient = Patient::factory()->create([
            'date_of_birth' => $dob,
        ]);

        $this->assertStringContainsString('سنة', $patient->age_string);
    }

    public function test_patient_mrn_is_automatically_generated(): void
    {
        $patient = Patient::factory()->create(['mrn' => null]);

        $this->assertNotNull($patient->mrn);
        $this->assertStringStartsWith('PAT-', $patient->mrn);
    }
}
