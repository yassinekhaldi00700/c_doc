<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\Department;
use App\Models\ResearchSubject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class BulkImportTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => UserRole::Admin, 'is_active' => true]);
    }

    private function csv(string $content, string $name = 'import.csv'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, $content);
    }

    public function test_admin_can_bulk_create_professors_sharing_one_password(): void
    {
        $department = Department::create(['name' => 'Computer Science', 'code' => 'CS']);
        $csv = "name,email,doctoral_program\nJane Doe,jane@example.com,CS\nJohn Roe,john@example.com,CS\n";

        $this->actingAs($this->admin())
            ->get(route('admin.users.bulk-create'))
            ->assertOk();

        $this->actingAs($this->admin())
            ->post(route('admin.users.bulk-store'), ['file' => $this->csv($csv), 'password' => 'test tets'])
            ->assertRedirect(route('admin.users.index'));

        $jane = User::where('email', 'jane@example.com')->sole();
        $john = User::where('email', 'john@example.com')->sole();

        $this->assertSame(UserRole::Professor, $jane->role);
        $this->assertSame($department->id, $jane->department_id);
        $this->assertTrue($jane->is_active);
        $this->assertNotNull($jane->email_verified_at);
        $this->assertNull($jane->password_changed_at);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('test tets', $jane->password));
        // Every account in the batch shares the same password.
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('test tets', $john->password));
    }

    public function test_bulk_professor_rows_with_problems_are_skipped_with_reasons_and_others_still_import(): void
    {
        $department = Department::create(['name' => 'Computer Science', 'code' => 'CS']);
        $existing = User::factory()->create(['email' => 'taken@example.com']);
        $csv = implode("\n", [
            'name,email,doctoral_program',
            'Valid Prof,valid@example.com,CS',
            'Missing Email,,CS',
            'Taken Email,taken@example.com,CS',
            'Unknown Program,unknown@example.com,ZZ',
        ])."\n";

        $response = $this->actingAs($this->admin())
            ->post(route('admin.users.bulk-store'), ['file' => $this->csv($csv), 'password' => 'test tets']);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('bulk_import_errors', fn ($errors) => count($errors) === 3);
        $this->assertTrue(User::where('email', 'valid@example.com')->exists());
        $this->assertFalse(User::where('email', 'unknown@example.com')->exists());
        $this->assertSame(1, User::where('email', 'taken@example.com')->count()); // unchanged: the pre-existing one only
    }

    public function test_only_an_admin_can_bulk_create_professors(): void
    {
        $professor = User::factory()->professor()->create(['is_active' => true]);
        $csv = "name,email,doctoral_program\nJane Doe,jane@example.com,CS\n";

        $this->actingAs($professor)
            ->get(route('admin.users.bulk-create'))
            ->assertForbidden();
        $this->actingAs($professor)
            ->post(route('admin.users.bulk-store'), ['file' => $this->csv($csv), 'password' => 'test tets'])
            ->assertForbidden();
    }

    public function test_admin_can_bulk_create_subjects_matched_to_a_professor_by_email(): void
    {
        $department = Department::create(['name' => 'Computer Science', 'code' => 'CS']);
        $professor = User::factory()->professor()->create(['email' => 'jane@example.com', 'is_active' => true]);
        $csv = "title,professor_email,doctoral_program\nStudy of Light,jane@example.com,CS\n";

        $this->actingAs($this->admin())
            ->get(route('admin.subjects.bulk-create'))
            ->assertOk();

        $this->actingAs($this->admin())
            ->post(route('admin.subjects.bulk-store'), ['file' => $this->csv($csv)])
            ->assertRedirect(route('admin.subjects.index'));

        $subject = ResearchSubject::sole();
        $this->assertSame('Study of Light', $subject->title);
        $this->assertSame($professor->id, $subject->professor_id);
        $this->assertSame($department->id, $subject->department_id);
        $this->assertFalse($subject->is_open);
        $this->assertStringContainsString('pending', $subject->description);
    }

    public function test_bulk_subject_rows_needing_an_unknown_professor_or_program_are_skipped(): void
    {
        $department = Department::create(['name' => 'Computer Science', 'code' => 'CS']);
        $professor = User::factory()->professor()->create(['email' => 'jane@example.com', 'is_active' => true]);
        $csv = implode("\n", [
            'title,professor_email,doctoral_program',
            'Valid Subject,jane@example.com,CS',
            'No Such Professor,ghost@example.com,CS',
            'No Such Program,jane@example.com,ZZ',
        ])."\n";

        $response = $this->actingAs($this->admin())
            ->post(route('admin.subjects.bulk-store'), ['file' => $this->csv($csv)]);

        $response->assertRedirect(route('admin.subjects.index'));
        $response->assertSessionHas('bulk_import_errors', fn ($errors) => count($errors) === 2);
        $this->assertSame(1, ResearchSubject::count());
    }
}
