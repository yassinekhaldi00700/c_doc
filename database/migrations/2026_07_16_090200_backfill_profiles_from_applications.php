<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Profile-level document types eligible to be copied forward from a
     * candidate's most recent application. Research proposal and motivation
     * letter are intentionally never copied — they no longer belong to the
     * profile phase.
     */
    private const COPIABLE_DOCUMENT_TYPES = ['cin_passport', 'cv', 'transcript', 'diploma'];

    public function up(): void
    {
        $candidateIds = DB::table('applications')->distinct()->pluck('candidate_id');

        foreach ($candidateIds as $candidateId) {
            DB::transaction(function () use ($candidateId) {
                $source = DB::table('applications')
                    ->where('candidate_id', $candidateId)
                    ->orderByRaw('COALESCE(submitted_at, created_at) DESC')
                    ->first();

                if (! $source) {
                    return;
                }

                $profileId = DB::table('profiles')->where('user_id', $candidateId)->value('id');

                if (! $profileId) {
                    $profileId = DB::table('profiles')->insertGetId([
                        'user_id' => $candidateId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('profiles')->where('id', $profileId)->update([
                    'first_name' => $source->first_name,
                    'last_name' => $source->last_name,
                    'birth_date' => $source->birth_date,
                    'birth_place' => $source->birth_place,
                    'nationality' => $source->nationality,
                    'gender' => $source->gender,
                    'cin_or_passport_number' => $source->cin_or_passport_number,
                    'address' => $source->address,
                    'phone' => $source->phone,
                    'last_degree' => $source->last_degree,
                    'last_institution' => $source->last_institution,
                    'graduation_year' => $source->graduation_year,
                    'field_of_study' => $source->field_of_study,
                    'grade_mention' => $source->grade_mention,
                    'updated_at' => now(),
                ]);

                $existingKeys = DB::table('profile_documents')
                    ->where('profile_id', $profileId)
                    ->get(['type', 'original_name'])
                    ->map(fn ($d) => $d->type.'|'.$d->original_name)
                    ->all();

                $documents = DB::table('application_documents')
                    ->where('application_id', $source->id)
                    ->whereIn('type', self::COPIABLE_DOCUMENT_TYPES)
                    ->get();

                foreach ($documents as $document) {
                    $key = $document->type.'|'.$document->original_name;

                    if (in_array($key, $existingKeys, true)) {
                        continue;
                    }

                    if (! Storage::disk('local')->exists($document->disk_path)) {
                        continue;
                    }

                    $extension = pathinfo($document->disk_path, PATHINFO_EXTENSION);
                    $newPath = "profiles/{$profileId}/{$document->type}-".Str::uuid().($extension ? ".{$extension}" : '');

                    Storage::disk('local')->copy($document->disk_path, $newPath);

                    DB::table('profile_documents')->insert([
                        'profile_id' => $profileId,
                        'type' => $document->type,
                        'disk_path' => $newPath,
                        'original_name' => $document->original_name,
                        'mime_type' => $document->mime_type,
                        'size' => $document->size,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $existingKeys[] = $key;
                }
            });
        }
    }

    public function down(): void
    {
        // Data-only migration: intentionally a no-op. Reversing could destroy
        // profile data/documents a candidate has since created or edited.
    }
};
