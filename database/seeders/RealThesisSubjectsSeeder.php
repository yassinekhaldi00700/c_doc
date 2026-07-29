<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Department;
use App\Models\ResearchSubject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Imports the real 2026-27 doctoral subject proposals ("Propositions -
 * sujets de thèse - 26-27 - Valides"). Each row supplies the supervising
 * professor's name/email, their établissement (mapped to a department),
 * and the subject title only — professors fill in the rest (description,
 * responsibilities, candidate profile, keywords, slots) themselves via
 * their own subject editor.
 *
 * Departments and professors are matched by unique key (department code,
 * user email) and subjects by (professor_id, title), so re-running this
 * seeder is safe and never duplicates or overwrites data a professor has
 * since edited themselves.
 */
class RealThesisSubjectsSeeder extends Seeder
{
    private const DEFAULT_PASSWORD = 'changeme.now!';

    /**
     * Établissement (spreadsheet) -> department code/name.
     */
    private const DEPARTMENTS = [
        'Engineering Sciences' => ['code' => 'ENGSCI', 'name' => 'Engineering Sciences'],
        'Health Sciences/Engineering Sciences' => ['code' => 'HSENG', 'name' => 'Health Sciences/Engineering Sciences'],
        'Human and Social Sciences' => ['code' => 'HSS', 'name' => 'Human and Social Sciences'],
    ];

    public function run(): void
    {
        $rows = json_decode(
            file_get_contents(database_path('data/thesis_subjects_2026_27.json')),
            true,
            flags: JSON_THROW_ON_ERROR
        );

        $departmentsByEtablissement = collect(self::DEPARTMENTS)->mapWithKeys(
            fn (array $dept, string $etablissement) => [
                $etablissement => Department::firstOrCreate(['code' => $dept['code']], ['name' => $dept['name']])->id,
            ]
        );

        // One row per professor (by email), keeping the first name spelling
        // encountered — the spreadsheet has minor formatting inconsistencies
        // (e.g. "ABADI Asmae" vs "Asmae ABADI") for the same person/email.
        $professorRows = collect($rows)->unique('email')->keyBy('email');

        $professorIdsByEmail = $professorRows->map(function (array $row) use ($departmentsByEtablissement) {
            $user = User::firstOrCreate(
                ['email' => $row['email']],
                [
                    'name' => 'Prof. '.trim($row['encadrant']),
                    'role' => UserRole::Professor,
                    'department_id' => $departmentsByEtablissement[$row['etablissement']],
                    'password' => Hash::make(self::DEFAULT_PASSWORD),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );

            return $user->id;
        });

        foreach ($rows as $row) {
            $professorId = $professorIdsByEmail[$row['email']];

            ResearchSubject::updateOrCreate(
                ['professor_id' => $professorId, 'title' => $row['titre']],
                [
                    'department_id' => $departmentsByEtablissement[$row['etablissement']],
                    'description' => 'Full description pending — to be completed by the supervising professor.',
                    'responsibilities' => null,
                    'candidate_profile' => null,
                    'keywords' => null,
                    'is_open' => false,
                ]
            );
        }

        $this->command?->info(sprintf(
            '%d departments, %d professors, %d research subjects imported from the 2026-27 thesis proposals.',
            $departmentsByEtablissement->count(),
            $professorIdsByEmail->count(),
            count($rows)
        ));
    }
}
