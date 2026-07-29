<x-app-layout title="Subject Details">
    <a href="{{ route('candidate.subjects.index') }}" class="d-inline-flex align-items-center gap-1 text-decoration-none small fw-semibold mb-3" style="color:#005292;">
        <i class="bi bi-arrow-left"></i> Back to all subjects
    </a>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <span class="badge bg-primary-subtle text-primary mb-2">{{ $subject->department->name }}</span>
                    @if (! $subject->is_open)
                        <span class="badge bg-secondary mb-2">Closed</span>
                    @endif
                    <h2 class="h3 fw-bold mb-3">{{ $subject->title }}</h2>

                    <h3 class="h6 fw-bold text-uppercase text-muted mb-2">Description</h3>
                    <p class="mb-4">{{ $subject->description }}</p>

                    @if ($subject->responsibilities)
                        <h3 class="h6 fw-bold text-uppercase text-muted mb-2">PhD Student's Responsibilities</h3>
                        <p class="mb-4">{{ $subject->responsibilities }}</p>
                    @endif

                    @if ($subject->candidate_profile)
                        <h3 class="h6 fw-bold text-uppercase text-muted mb-2">Candidate Profile</h3>
                        <p class="mb-4">{{ $subject->candidate_profile }}</p>
                    @endif

                    @if ($subject->keywords)
                        <h3 class="h6 fw-bold text-uppercase text-muted mb-2">Keywords</h3>
                        <p class="mb-0">
                            @foreach (explode(',', $subject->keywords) as $keyword)
                                <span class="badge bg-light text-dark border me-1">{{ trim($keyword) }}</span>
                            @endforeach
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-3">Supervisor</h3>
                    <p class="mb-1"><i class="bi bi-person-badge me-2 text-primary"></i>{{ $subject->professor->name }}</p>
                    <p class="mb-1 small text-muted"><i class="bi bi-envelope me-2"></i>{{ $subject->professor->email }}</p>
                    <hr>
                    <p class="mb-0"><i class="bi bi-building me-2 text-primary"></i>{{ $subject->department->name }}</p>
                </div>
            </div>

            @if (auth()->user()->isCandidate())
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        @if ($alreadyApplied)
                            <p class="text-success mb-3"><i class="bi bi-check-circle-fill me-1"></i>You already applied to this subject.</p>
                            <a href="{{ route('candidate.applications.index') }}" class="btn btn-outline-primary w-100">View My Applications</a>
                        @elseif (! $subject->is_open)
                            <p class="text-muted mb-0">This subject is no longer accepting applications.</p>
                        @elseif (! $profileComplete)
                            <p class="text-muted small mb-2">Complete your profile before applying.</p>
                            <a href="{{ route('candidate.applications.create', $subject) }}" class="btn btn-warning w-100 py-2">
                                <i class="bi bi-person-badge me-1"></i> Complete Profile to Apply
                            </a>
                        @else
                            <a href="{{ route('candidate.applications.create', $subject) }}" class="btn btn-success w-100 py-2">
                                <i class="bi bi-send me-1"></i> Apply for this Subject
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
