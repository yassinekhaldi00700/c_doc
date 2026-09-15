<?php

namespace Tests\Feature;

use App\Models\AdmissionSetting;
use App\Models\Department;
use App\Models\ResearchSubject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationAccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_pause_and_resume_all_applications(): void
    {
        $admin = User::factory()->admin()->create(['is_active' => true]);

        $this->actingAs($admin)
            ->patch(route('admin.subjects.toggle-application-access'))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertTrue(AdmissionSetting::applicationsArePaused());

        $this->actingAs($admin)
            ->patch(route('admin.subjects.toggle-application-access'))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertFalse(AdmissionSetting::applicationsArePaused());
    }

    public function test_paused_applications_keep_open_subject_visible_but_block_the_apply_routes(): void
    {
        $candidate = User::factory()->candidate()->create(['is_active' => true]);
        $subject = $this->createOpenSubject();

        AdmissionSetting::query()->first()->update(['applications_paused' => true]);

        $this->actingAs($candidate)
            ->get(route('candidate.subjects.show', $subject))
            ->assertOk()
            ->assertSee($subject->title)
            ->assertSee('Applications are temporarily paused.')
            ->assertDontSee('Apply for this Subject');

        $this->actingAs($candidate)
            ->get(route('candidate.applications.create', $subject))
            ->assertRedirect(route('candidate.subjects.show', $subject))
            ->assertSessionHas('warning');

        $this->actingAs($candidate)
            ->post(route('candidate.applications.store', $subject))
            ->assertRedirect(route('candidate.subjects.show', $subject))
            ->assertSessionHas('warning');

        $this->assertDatabaseCount('applications', 0);
    }

    private function createOpenSubject(): ResearchSubject
    {
        $department = Department::create([
            'name' => 'Engineering',
            'code' => 'ENG',
        ]);

        $professor = User::factory()->professor()->create([
            'department_id' => $department->id,
            'is_active' => true,
        ]);

        return ResearchSubject::create([
            'professor_id' => $professor->id,
            'department_id' => $department->id,
            'title' => 'Visible Open Research Subject',
            'description' => 'A research subject that remains visible during the global pause.',
            'is_open' => true,
        ]);
    }
}
