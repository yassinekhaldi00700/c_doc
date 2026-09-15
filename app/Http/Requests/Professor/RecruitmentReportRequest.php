<?php

namespace App\Http\Requests\Professor;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class RecruitmentReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isProfessor()
            && (int) $this->route('subject')->professor_id === (int) $this->user()->id;
    }

    protected function prepareForValidation(): void
    {
        foreach (['committee', 'shortlist', 'interviews'] as $field) {
            $rows = $this->input($field, []);
            if (is_array($rows)) {
                $this->merge([$field => array_values(array_filter($rows, fn ($row) =>
                    ! is_array($row) || collect($row)->contains(fn ($value) => filled($value))
                ))]);
            }
        }
    }

    public function rules(): array
    {
        $candidateRule = Rule::exists('applications', 'id')->where('subject_id', $this->route('subject')->id);

        return [
            'co_director_name' => ['nullable', 'required_with:co_director_email,co_director_institution', 'string', 'max:200'],
            'co_director_email' => ['nullable', 'required_with:co_director_name', 'email', 'max:255'],
            'co_director_institution' => ['nullable', 'string', 'max:200'],
            'report_date' => ['required', 'date_format:Y-m-d'],
            'committee' => ['required', 'array', 'min:3', 'max:3'],
            'committee.*' => ['array:professor_id,name,email,institution'],
            'committee.*.professor_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('role', UserRole::Professor->value)],
            'committee.*.name' => ['nullable', 'required_without:committee.*.professor_id', 'string', 'max:200'],
            'committee.*.email' => ['nullable', 'required_without:committee.*.professor_id', 'email', 'max:255'],
            'committee.*.institution' => ['nullable', 'string', 'max:200'],
            'shortlist' => ['required', 'array', 'min:1', 'max:5'],
            'shortlist.*' => ['array:application_id,score'],
            'shortlist.*.application_id' => ['required', 'integer', 'distinct', $candidateRule],
            'shortlist.*.score' => ['required', 'numeric', 'between:0,100'],
            'interviews' => ['required', 'array', 'min:1', 'max:3'],
            'interviews.*' => ['array:application_id,score'],
            'interviews.*.application_id' => ['required', 'integer', 'distinct', $candidateRule],
            'interviews.*.score' => ['required', 'numeric', 'between:0,100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'co_director_name.required_with' => 'Indiquez le nom complet du co-directeur (vous avez renseigné son e-mail ou son établissement).',
            'co_director_email.required_with' => 'Indiquez l’adresse e-mail du co-directeur (vous avez renseigné son nom).',
            'co_director_email.email' => 'L’adresse e-mail du co-directeur n’est pas valide.',
            'report_date.required' => 'La date du PV est obligatoire.',
            'report_date.date_format' => 'La date du PV n’est pas valide.',

            'committee.required' => 'Choisissez exactement 3 membres du comité, en plus du directeur de thèse et, le cas échéant, du co-directeur.',
            'committee.min' => 'Choisissez exactement 3 membres du comité, en plus du directeur de thèse et, le cas échéant, du co-directeur.',
            'committee.max' => 'Choisissez exactement 3 membres du comité : le directeur de thèse et le co-directeur (le cas échéant) sont déjà inclus automatiquement dans le PV.',
            'committee.*.name.required_without' => 'Indiquez le nom du membre du comité, ou choisissez un professeur de la plateforme.',
            'committee.*.email.required_without' => 'Indiquez l’adresse e-mail du membre du comité, ou choisissez un professeur de la plateforme.',
            'committee.*.email.email' => 'L’adresse e-mail d’un membre du comité n’est pas valide.',

            'shortlist.required' => 'Sélectionnez au moins un candidat dans « 5. Résultats de la Pré-sélection ».',
            'shortlist.min' => 'Sélectionnez au moins un candidat dans « 5. Résultats de la Pré-sélection ».',
            'shortlist.max' => 'Vous ne pouvez pas retenir plus de 5 candidats en pré-sélection.',
            'shortlist.*.application_id.required' => 'Sélectionnez un candidat pour chaque ligne renseignée de « 5. Résultats de la Pré-sélection ».',
            'shortlist.*.application_id.distinct' => 'Un même candidat ne peut être sélectionné qu’une seule fois en pré-sélection.',
            'shortlist.*.application_id.integer' => 'Le candidat sélectionné en pré-sélection n’est pas valide.',
            'shortlist.*.application_id.exists' => 'Le candidat sélectionné en pré-sélection n’a pas postulé à ce sujet.',
            'shortlist.*.score.required' => 'Indiquez une note (0 à 100) pour chaque candidat de la pré-sélection.',
            'shortlist.*.score.between' => 'La note de pré-sélection doit être comprise entre 0 et 100.',

            'interviews.required' => 'Sélectionnez au moins un candidat pour les entretiens, parmi ceux retenus en « 5. Résultats de la Pré-sélection ».',
            'interviews.min' => 'Sélectionnez au moins un candidat pour les entretiens.',
            'interviews.max' => 'Vous ne pouvez pas retenir plus de 3 candidats pour les entretiens.',
            'interviews.*.application_id.required' => 'Sélectionnez un candidat pour chaque ligne renseignée des entretiens.',
            'interviews.*.application_id.distinct' => 'Un même candidat ne peut être sélectionné qu’une seule fois pour les entretiens.',
            'interviews.*.application_id.integer' => 'Le candidat sélectionné pour l’entretien n’est pas valide.',
            'interviews.*.application_id.exists' => 'Le candidat sélectionné pour l’entretien n’a pas postulé à ce sujet.',
            'interviews.*.score.required' => 'Indiquez une note d’entretien (0 à 100) pour chaque candidat sélectionné.',
            'interviews.*.score.between' => 'La note d’entretien doit être comprise entre 0 et 100.',
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }
            $ids = collect($this->input('shortlist'))->pluck('application_id')->map(fn ($id) => (int) $id);
            foreach ($this->input('interviews') as $index => $row) {
                if (! $ids->contains((int) $row['application_id'])) {
                    $validator->errors()->add("interviews.$index.application_id", 'Ce candidat doit d’abord être retenu dans « 5. Résultats de la Pré-sélection » avant de pouvoir être sélectionné pour l’entretien.');
                }
            }
            // The director and co-director are prepended to the committee members on
            // the PV, so a committee pick that is really one of them would show up
            // twice in the jury — reject it here just like any other duplicate.
            $emails = [mb_strtolower($this->route('subject')->professor->email)];
            if (filled($this->input('co_director_email'))) {
                $emails[] = mb_strtolower($this->input('co_director_email'));
            }
            foreach ($this->input('committee') as $index => $row) {
                $email = filled($row['professor_id'] ?? null)
                    ? User::findOrFail($row['professor_id'])->email : $row['email'];
                $email = mb_strtolower($email);
                if (in_array($email, $emails, true)) {
                    $validator->errors()->add("committee.$index.email", 'Chaque membre du comité doit être une personne différente du directeur de thèse, du co-directeur et des autres membres.');
                }
                $emails[] = $email;
            }
        }];
    }

    public function reportData(): array
    {
        $data = $this->validated();
        $data['committee'] = array_map(function ($member) {
            if (filled($member['professor_id'] ?? null)) {
                $professor = User::findOrFail($member['professor_id']);
                return ['professor_id' => $professor->id, 'name' => $professor->name, 'email' => $professor->email, 'institution' => 'UEMF'];
            }
            return $member;
        }, $data['committee']);

        foreach (['shortlist', 'interviews'] as $field) {
            $data[$field] = collect($data[$field])->sortByDesc('score')->values()->all();
        }

        return $data;
    }
}
