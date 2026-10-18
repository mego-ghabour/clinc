<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_audit_logs(): void
    {
        $user = $this->authenticateSuperAdmin();

        activity()
            ->causedBy($user)
            ->event('created')
            ->log('تم تجربة تسجيل نشاط');

        $response = $this->get(route('audit_logs.index'));

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    public function test_can_rollback_updated_model_via_audit_log(): void
    {
        $user = $this->authenticateSuperAdmin();
        $patient = Patient::factory()->create([
            'first_name' => 'سالم',
            'family_name' => 'القديم',
        ]);

        $patient->update(['first_name' => 'سالم_معدل']);

        $activity = Activity::where('subject_type', Patient::class)
            ->where('subject_id', $patient->id)
            ->where('event', 'updated')
            ->latest()
            ->first();

        $this->assertNotNull($activity);

        $response = $this->post(route('audit_logs.rollback', $activity->id));

        $response->assertRedirect();
        $patient->refresh();
        $this->assertEquals('سالم', $patient->first_name);
    }
}
