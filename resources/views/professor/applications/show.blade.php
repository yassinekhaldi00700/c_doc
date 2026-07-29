<x-app-layout title="Applicant Review">
    <a href="{{ route('professor.applications.index') }}" class="d-inline-flex align-items-center gap-1 text-decoration-none small fw-semibold mb-3" style="color:#005292;">
        <i class="bi bi-arrow-left"></i> Back to applicants
    </a>

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color:#44A66D;">Application for {{ $application->subject->title }}</p>
            <h2 class="h4 fw-bold mb-0">{{ $application->fullName() }}</h2>
        </div>
        <x-status-badge :status="$application->status" class="fs-6 px-3 py-2" />
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h3 class="h6 fw-bold mb-0"><i class="bi bi-person me-2 text-primary"></i>Personal Information</h3>
                </div>
                <div class="card-body">
                    @php($profile = $application->candidate->profile)
                    <div class="row g-3 small">
                        <div class="col-md-6"><span class="text-muted">Full name:</span> {{ $application->fullName() }}</div>
                        <div class="col-md-6"><span class="text-muted">Email:</span> {{ $application->candidate->email }}</div>
                        <div class="col-md-6"><span class="text-muted">Date of birth:</span> {{ $profile?->birth_date?->format('d M Y') }}</div>
                        <div class="col-md-6"><span class="text-muted">Place of birth:</span> {{ $profile?->birth_place }}</div>
                        <div class="col-md-6"><span class="text-muted">Nationality:</span> {{ $profile?->nationality }}</div>
                        <div class="col-md-6"><span class="text-muted">Gender:</span> {{ $profile?->gender ? ucfirst($profile->gender) : null }}</div>
                        <div class="col-md-6"><span class="text-muted">ID / Passport:</span> {{ $profile?->cin_or_passport_number }}</div>
                        <div class="col-md-6"><span class="text-muted">Phone:</span> {{ $profile?->phone }}</div>
                        <div class="col-md-6"><span class="text-muted">Address:</span> {{ $profile?->address }}</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h3 class="h6 fw-bold mb-0"><i class="bi bi-mortarboard me-2 text-primary"></i>Academic Background</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3 small">
                        <div class="col-md-6"><span class="text-muted">Last degree:</span> {{ $profile?->last_degree }}</div>
                        <div class="col-md-6"><span class="text-muted">Institution:</span> {{ $profile?->last_institution }}</div>
                        <div class="col-md-6"><span class="text-muted">Graduation year:</span> {{ $profile?->graduation_year }}</div>
                        <div class="col-md-6"><span class="text-muted">Field of study:</span> {{ $profile?->field_of_study }}</div>
                        @if ($profile?->grade_mention)
                            <div class="col-md-6"><span class="text-muted">Grade / mention:</span> {{ $profile->grade_mention }}</div>
                        @endif
                        @if ($application->motivation_summary)
                            <div class="col-12 mt-2">
                                <span class="text-muted d-block mb-1">Motivation summary:</span>
                                <p class="mb-0">{{ $application->motivation_summary }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="h6 fw-bold mb-0"><i class="bi bi-file-earmark-pdf me-2 text-primary"></i>Submitted Documents</h3>
                    <p class="small text-muted mb-0">Click a document to preview it directly in your browser.</p>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @forelse ($application->documents as $document)
                            <x-pdf-link :document="$document" />
                        @empty
                            <p class="text-muted mb-0">No documents found.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            @if ($application->review_comment)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h3 class="h6 fw-bold mb-2"><i class="bi bi-chat-square-text me-2 text-primary"></i>Reviewer Comment</h3>
                        <p class="mb-0 small">{{ $application->review_comment }}</p>
                    </div>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="h6 fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Status Timeline</h3>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        @foreach ($application->statusLogs as $log)
                            <li>
                                <p class="small fw-semibold mb-1">{{ $log->to_status->label() }}</p>
                                <p class="small text-muted mb-1">
                                    {{ $log->created_at->format('d M Y, H:i') }}
                                    @if ($log->changedBy) &middot; {{ $log->changedBy->name }} @endif
                                </p>
                                @if ($log->comment)
                                    <p class="small mb-0">{{ $log->comment }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
