@php $subject = $subject ?? null; @endphp

<div class="row g-3">
    <div class="col-12">
        <x-input-label for="department_id" value="Doctoral Program" />
        @if ($subject)
            <p class="form-control-plaintext fw-semibold" id="department_id">{{ $subject->department->name }}</p>
        @else
            <select id="department_id" name="department_id" class="form-select" required>
                <option value="">Select a doctoral program...</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
        @endif
        <x-input-error :messages="$errors->get('department_id')" />
    </div>

    <div class="col-12">
        <x-input-label for="title" value="Subject title" />
        @if ($subject)
            <p class="form-control-plaintext fw-semibold" id="title">{{ $subject->title }}</p>
        @else
            <x-text-input id="title" name="title" value="{{ old('title') }}" required />
        @endif
        <x-input-error :messages="$errors->get('title')" />
    </div>

    @if ($subject)
        <div class="col-12">
            <div id="content-group-error" class="alert alert-danger d-none mb-0" role="alert">
                Fill in Description, PhD student's responsibilities, Candidate profile, and Keywords together, or leave all four empty.
            </div>
        </div>
    @endif

    <div class="col-12">
        <x-input-label for="description" value="Description" />
        <textarea id="description" name="description" rows="5" class="form-control" required>{{ old('description', $subject?->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" />
    </div>

    <div class="col-12">
        <x-input-label for="responsibilities" value="PhD student's responsibilities" />
        <textarea id="responsibilities" name="responsibilities" rows="3" class="form-control">{{ old('responsibilities', $subject?->responsibilities) }}</textarea>
        <x-input-error :messages="$errors->get('responsibilities')" />
    </div>

    <div class="col-12">
        <x-input-label for="candidate_profile" value="Candidate profile" />
        <textarea id="candidate_profile" name="candidate_profile" rows="3" class="form-control">{{ old('candidate_profile', $subject?->candidate_profile) }}</textarea>
        <x-input-error :messages="$errors->get('candidate_profile')" />
    </div>

    <div class="col-12">
        <x-input-label for="keywords" value="Keywords (comma-separated)" />
        <x-text-input id="keywords" name="keywords" value="{{ old('keywords', $subject?->keywords) }}" placeholder="e.g. machine learning, NLP, data science" />
        <x-input-error :messages="$errors->get('keywords')" />
    </div>

    <div class="col-12">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="is_open" name="is_open" value="1" @checked(old('is_open', $subject?->is_open ?? true))>
            <label class="form-check-label" for="is_open">Open for applications</label>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ route('professor.subjects.index') }}" class="btn btn-outline-secondary">Cancel</a>
    <button type="submit" class="btn btn-primary px-4">
        {{ $subject ? 'Save Changes' : 'Publish Subject' }}
    </button>
</div>
