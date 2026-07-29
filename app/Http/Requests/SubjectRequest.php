<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        $subject = $this->route('subject');

        return $subject
            ? ($this->user()?->can('update', $subject) ?? false)
            : ($this->user()?->can('create', \App\Models\ResearchSubject::class) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Only present on the admin "create subject" form — a subject
            // being updated already has its professor fixed, and there's
            // no reassignment UI for it (yet).
            'professor_id' => [
                $this->route('subject') ? 'sometimes' : 'required',
                Rule::exists('users', 'id')->where('role', UserRole::Professor->value),
            ],

            // Same story for department/title: fixed at creation, the
            // professor's edit form doesn't render them as inputs at all
            // (shown read-only instead), so nothing to validate on update.
            'department_id' => [$this->route('subject') ? 'sometimes' : 'required', 'exists:departments,id'],
            'title' => [$this->route('subject') ? 'sometimes' : 'required', 'string', 'max:255'],

            // The admin's "create subject" form only sets professor/
            // department/title — the professor fills in the content
            // themselves afterward, so description is required on their
            // edit form but simply isn't sent (or validated) on create.
            'description' => [$this->route('subject') ? 'required' : 'sometimes', 'string', 'max:5000'],
            'responsibilities' => ['nullable', 'string', 'max:3000'],
            'candidate_profile' => ['nullable', 'string', 'max:3000'],
            'keywords' => ['nullable', 'string', 'max:255'],
            'is_open' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * On the professor's edit form, description/responsibilities/candidate
     * profile/keywords are treated as one group: either all four carry
     * content or none do — a subject can't end up with keywords but no
     * stated responsibilities or candidate profile. The admin "create
     * subject" form doesn't have this constraint.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->route('subject')) {
                return;
            }

            $values = [
                trim((string) $this->input('description')),
                trim((string) $this->input('responsibilities')),
                trim((string) $this->input('candidate_profile')),
                trim((string) $this->input('keywords')),
            ];

            $filledCount = count(array_filter($values, fn (string $value) => $value !== ''));

            if ($filledCount > 0 && $filledCount < count($values)) {
                $validator->errors()->add(
                    'description',
                    'Fill in Description, PhD student\'s responsibilities, Candidate profile, and Keywords together, or leave all four empty.'
                );
            }
        });
    }
}
