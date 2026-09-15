<?php

namespace App\Http\Requests\Professor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OralExamPicksRequest extends FormRequest
{
    /** The oral exam window this recruitment round is scheduled in. */
    public const MIN_DATE = '2026-09-21';

    public const MAX_DATE = '2026-09-25';

    public function authorize(): bool
    {
        return $this->user()?->isProfessor()
            && (int) $this->route('subject')->professor_id === (int) $this->user()->id;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'application_ids' => ['array', 'max:5'],
            'application_ids.*' => ['integer', Rule::exists('applications', 'id')->where('subject_id', $this->route('subject')->id)],
        ];

        // Every picked candidate needs a proposed exam date within the window;
        // dates submitted for candidates that aren't picked are simply ignored.
        foreach ((array) $this->input('application_ids', []) as $id) {
            $rules["dates.$id"] = ['required', 'date_format:Y-m-d', 'after_or_equal:'.self::MIN_DATE, 'before_or_equal:'.self::MAX_DATE];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'application_ids.max' => 'You can propose at most 5 candidates for the oral exam.',
            'application_ids.*.exists' => 'A selected candidate did not apply to this subject.',
            'dates.*.required' => 'Pick a proposed exam date for every candidate you select.',
            'dates.*.date_format' => 'The proposed exam date is not valid.',
            'dates.*.after_or_equal' => 'The proposed exam date must be between '.self::MIN_DATE.' and '.self::MAX_DATE.'.',
            'dates.*.before_or_equal' => 'The proposed exam date must be between '.self::MIN_DATE.' and '.self::MAX_DATE.'.',
        ];
    }
}
