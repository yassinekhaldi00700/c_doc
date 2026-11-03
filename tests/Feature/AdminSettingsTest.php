<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use App\Support\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    private function valid(array $overrides = []): array
    {
        return array_merge([
            'application_deadline' => '2026-11-20',
            'oral_exam_min_date' => '2026-12-01',
            'oral_exam_max_date' => '2026-12-05',
        ], $overrides);
    }

    public function test_default_oral_exam_period(): void
    {
        $this->assertSame('2026-11-09', SiteSettings::oralExamMinDate());
        $this->assertSame('2026-11-13', SiteSettings::oralExamMaxDate());
    }

    public function test_admin_can_update_all_dates(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin, 'is_active' => true]);

        $this->actingAs($admin)->get(route('admin.settings.edit'))->assertOk()->assertSee('Oral exam period');
        $this->actingAs($admin)->put(route('admin.settings.update'), $this->valid())
            ->assertRedirect(route('admin.settings.edit'));

        $this->assertSame('2026-11-20', SiteSettings::deadline()->format('Y-m-d'));
        $this->assertSame('2026-12-01', SiteSettings::oralExamMinDate());
        $this->assertSame('2026-12-05', SiteSettings::oralExamMaxDate());
    }

    public function test_end_of_period_cannot_precede_start(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin, 'is_active' => true]);

        $this->actingAs($admin)->put(route('admin.settings.update'), $this->valid(['oral_exam_max_date' => '2026-11-30']))
            ->assertSessionHasErrors('oral_exam_max_date');
        $this->assertSame('2026-11-13', SiteSettings::oralExamMaxDate());
    }

    public function test_non_admins_cannot_change_dates(): void
    {
        $professor = User::factory()->create(['role' => UserRole::Professor, 'is_active' => true]);

        $this->actingAs($professor)->put(route('admin.settings.update'), $this->valid())->assertForbidden();
        $this->assertSame('2026-11-09', SiteSettings::oralExamMinDate());
    }
}
