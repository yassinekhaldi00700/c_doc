<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Department;
use App\Models\RecruitmentReport;
use App\Models\ResearchSubject;
use App\Models\User;
use App\Services\RecruitmentReportDocument;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ZipArchive;

class RecruitmentReportTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(): array
    {
        $professor = User::factory()->professor()->create(['is_active' => true]);
        $committeeProfessor = User::factory()->professor()->create(['is_active' => true]);
        $department = Department::create(['name' => 'Sciences', 'code' => 'SCI']);
        $subject = ResearchSubject::create([
            'professor_id' => $professor->id, 'department_id' => $department->id,
            'title' => 'Recherche & innovation <2026>', 'description' => 'Résumé scientifique & méthodes.', 'is_open' => true,
        ]);
        $candidate = User::factory()->create();
        $candidate->profile()->create(['first_name' => 'Amal', 'last_name' => 'Bennani']);
        $application = Application::create(['candidate_id' => $candidate->id, 'subject_id' => $subject->id, 'status' => 'pending']);
        $data = [
            'co_director_name' => 'Co-directeur', 'co_director_email' => 'co@example.com', 'co_director_institution' => 'Université externe',
            'report_date' => '2026-09-09',
            'committee' => [
                ['professor_id' => $committeeProfessor->id],
                ['name' => 'Membre Deux', 'email' => 'two@example.com', 'institution' => 'UEMF'],
                ['name' => 'Membre Trois', 'email' => 'three@example.com', 'institution' => 'UEMF'],
            ],
            'shortlist' => [['application_id' => $application->id, 'score' => 0]],
            'interviews' => [['application_id' => $application->id, 'score' => 100]],
        ];
        return [$professor, $subject, $data, $committeeProfessor];
    }

    public function test_professor_can_save_reload_and_export_a_report_with_preserved_template(): void
    {
        [$professor, $subject, $data, $committeeProfessor] = $this->fixture();
        $this->actingAs($professor)->get(route('professor.recruitment.index'))->assertOk()->assertSee($subject->title);
        $this->get(route('professor.recruitment.edit', $subject))->assertOk()->assertSee('Amal');
        // Empty rows emitted by the form must be ignored.
        $data['committee'][] = ['professor_id' => '', 'name' => '', 'email' => '', 'institution' => ''];
        $data['shortlist'][] = ['application_id' => '', 'score' => ''];
        $this->post(route('professor.recruitment.update', $subject), $data)->assertSessionHasNoErrors()->assertRedirect();
        $saved = RecruitmentReport::sole()->data;
        $this->assertSame($committeeProfessor->name, $saved['committee'][0]['name']);
        $this->get(route('professor.recruitment.edit', $subject))->assertOk()->assertSee('Co-directeur');
        $download = $this->post(route('professor.recruitment.update', $subject), [...$data, 'action' => 'download']);
        $download->assertDownload('PV-Recrutement-'.$subject->id.'.docx');
        $downloadPath = $download->baseResponse->getFile()->getPathname();
        @unlink($downloadPath);

        $path = app(RecruitmentReportDocument::class)->generate($subject, $saved);
        $output = new ZipArchive;
        $template = new ZipArchive;
        try {
            $this->assertTrue($output->open($path));
            $template->open(base_path('PV-Recrutement-Canvas.docx'));
            for ($i = 0; $i < $template->numFiles; $i++) {
                $part = $template->getNameIndex($i);
                if ($part !== 'word/document.xml' && $part !== 'word/header1.xml') {
                    $this->assertSame($template->getFromName($part), $output->getFromName($part), $part);
                }
            }
            $xml = new DOMDocument;
            $this->assertTrue($xml->loadXML($output->getFromName('word/document.xml')));
            $xpath = new DOMXPath($xml);
            $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
            $this->assertStringContainsString($subject->title, $xml->textContent);
            $this->assertStringContainsString($subject->description, $xml->textContent);
            $this->assertStringContainsString('Amal', $xml->textContent);
            $this->assertStringContainsString('09/09/2026', $xml->textContent);
            $this->assertStringNotContainsString('xxxxxxxx', $xml->textContent);
            // The thesis director and co-director lead the jury, ahead of the 3 chosen members.
            $bodyNodes = iterator_to_array($xpath->query('//w:body/*'));
            $this->assertStringContainsString($professor->name, $bodyNodes[32]->textContent);
            $this->assertStringContainsString('Co-directeur', $bodyNodes[33]->textContent);
            $this->assertStringContainsString($committeeProfessor->name, $bodyNodes[34]->textContent);
            $this->assertStringContainsString('Membre Deux', $bodyNodes[35]->textContent);
            $this->assertStringContainsString('Membre Trois', $bodyNodes[36]->textContent);
            $this->assertSame('Pr. '.$professor->name, $bodyNodes[67]->textContent);
            $this->assertSame('Pr. Co-directeur', $bodyNodes[70]->textContent);
            $this->assertSame('Pr. '.$committeeProfessor->name, $bodyNodes[73]->textContent);
            $original = new DOMDocument;
            $original->loadXML($template->getFromName('word/document.xml'));
            $originalXpath = new DOMXPath($original);
            $originalXpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
            foreach ([2, 4] as $table) {
                $this->assertSame(
                    $original->saveXML($originalXpath->query('//w:body/w:tbl')->item($table - 1)),
                    $xml->saveXML($xpath->query('//w:body/w:tbl')->item($table - 1))
                );
            }

            // The header reference number's date should follow the PV date, not the template's dummy digits.
            $header = new DOMDocument;
            $this->assertTrue($header->loadXML($output->getFromName('word/header1.xml')));
            $headerXpath = new DOMXPath($header);
            $headerXpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
            $headerText = implode('', array_map(fn ($t) => $t->textContent, iterator_to_array($headerXpath->query('//w:t'))));
            $this->assertStringContainsString('CED-090926', $headerText);
        } finally {
            $output->close();
            $template->close();
            @unlink($path);
        }
    }

    public function test_other_professors_and_candidates_cannot_access_the_report(): void
    {
        [, $subject, $data] = $this->fixture();
        foreach ([User::factory()->professor()->create(['is_active' => true]), User::factory()->create(['is_active' => true])] as $user) {
            $this->actingAs($user)->get(route('professor.recruitment.edit', $subject))->assertForbidden();
            $this->post(route('professor.recruitment.update', $subject), $data)->assertForbidden();
        }
        $this->assertDatabaseCount('recruitment_reports', 0);
    }

    public function test_export_expands_candidate_and_interview_tables_and_ranks_scores(): void
    {
        [$professor, $subject, $data] = $this->fixture();
        for ($i = 0; $i < 10; $i++) {
            Application::create(['subject_id' => $subject->id, 'candidate_id' => User::factory()->create()->id, 'status' => 'pending']);
        }
        $data['shortlist'] = $subject->applications()->orderBy('id')->limit(5)->get()->map(fn ($application, $i) => ['application_id' => $application->id, 'score' => $i * 20])->all();
        $data['interviews'] = collect($data['shortlist'])->take(3)->values()->all();
        $this->actingAs($professor)->post(route('professor.recruitment.update', $subject), $data)->assertSessionHasNoErrors();
        $saved = RecruitmentReport::sole()->data;
        $this->assertEquals(80, $saved['shortlist'][0]['score']);
        $path = app(RecruitmentReportDocument::class)->generate($subject, $saved);
        $zip = new ZipArchive;
        try {
            $zip->open($path);
            $xml = new DOMDocument;
            $xml->loadXML($zip->getFromName('word/document.xml'));
            $xpath = new DOMXPath($xml);
            $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
            $this->assertSame(12, $xpath->query('(//w:body/w:tbl)[1]/w:tr')->length);
            // Interviews are capped at 3 candidates: header row + 3 data rows.
            $this->assertSame(4, $xpath->query('(//w:body/w:tbl)[5]/w:tr')->length);
        } finally {
            $zip->close();
            @unlink($path);
        }
        // The committee is fixed at exactly 3 members (the director/co-director are added automatically).
        $data['committee'][] = ['name' => 'Four', 'email' => 'four@example.com'];
        $this->post(route('professor.recruitment.update', $subject), $data)->assertSessionHasErrors('committee');
    }

    public function test_invalid_scores_committees_and_non_shortlisted_interviews_are_rejected(): void
    {
        [$professor, $subject, $data] = $this->fixture();
        $this->actingAs($professor);
        $invalid = $data;
        $invalid['committee'] = array_slice($data['committee'], 0, 2);
        $invalid['shortlist'][0]['score'] = 101;
        $this->post(route('professor.recruitment.update', $subject), $invalid)->assertSessionHasErrors(['committee', 'shortlist.0.score']);
        $second = Application::create(['subject_id' => $subject->id, 'candidate_id' => User::factory()->create()->id, 'status' => 'pending']);
        $invalid = $data;
        $invalid['interviews'][0]['application_id'] = $second->id;
        $this->post(route('professor.recruitment.update', $subject), $invalid)->assertSessionHasErrors('interviews.0.application_id');
        $invalid = $data;
        $invalid['committee'][2] = $invalid['committee'][1];
        $this->post(route('professor.recruitment.update', $subject), $invalid)->assertSessionHasErrors('committee.2.email');
        $invalid = $data;
        $invalid['committee'][0] = ['professor_id' => $professor->id];
        $this->post(route('professor.recruitment.update', $subject), $invalid)->assertSessionHasErrors('committee.0.email');
        $invalid = $data;
        $invalid['committee'][0] = ['name' => 'Co', 'email' => 'co@example.com'];
        $this->post(route('professor.recruitment.update', $subject), $invalid)->assertSessionHasErrors('committee.0.email');
        $invalid = $data;
        $extra = [Application::create(['subject_id' => $subject->id, 'candidate_id' => User::factory()->create()->id, 'status' => 'pending']),
            Application::create(['subject_id' => $subject->id, 'candidate_id' => User::factory()->create()->id, 'status' => 'pending'])];
        $invalid['shortlist'] = array_merge($data['shortlist'], [
            ['application_id' => $second->id, 'score' => 90],
            ['application_id' => $extra[0]->id, 'score' => 80],
            ['application_id' => $extra[1]->id, 'score' => 70],
        ]);
        $invalid['interviews'] = $invalid['shortlist'];
        $this->post(route('professor.recruitment.update', $subject), $invalid)->assertSessionHasErrors('interviews');
        [, $otherSubject] = $this->fixtureWithOtherSubject($professor);
        $foreign = Application::create(['subject_id' => $otherSubject->id, 'candidate_id' => User::factory()->create()->id, 'status' => 'pending']);
        $invalid = $data;
        $invalid['shortlist'][0]['application_id'] = $foreign->id;
        $this->post(route('professor.recruitment.update', $subject), $invalid)->assertSessionHasErrors('shortlist.0.application_id');
        $this->assertDatabaseCount('recruitment_reports', 0);
    }

    private function fixtureWithOtherSubject(User $professor): array
    {
        return [$professor, ResearchSubject::create([
            'professor_id' => $professor->id, 'department_id' => Department::first()->id,
            'title' => 'Other', 'description' => 'Other', 'is_open' => true,
        ])];
    }
}
