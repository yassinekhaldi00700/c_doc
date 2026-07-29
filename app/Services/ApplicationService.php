<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Enums\DocumentType;
use App\Models\Application;
use App\Models\ResearchSubject;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ApplicationService
{
    public function __construct(
        protected DocumentUploadService $documentUploadService,
        protected ProfileService $profileService,
    ) {
    }

    public function submit(
        User $candidate,
        ResearchSubject $subject,
        ?string $motivationSummary,
        UploadedFile $motivationLetter,
    ): Application {
        return DB::transaction(function () use ($candidate, $subject, $motivationSummary, $motivationLetter) {
            $application = Application::create([
                'candidate_id' => $candidate->id,
                'subject_id' => $subject->id,
                'status' => ApplicationStatus::Pending,
                'motivation_summary' => $motivationSummary,
                'submitted_at' => now(),
            ]);

            if ($candidate->profile) {
                $this->profileService->snapshotDocumentsFor($application, $candidate->profile);
            }

            $this->documentUploadService->store($application, DocumentType::MotivationLetter, $motivationLetter, 'applications');

            $application->statusLogs()->create([
                'from_status' => null,
                'to_status' => ApplicationStatus::Pending,
                'changed_by' => $candidate->id,
                'comment' => 'Application submitted.',
            ]);

            return $application;
        });
    }
}
