<x-app-layout title="Apply for a Research Subject">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <p class="text-uppercase small fw-semibold mb-1" style="color:#44A66D;">Applying for</p>
            <h2 class="h4 fw-bold mb-1">{{ $subject->title }}</h2>
            <p class="text-muted mb-0">
                <i class="bi bi-person-badge me-1"></i>{{ $subject->professor->name }}
                &middot; <i class="bi bi-building me-1"></i>{{ $subject->department->name }}
            </p>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <h3 class="h6 fw-bold mb-0"><i class="bi bi-person-check me-2 text-primary"></i>Your Profile</h3>
            <a href="{{ route('candidate.profile.overview') }}" class="btn btn-sm btn-outline-secondary">Edit profile</a>
        </div>
        <div class="card-body">
            <div class="row g-3 small">
                <div class="col-md-6"><span class="text-muted">Full name:</span> {{ $profile->fullName() }}</div>
                <div class="col-md-6"><span class="text-muted">Nationality:</span> {{ $profile->nationality }}</div>
                <div class="col-md-6"><span class="text-muted">Last degree:</span> {{ $profile->last_degree }}</div>
                <div class="col-md-6"><span class="text-muted">Institution:</span> {{ $profile->last_institution }}</div>
            </div>
            @if ($profile->documents->isNotEmpty())
                <hr>
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($profile->documents as $document)
                        <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1 py-2 px-2">
                            <i class="bi {{ $document->type->icon() }}"></i>{{ $document->type->label() }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('candidate.applications.store', $subject) }}" enctype="multipart/form-data">
        @csrf

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h3 class="h6 fw-bold mb-0"><i class="bi bi-envelope-paper me-2 text-primary"></i>Motivation for this Subject</h3>
                <p class="small text-muted mb-0">This is the only part you fill in fresh for each subject.</p>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <x-file-input name="motivation_letter" label="Motivation Letter" required :max-size-mb="5" />
                    </div>
                    <div class="col-12">
                        <x-input-label for="motivation_summary" value="Brief motivation summary (optional)" />
                        <textarea id="motivation_summary" name="motivation_summary" rows="4" class="form-control" placeholder="Briefly summarize why you are interested in this research subject...">{{ old('motivation_summary') }}</textarea>
                        <x-input-error :messages="$errors->get('motivation_summary')" />
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ route('candidate.subjects.show', $subject) }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-success px-4">
                <i class="bi bi-send me-1"></i> Submit Application
            </button>
        </div>
    </form>
</x-app-layout>
