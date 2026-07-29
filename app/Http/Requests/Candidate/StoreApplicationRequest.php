<?php

namespace App\Http\Requests\Candidate;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isCandidate() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'motivation_summary' => ['nullable', 'string', 'max:3000'],
            'documents.motivation_letter' => ['required', 'file', 'mimes:pdf', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'documents.motivation_letter' => 'motivation letter',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function documentFiles(): array
    {
        return array_filter($this->file('documents', []));
    }
}
