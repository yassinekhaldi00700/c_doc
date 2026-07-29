<?php

namespace App\Services;

use App\Enums\DocumentType;
use App\Models\Application;
use App\Models\Profile;
use App\Models\ProfileDocument;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileService
{
    /**
     * Document types uploaded as a single file that can be replaced by a
     * re-upload, as opposed to append-only multi-file slots.
     */
    private const SINGLE_FILE_TYPES = [
        DocumentType::CinPassport,
        DocumentType::Cv,
        DocumentType::LastDiplomaCertificate,
        DocumentType::LicenseCertificate,
        DocumentType::BaccalaureatCertificate,
        DocumentType::RecommendationLetter,
        DocumentType::Other,
    ];

    public function __construct(protected DocumentUploadService $documentUploadService)
    {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateDetails(User $candidate, array $data): Profile
    {
        $profile = Profile::updateOrCreate(['user_id' => $candidate->id], $data);

        $this->stampCompletionIfNeeded($profile);

        return $profile;
    }

    /**
     * @param  array<string, UploadedFile|array<UploadedFile>>  $files
     */
    public function uploadDocuments(Profile $profile, array $files): void
    {
        DB::transaction(function () use ($profile, $files) {
            foreach ($files as $typeValue => $fileOrFiles) {
                $type = DocumentType::from($typeValue);
                $list = is_array($fileOrFiles) ? $fileOrFiles : [$fileOrFiles];

                foreach ($list as $file) {
                    if (! $file) {
                        continue;
                    }

                    if (in_array($type, self::SINGLE_FILE_TYPES, true)) {
                        $this->documentUploadService->replace($profile, $type, $file, 'profiles');
                    } else {
                        $this->documentUploadService->store($profile, $type, $file, 'profiles');
                    }
                }
            }
        });

        $this->stampCompletionIfNeeded($profile->fresh(['documents']));
    }

    public function deleteDocument(ProfileDocument $document): void
    {
        $this->documentUploadService->delete($document);
    }

    /**
     * Copy the candidate's current profile documents into the given
     * application's own document set, so reviewers always see exactly what
     * was true at the moment the candidate applied, regardless of later
     * profile edits.
     */
    public function snapshotDocumentsFor(Application $application, Profile $profile): void
    {
        $disk = $this->documentUploadService->disk();

        foreach ($profile->documents as $document) {
            if (! Storage::disk($disk)->exists($document->disk_path)) {
                continue;
            }

            $extension = pathinfo($document->disk_path, PATHINFO_EXTENSION);
            $newPath = sprintf(
                'applications/%d/%s-%s%s',
                $application->id,
                $document->type->value,
                Str::uuid(),
                $extension ? ".{$extension}" : ''
            );

            Storage::disk($disk)->copy($document->disk_path, $newPath);

            $application->documents()->create([
                'type' => $document->type,
                'disk_path' => $newPath,
                'original_name' => $document->original_name,
                'mime_type' => $document->mime_type,
                'size' => $document->size,
            ]);
        }
    }

    protected function stampCompletionIfNeeded(Profile $profile): void
    {
        if ($profile->completed_at === null && $profile->isComplete()) {
            $profile->forceFill(['completed_at' => now()])->save();
        }
    }
}
