<?php

namespace Tests\Feature;

use App\Http\Requests\Professor\OralExamPicksRequest;
use App\Models\Application;
use App\Models\Department;
use App\Models\ResearchSubject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OralExamPicksTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(int $applicationCount = 6): array
    {
        $professor = User::factory()->professor()->create(['is_active' => true]);
        $department = Department::create(['name' => 'Sciences', 'code' => 'SCI']);
        $subject = ResearchSubject::create([
            'professor_id' => $professor->id, 'department_id' => $department->id,
            'title' => 'Photonics', 'description' => 'Study of light.', 'is_open' => true,
        ]);
        $applications = collect(range(1, $applicationCount))->map(
            fn () => Application::create(['subject_id' => $subject->id, 'candidate_id' => User::factory()->create()->id, 'status' => 'pending'])
        );

        return [$professor, $subject, $applications];
    }

    /** Builds a valid `dates` payload (within the allowed window) for the given application ids. */
    private function datesFor(iterable $ids): array
    {
        return collect($ids)->mapWithKeys(fn ($id) => [$id => OralExamPicksRequest::MIN_DATE.'T09:00'])->all();
    }

    public function test_professor_reaches_a_subjects_picks_directly_from_the_index(): void
    {
        [$professor, $subject] = $this->fixture(1);
        $this->actingAs($professor)
            ->get(route('professor.oral-exam-picks.index'))
            ->assertOk()
            ->assertSee($subject->title)
            ->assertSee(route('professor.oral-exam-picks.edit', $subject), false);
    }

    public function test_professor_can_pick_and_replace_up_to_5_candidates(): void
    {
        [$professor, $subject, $applications] = $this->fixture();
        $this->actingAs($professor);

        $this->get(route('professor.oral-exam-picks.edit', $subject))->assertOk()->assertSee('Oral Exam Picks');

        $firstPicks = $applications->take(5)->pluck('id')->all();
        $this->post(route('professor.oral-exam-picks.update', $subject), [
            'application_ids' => $firstPicks,
            'dates' => $this->datesFor($firstPicks),
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertSame(5, Application::whereIn('id', $firstPicks)->whereNotNull('professor_favorited_at')->count());
        $this->assertSame(OralExamPicksRequest::MIN_DATE.'T09:00', Application::find($firstPicks[0])->professor_proposed_exam_at->format('Y-m-d\TH:i'));
        $this->assertSame(0, Application::where('id', $applications->last()->id)->whereNotNull('professor_favorited_at')->count());

        // Replacing the selection clears the previous picks, not just adds to them.
        $newPicks = [$applications->last()->id];
        $this->post(route('professor.oral-exam-picks.update', $subject), [
            'application_ids' => $newPicks,
            'dates' => $this->datesFor($newPicks),
        ])->assertSessionHasNoErrors();
        $this->assertSame(1, Application::whereNotNull('professor_favorited_at')->count());
        $this->assertNotNull(Application::find($applications->last()->id)->professor_favorited_at);
    }

    public function test_more_than_5_picks_are_rejected(): void
    {
        [$professor, $subject, $applications] = $this->fixture();
        $this->actingAs($professor);

        $ids = $applications->pluck('id')->all();
        $this->post(route('professor.oral-exam-picks.update', $subject), [
            'application_ids' => $ids,
            'dates' => $this->datesFor($ids),
        ])->assertSessionHasErrors('application_ids');
        $this->assertSame(0, Application::whereNotNull('professor_favorited_at')->count());
    }

    public function test_a_pick_needs_a_date_and_time_within_the_allowed_window(): void
    {
        [$professor, $subject, $applications] = $this->fixture(1);
        $this->actingAs($professor);
        $id = $applications->first()->id;

        $this->post(route('professor.oral-exam-picks.update', $subject), ['application_ids' => [$id]])
            ->assertSessionHasErrors("dates.$id");

        // Before the window, after the window, and the instant the window closes (exclusive).
        $this->post(route('professor.oral-exam-picks.update', $subject), ['application_ids' => [$id], 'dates' => [$id => '2026-09-20T23:59']])
            ->assertSessionHasErrors("dates.$id");
        $this->post(route('professor.oral-exam-picks.update', $subject), ['application_ids' => [$id], 'dates' => [$id => '2026-09-26T00:00']])
            ->assertSessionHasErrors("dates.$id");

        // Any time on the last day is still valid — the window is inclusive of the whole day.
        $this->post(route('professor.oral-exam-picks.update', $subject), ['application_ids' => [$id], 'dates' => [$id => '2026-09-25T23:00']])
            ->assertSessionHasNoErrors();

        $this->post(route('professor.oral-exam-picks.update', $subject), ['application_ids' => [$id], 'dates' => [$id => '2026-09-23T14:30']])
            ->assertSessionHasNoErrors();
        $this->assertSame('2026-09-23T14:30', Application::find($id)->professor_proposed_exam_at->format('Y-m-d\TH:i'));
    }

    public function test_a_candidate_from_another_subject_cannot_be_picked(): void
    {
        [$professor, $subject] = $this->fixture(1);
        $otherSubject = ResearchSubject::create([
            'professor_id' => $professor->id, 'department_id' => Department::first()->id,
            'title' => 'Other', 'description' => 'Other', 'is_open' => true,
        ]);
        $foreign = Application::create(['subject_id' => $otherSubject->id, 'candidate_id' => User::factory()->create()->id, 'status' => 'pending']);
        $this->actingAs($professor);

        $this->post(route('professor.oral-exam-picks.update', $subject), [
            'application_ids' => [$foreign->id],
            'dates' => $this->datesFor([$foreign->id]),
        ])->assertSessionHasErrors('application_ids.0');
    }

    public function test_only_the_owning_professor_can_pick(): void
    {
        [, $subject, $applications] = $this->fixture(1);
        $other = User::factory()->professor()->create(['is_active' => true]);
        $this->actingAs($other);

        $ids = $applications->pluck('id')->all();
        $this->get(route('professor.oral-exam-picks.edit', $subject))->assertForbidden();
        $this->post(route('professor.oral-exam-picks.update', $subject), [
            'application_ids' => $ids,
            'dates' => $this->datesFor($ids),
        ])->assertForbidden();
    }

    public function test_picking_does_not_change_application_status_and_admin_sees_it(): void
    {
        [$professor, $subject, $applications] = $this->fixture(1);
        $this->actingAs($professor);
        $id = $applications->first()->id;
        $this->post(route('professor.oral-exam-picks.update', $subject), [
            'application_ids' => [$id],
            'dates' => [$id => '2026-09-22T15:30'],
        ]);

        $this->assertSame('pending', $applications->first()->fresh()->status->value);

        $admin = User::factory()->create(['role' => \App\Enums\UserRole::Admin, 'is_active' => true]);
        $this->actingAs($admin)
            ->get(route('admin.subjects.show', $subject))
            ->assertOk()
            ->assertSee("Professor's pick", false);
        $this->actingAs($admin)
            ->get(route('admin.applications.show', $applications->first()))
            ->assertOk()
            ->assertSee('proposed', false)
            ->assertSee('22 Sep 2026, 15:30', false);
    }
}
