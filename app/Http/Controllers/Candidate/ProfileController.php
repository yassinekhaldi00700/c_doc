<?php

namespace App\Http\Controllers\Candidate;

use App\Enums\DegreeTrack;
use App\Enums\DocumentType;
use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\ProfileDocument;
use App\Models\ResearchSubject;
use App\Services\ProfileService;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Profile-level document types, keyed by the field name under
     * `documents[...]`, mapped to the friendly name used in messages.
     */
    private const ACADEMIC_DOCUMENT_LABELS = [
        'last_diploma_certificate' => 'last diploma certificate',
        'license_certificate' => 'license / bachelor certificate',
        'baccalaureat_certificate' => 'baccalaureat certificate',
        'transcript' => 'academic transcripts',
        'cv' => 'CV',
        'publication' => 'publications',
        'recommendation_letter' => 'recommendation letter',
    ];

    public function __construct(protected ProfileService $profileService)
    {
    }

    public function overview(Request $request): View
    {
        $profile = $this->profileFor($request);

        return view('candidate.profile.overview', [
            'profile' => $profile,
            'intendedSubject' => session('intended_subject')
                ? ResearchSubject::find(session('intended_subject'))
                : null,
        ]);
    }

    public function editPersonal(Request $request): View
    {
        return view('candidate.profile.personal', [
            'profile' => $this->profileFor($request)->load('documents'),
        ]);
    }

    /**
     * A browser can never refill an <input type="file"> after a redirect,
     * so any submitted file is saved immediately — before checking whether
     * the rest of the step is complete. That way, failing on an unrelated
     * field (e.g. a missing text field) never forces the candidate to
     * re-select a file they already provided correctly.
     */
    public function updatePersonal(Request $request): RedirectResponse
    {
        $profile = $this->profileFor($request);

        $fileValidator = Validator::make($request->all(), [
            'documents.cin_passport' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'privacy_consent' => ['required', 'accepted'],
        ], $this->privacyConsentMessages(), [
            'documents.cin_passport' => 'national ID / passport',
            'privacy_consent' => 'personal-data consent',
        ]);

        if ($fileValidator->fails()) {
            return back()->withErrors($fileValidator)->withInput();
        }

        $files = array_filter($request->file('documents', []));

        if (! empty($files)) {
            $this->profileService->uploadDocuments($profile, $files);
            $profile->load('documents');
        }

        $textValidator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date', 'before:-18 years'],
            'birth_place' => ['required', 'string', 'max:150'],
            'nationality' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female'],
            'cin_or_passport_number' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
        ]);

        $this->forceValidation($textValidator);

        if (! $profile->hasDocument(DocumentType::CinPassport)) {
            $textValidator->errors()->add('documents.cin_passport', 'The national ID / passport field is required.');
        }

        if ($textValidator->errors()->isNotEmpty()) {
            return back()->withErrors($textValidator->errors())->withInput();
        }

        $this->profileService->updateDetails($request->user(), $textValidator->validated());

        return redirect()->route('candidate.profile.overview')
            ->with('success', 'Your personal information has been saved.');
    }

    public function editAcademic(Request $request): View
    {
        return view('candidate.profile.academic', [
            'profile' => $this->profileFor($request)->load('documents'),
        ]);
    }

    public function updateAcademic(Request $request): RedirectResponse
    {
        $profile = $this->profileFor($request);

        $fileValidator = Validator::make(
            $request->all(),
            [...$this->academicFileRules(), 'privacy_consent' => ['required', 'accepted']],
            $this->privacyConsentMessages(),
            [...$this->academicFileAttributes(), 'privacy_consent' => 'personal-data consent']
        );

        if ($fileValidator->fails()) {
            return back()->withErrors($fileValidator)->withInput();
        }

        $files = array_filter($request->file('documents', []));

        if (! empty($files)) {
            $this->profileService->uploadDocuments($profile, $files);
            $profile->load('documents');
        }

        $textValidator = Validator::make($request->all(), [
            'degree_track' => ['required', 'in:master,engineer'],

            'last_degree' => ['required', 'string', 'max:150'],
            'last_institution' => ['required', 'string', 'max:200'],
            'graduation_year' => ['required', 'integer', 'min:1980', 'max:'.now()->year],
            'field_of_study' => ['required', 'string', 'max:150'],
            'grade_mention' => ['nullable', 'string', 'max:100'],

            'license_institution' => ['nullable', 'required_if:degree_track,master', 'string', 'max:200'],
            'license_graduation_year' => ['nullable', 'required_if:degree_track,master', 'integer', 'min:1980', 'max:'.now()->year],
            'license_field_of_study' => ['nullable', 'required_if:degree_track,master', 'string', 'max:150'],
            'license_grade_mention' => ['nullable', 'string', 'max:100'],

            'bac_institution' => ['required', 'string', 'max:200'],
            'bac_graduation_year' => ['required', 'integer', 'min:1980', 'max:'.now()->year],
            'bac_field_of_study' => ['required', 'string', 'max:150'],
            'bac_grade_mention' => ['nullable', 'string', 'max:100'],
        ], [], [
            'license_institution' => 'license institution',
            'license_graduation_year' => 'license graduation year',
            'license_field_of_study' => 'license field of study',
            'bac_institution' => 'baccalaureat institution',
            'bac_graduation_year' => 'baccalaureat graduation year',
            'bac_field_of_study' => 'baccalaureat field of study',
        ]);

        $this->forceValidation($textValidator);

        foreach (DocumentType::academicRequiredDocuments() as $type) {
            if (! $profile->hasDocument($type)) {
                $textValidator->errors()->add(
                    "documents.{$type->value}",
                    'The '.self::ACADEMIC_DOCUMENT_LABELS[$type->value].' field is required.'
                );
            }
        }

        if ($request->input('degree_track') === DegreeTrack::Master->value && ! $profile->hasDocument(DocumentType::LicenseCertificate)) {
            $textValidator->errors()->add('documents.license_certificate', 'The license / bachelor certificate field is required.');
        }

        if ($textValidator->errors()->isNotEmpty()) {
            return back()->withErrors($textValidator->errors())->withInput();
        }

        $this->profileService->updateDetails($request->user(), $textValidator->validated());

        return redirect()->route('candidate.profile.overview')
            ->with('success', 'Your academic background has been saved.');
    }

    /**
     * Deleting one document is a small, isolated action — but since it's
     * rendered inside the same big Personal/Academic <form>, the browser
     * submits everything else on the page along with it. We're still in
     * the "filling phase" here, not a formal step submission, so nothing
     * the candidate has already typed or selected elsewhere on the page
     * should be discarded just because they removed one document: save
     * whatever else validates, silently skip whatever doesn't, and never
     * block the deletion itself on it.
     */
    public function destroyDocument(Request $request, ProfileDocument $document): RedirectResponse
    {
        abort_unless($document->profile->user_id === $request->user()->id, 403);

        $profile = $document->profile;

        $this->profileService->deleteDocument($document);
        $this->saveWhateverValidates($request, $profile);

        return back()->with('success', 'Document removed.');
    }

    /**
     * Stream a candidate's own profile document inline, same pattern as
     * DocumentController@preview but scoped to the owning candidate only —
     * profile documents aren't shared with reviewers directly (they're
     * snapshot-copied into each application at submission time instead).
     */
    public function previewDocument(Request $request, ProfileDocument $document)
    {
        abort_unless($document->profile->user_id === $request->user()->id, 403);

        abort_unless(Storage::disk('local')->exists($document->disk_path), 404);

        return Storage::disk('local')->response(
            $document->disk_path,
            $document->original_name,
            ['Content-Type' => $document->mime_type ?: 'application/pdf'],
            'inline'
        );
    }

    protected function profileFor(Request $request): Profile
    {
        return Profile::firstOrCreate(['user_id' => $request->user()->id]);
    }

    /**
     * Force the validator to run now (populating its message bag) so
     * additional, manually-built error messages can be appended to the
     * same bag before it's checked.
     */
    protected function forceValidation(ValidatorContract $validator): void
    {
        $validator->errors();
    }

    /**
     * Best-effort, non-blocking save of whatever text fields and files are
     * present in the request and pass basic format validation — used for
     * incidental actions (like deleting one document) that submit the rest
     * of the page's form along with them, but shouldn't act like a formal
     * "Save & Continue" (no "required" enforcement, no error messages).
     */
    protected function saveWhateverValidates(Request $request, Profile $profile): void
    {
        $textFields = $request->only(array_keys($this->lenientProfileFieldRules()));

        if (! empty($textFields)) {
            $validator = Validator::make($textFields, $this->lenientProfileFieldRules());
            $this->forceValidation($validator);

            $validFields = collect($textFields)
                ->reject(fn ($value, $key) => $validator->errors()->has($key))
                ->all();

            if (! empty($validFields)) {
                $this->profileService->updateDetails($request->user(), $validFields);
            }
        }

        $files = array_filter($request->file('documents', []));

        if (! empty($files)) {
            $fileValidator = Validator::make($request->all(), [
                ...$this->academicFileRules(),
                'documents.cin_passport' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            ]);
            $this->forceValidation($fileValidator);

            $validFiles = collect($files)
                ->reject(fn ($value, $type) => $fileValidator->errors()->has("documents.{$type}")
                    || $fileValidator->errors()->has("documents.{$type}.*"))
                ->all();

            if (! empty($validFiles)) {
                $this->profileService->uploadDocuments($profile, $validFiles);
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function lenientProfileFieldRules(): array
    {
        return [
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date', 'before:-18 years'],
            'birth_place' => ['nullable', 'string', 'max:150'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', 'in:male,female'],
            'cin_or_passport_number' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],

            'degree_track' => ['nullable', 'in:master,engineer'],
            'last_degree' => ['nullable', 'string', 'max:150'],
            'last_institution' => ['nullable', 'string', 'max:200'],
            'graduation_year' => ['nullable', 'integer', 'min:1980', 'max:'.now()->year],
            'field_of_study' => ['nullable', 'string', 'max:150'],
            'grade_mention' => ['nullable', 'string', 'max:100'],
            'license_institution' => ['nullable', 'string', 'max:200'],
            'license_graduation_year' => ['nullable', 'integer', 'min:1980', 'max:'.now()->year],
            'license_field_of_study' => ['nullable', 'string', 'max:150'],
            'license_grade_mention' => ['nullable', 'string', 'max:100'],
            'bac_institution' => ['nullable', 'string', 'max:200'],
            'bac_graduation_year' => ['nullable', 'integer', 'min:1980', 'max:'.now()->year],
            'bac_field_of_study' => ['nullable', 'string', 'max:150'],
            'bac_grade_mention' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function academicFileRules(): array
    {
        return [
            'documents.last_diploma_certificate' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'documents.license_certificate' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'documents.baccalaureat_certificate' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'documents.transcript' => ['nullable', 'array'],
            'documents.transcript.*' => ['file', 'mimes:pdf', 'max:10240'],
            'documents.cv' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'documents.publication' => ['nullable', 'array'],
            'documents.publication.*' => ['file', 'mimes:pdf', 'max:10240'],
            'documents.recommendation_letter' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'documents.other' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function academicFileAttributes(): array
    {
        return collect(self::ACADEMIC_DOCUMENT_LABELS)
            ->mapWithKeys(fn ($label, $field) => ["documents.{$field}" => $label])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    protected function privacyConsentMessages(): array
    {
        return [
            'privacy_consent.required' => 'You must consent to the processing of your personal data before saving this profile step.',
            'privacy_consent.accepted' => 'You must consent to the processing of your personal data before saving this profile step.',
        ];
    }
}
