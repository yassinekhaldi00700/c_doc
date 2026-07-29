@php
    $byType = $profile->documents->groupBy(fn ($document) => $document->type->value);
    $has = fn (string $type) => $byType->has($type);
    $track = old('degree_track', $profile->degree_track?->value);
@endphp
<x-app-layout title="Academic Background">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <p class="text-uppercase small fw-semibold mb-1" style="color:#44A66D;">Candidate Profile</p>
            <h2 class="h4 fw-bold mb-1">Academic Background</h2>
            <p class="text-muted mb-0">This information is reused for every subject you apply to. You can save partial progress and come back to finish it later.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('candidate.profile.academic.update') }}" enctype="multipart/form-data" novalidate
          hx-boost="true" hx-encoding="multipart/form-data" hx-target="body" hx-swap="outerHTML">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <p class="fw-semibold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Please fix the following before this step can be saved:</p>
                <ul class="mb-0 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h3 class="h6 fw-bold mb-0"><i class="bi bi-signpost-split me-2 text-primary"></i>Degree Track</h3>
                <p class="small text-muted mb-0">Choose the track that matches your current profile.</p>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="radio" class="btn-check" name="degree_track" id="track_master" value="master" autocomplete="off" @checked($track === 'master')>
                        <label class="degree-track-card" for="track_master">
                            <span class="degree-track-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                                <i class="bi bi-mortarboard fs-4"></i>
                            </span>
                            <span class="flex-grow-1">
                                <span class="fw-bold d-block mb-1">Master</span>
                                <span class="small text-muted">License / Bachelor's degree is required as your prior qualification.</span>
                            </span>
                            <i class="bi bi-check-circle-fill degree-track-check"></i>
                        </label>
                    </div>
                    <div class="col-md-6">
                        <input type="radio" class="btn-check" name="degree_track" id="track_engineer" value="engineer" autocomplete="off" @checked($track === 'engineer')>
                        <label class="degree-track-card" for="track_engineer">
                            <span class="degree-track-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                                <i class="bi bi-gear fs-4"></i>
                            </span>
                            <span class="flex-grow-1">
                                <span class="fw-bold d-block mb-1">Engineering Degree</span>
                                <span class="small text-muted">License / Bachelor's degree is optional for this track.</span>
                            </span>
                            <i class="bi bi-check-circle-fill degree-track-check"></i>
                        </label>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('degree_track')" class="mt-2" />
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h3 class="h6 fw-bold mb-0"><i class="bi bi-award me-2 text-primary"></i>Last Diploma</h3>
                <p class="small text-muted mb-0">Your most advanced degree obtained so far.</p>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <x-input-label for="last_degree" value="Last degree obtained" />
                        <x-text-input id="last_degree" name="last_degree" value="{{ old('last_degree', $profile->last_degree) }}" placeholder="e.g. Master's degree in Computer Science" required />
                        <x-input-error :messages="$errors->get('last_degree')" />
                    </div>
                    <div class="col-md-6">
                        <x-input-label for="last_institution" value="Institution" />
                        <x-text-input id="last_institution" name="last_institution" value="{{ old('last_institution', $profile->last_institution) }}" required />
                        <x-input-error :messages="$errors->get('last_institution')" />
                    </div>
                    <div class="col-md-4">
                        <x-input-label for="graduation_year" value="Graduation year" />
                        <x-text-input id="graduation_year" type="number" name="graduation_year" value="{{ old('graduation_year', $profile->graduation_year) }}" min="1980" max="{{ now()->year }}" required />
                        <x-input-error :messages="$errors->get('graduation_year')" />
                    </div>
                    <div class="col-md-4">
                        <x-input-label for="field_of_study" value="Field of study" />
                        <x-text-input id="field_of_study" name="field_of_study" value="{{ old('field_of_study', $profile->field_of_study) }}" required />
                        <x-input-error :messages="$errors->get('field_of_study')" />
                    </div>
                    <div class="col-md-4">
                        <x-input-label for="grade_mention" value="Grade / mention (optional)" />
                        <x-text-input id="grade_mention" name="grade_mention" value="{{ old('grade_mention', $profile->grade_mention) }}" placeholder="e.g. Très Bien" />
                        <x-input-error :messages="$errors->get('grade_mention')" />
                    </div>
                    <div class="col-12">
                        <hr>
                        @if ($has('last_diploma_certificate'))
                            @foreach ($byType->get('last_diploma_certificate', []) as $document)
                                <x-profile-document-chip :document="$document" />
                            @endforeach
                        @endif
                        <x-file-input name="last_diploma_certificate" label="Last Diploma Certificate" required :max-size-mb="10"
                            :hint="$has('last_diploma_certificate') ? 'Choose a new file to replace the one above.' : null" />
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h3 class="h6 fw-bold mb-0"><i class="bi bi-mortarboard me-2 text-primary"></i>License / Bachelor's Degree</h3>
                <p class="small text-muted mb-0">Required if your track is Master, optional if your track is Engineering Degree.</p>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <x-input-label for="license_institution" value="Institution" />
                        <x-text-input id="license_institution" name="license_institution" value="{{ old('license_institution', $profile->license_institution) }}" :required="$track === 'master'" data-required-for-track="master" />
                        <x-input-error :messages="$errors->get('license_institution')" />
                    </div>
                    <div class="col-md-6">
                        <x-input-label for="license_field_of_study" value="Field of study" />
                        <x-text-input id="license_field_of_study" name="license_field_of_study" value="{{ old('license_field_of_study', $profile->license_field_of_study) }}" :required="$track === 'master'" data-required-for-track="master" />
                        <x-input-error :messages="$errors->get('license_field_of_study')" />
                    </div>
                    <div class="col-md-6">
                        <x-input-label for="license_graduation_year" value="Graduation year" />
                        <x-text-input id="license_graduation_year" type="number" name="license_graduation_year" value="{{ old('license_graduation_year', $profile->license_graduation_year) }}" min="1980" max="{{ now()->year }}" :required="$track === 'master'" data-required-for-track="master" />
                        <x-input-error :messages="$errors->get('license_graduation_year')" />
                    </div>
                    <div class="col-md-6">
                        <x-input-label for="license_grade_mention" value="Grade / mention (optional)" />
                        <x-text-input id="license_grade_mention" name="license_grade_mention" value="{{ old('license_grade_mention', $profile->license_grade_mention) }}" placeholder="e.g. Bien" />
                        <x-input-error :messages="$errors->get('license_grade_mention')" />
                    </div>
                    <div class="col-12">
                        <hr>
                        @if ($has('license_certificate'))
                            @foreach ($byType->get('license_certificate', []) as $document)
                                <x-profile-document-chip :document="$document" />
                            @endforeach
                        @endif
                        <x-file-input name="license_certificate" label="License / Bachelor Certificate" :required="$track === 'master'" :max-size-mb="10"
                            data-required-for-track="master"
                            hint="Required for the Master track, optional for the Engineering Degree track." />
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h3 class="h6 fw-bold mb-0"><i class="bi bi-patch-check me-2 text-primary"></i>Baccalaureat</h3>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <x-input-label for="bac_institution" value="Institution" />
                        <x-text-input id="bac_institution" name="bac_institution" value="{{ old('bac_institution', $profile->bac_institution) }}" required />
                        <x-input-error :messages="$errors->get('bac_institution')" />
                    </div>
                    <div class="col-md-6">
                        <x-input-label for="bac_field_of_study" value="Field of study" />
                        <x-text-input id="bac_field_of_study" name="bac_field_of_study" value="{{ old('bac_field_of_study', $profile->bac_field_of_study) }}" placeholder="e.g. Sciences Mathématiques" required />
                        <x-input-error :messages="$errors->get('bac_field_of_study')" />
                    </div>
                    <div class="col-md-6">
                        <x-input-label for="bac_graduation_year" value="Graduation year" />
                        <x-text-input id="bac_graduation_year" type="number" name="bac_graduation_year" value="{{ old('bac_graduation_year', $profile->bac_graduation_year) }}" min="1980" max="{{ now()->year }}" required />
                        <x-input-error :messages="$errors->get('bac_graduation_year')" />
                    </div>
                    <div class="col-md-6">
                        <x-input-label for="bac_grade_mention" value="Grade / mention (optional)" />
                        <x-text-input id="bac_grade_mention" name="bac_grade_mention" value="{{ old('bac_grade_mention', $profile->bac_grade_mention) }}" placeholder="e.g. Assez Bien" />
                        <x-input-error :messages="$errors->get('bac_grade_mention')" />
                    </div>
                    <div class="col-12">
                        <hr>
                        @if ($has('baccalaureat_certificate'))
                            @foreach ($byType->get('baccalaureat_certificate', []) as $document)
                                <x-profile-document-chip :document="$document" />
                            @endforeach
                        @endif
                        <x-file-input name="baccalaureat_certificate" label="Baccalaureat Certificate" required :max-size-mb="10"
                            :hint="$has('baccalaureat_certificate') ? 'Choose a new file to replace the one above.' : null" />
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h3 class="h6 fw-bold mb-0"><i class="bi bi-file-earmark-pdf me-2 text-primary"></i>Transcripts &amp; CV</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        @foreach ($byType->get('transcript', []) as $document)
                            <x-profile-document-chip :document="$document" />
                        @endforeach
                        <x-file-input name="transcript" label="Academic Transcripts" required multiple :max-size-mb="10"
                            hint="You may select multiple files (e.g. one per academic year)." />
                    </div>
                    <div class="col-md-6">
                        @foreach ($byType->get('cv', []) as $document)
                            <x-profile-document-chip :document="$document" />
                        @endforeach
                        <x-file-input name="cv" label="Curriculum Vitae (CV) obligator photo" required :max-size-mb="5"
                            :hint="$has('cv') ? 'Choose a new file to replace the one above.' : null" />
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h3 class="h6 fw-bold mb-0"><i class="bi bi-file-earmark-plus me-2 text-primary"></i>Optional Documents</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        @foreach ($byType->get('publication', []) as $document)
                            <x-profile-document-chip :document="$document" />
                        @endforeach
                        <x-file-input name="publication" label="Publications" multiple :max-size-mb="10" hint="Scientific articles, conference papers, etc." />
                    </div>
                    <div class="col-md-6">
                        @foreach ($byType->get('recommendation_letter', []) as $document)
                            <x-profile-document-chip :document="$document" />
                        @endforeach
                        <x-file-input name="recommendation_letter" label="Recommendation Letter" :max-size-mb="5"
                            :hint="$has('recommendation_letter') ? 'Choose a new file to replace the one above.' : null" />
                    </div>
                    <div class="col-md-6">
                        @foreach ($byType->get('other', []) as $document)
                            <x-profile-document-chip :document="$document" />
                        @endforeach
                        <x-file-input name="other" label="Other Supporting Document" :max-size-mb="10"
                            :hint="$has('other') ? 'Choose a new file to replace the one above.' : null" />
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ route('candidate.profile.overview') }}" hx-boost="true" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-success px-4">
                <i class="bi bi-save me-1"></i> Save & Continue
            </button>
        </div>
    </form>
</x-app-layout>
