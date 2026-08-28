<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilePrivacyConsentTest extends TestCase
{
    use RefreshDatabase;

    public function test_both_profile_steps_show_the_cndp_consent(): void
    {
        $user = User::factory()->candidate()->create(['is_active' => true]);

        foreach (['candidate.profile.personal.edit', 'candidate.profile.academic.edit'] as $route) {
            $this->actingAs($user)
                ->get(route($route))
                ->assertOk()
                ->assertSee('I consent to the processing of my personal data.')
                ->assertSee('A-GS-836/2022')
                ->assertSee('T-HB-331/2022');
        }
    }

    public function test_a_profile_document_is_not_saved_without_consent(): void
    {
        Storage::fake('local');
        $user = User::factory()->candidate()->create(['is_active' => true]);

        $this->actingAs($user)
            ->post(route('candidate.profile.personal.update'), [
                'documents' => [
                    'cin_passport' => UploadedFile::fake()->create('identity.pdf', 100, 'application/pdf'),
                ],
            ])
            ->assertSessionHasErrors('privacy_consent');

        $this->assertDatabaseCount('profile_documents', 0);
    }
}
