<x-app-layout title="My Profile">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <p class="text-uppercase small fw-semibold mb-1" style="color:#44A66D;">Candidate Profile</p>
            <h2 class="h4 fw-bold mb-1">Complete your profile</h2>
            <p class="text-muted mb-0">Fill this in once — it's reused for every subject you apply to. Only a motivation letter needs to be provided separately for each application.</p>
        </div>
    </div>

    @if ($profile->isComplete())
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <div>
                Your profile is complete
                @if ($profile->completed_at)
                    <span class="text-muted">(since {{ $profile->completed_at->format('d M Y') }})</span>
                @endif
                — you're ready to apply to any subject.
            </div>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h3 class="h6 fw-bold mb-0"><i class="bi bi-person me-2 text-primary"></i>Personal Information</h3>
                        @if ($profile->personalComplete())
                            <span class="badge bg-success-subtle text-success"><i class="bi bi-check-lg"></i></span>
                        @else
                            <span class="badge bg-warning-subtle text-warning">Incomplete</span>
                        @endif
                    </div>
                    <p class="small text-muted flex-grow-1">Name, birth details, nationality, contact address, and your National ID / Passport.</p>
                    <a href="{{ route('candidate.profile.personal.edit') }}" hx-boost="true" class="btn btn-sm btn-outline-primary">
                        {{ $profile->personalComplete() ? 'Edit' : 'Complete' }}
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h3 class="h6 fw-bold mb-0"><i class="bi bi-mortarboard me-2 text-primary"></i>Academic Background</h3>
                        @if ($profile->academicComplete())
                            <span class="badge bg-success-subtle text-success"><i class="bi bi-check-lg"></i></span>
                        @else
                            <span class="badge bg-warning-subtle text-warning">Incomplete</span>
                        @endif
                    </div>
                    <p class="small text-muted flex-grow-1">Your degree track, Last Diploma, License and Baccalaureat records with their certificates, transcripts, CV, and optional documents.</p>
                    <a href="{{ route('candidate.profile.academic.edit') }}" hx-boost="true" class="btn btn-sm btn-outline-primary">
                        {{ $profile->academicComplete() ? 'Edit' : 'Complete' }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    @unless ($profile->isComplete())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h3 class="h6 fw-bold mb-2"><i class="bi bi-list-check me-2 text-warning"></i>Still missing</h3>
                <ul class="mb-0 small">
                    @foreach ($profile->missingRequirements() as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endunless

    @if ($profile->isComplete())
        <div class="d-flex justify-content-end">
            @if ($intendedSubject)
                <a href="{{ route('candidate.applications.create', $intendedSubject) }}" class="btn btn-success px-4">
                    <i class="bi bi-send me-1"></i> Continue applying to &ldquo;{{ $intendedSubject->title }}&rdquo;
                </a>
            @else
                <a href="{{ route('candidate.subjects.index') }}" class="btn btn-success px-4">
                    <i class="bi bi-search me-1"></i> Browse subjects to apply
                </a>
            @endif
        </div>
    @endif
</x-app-layout>
