<x-app-layout title="Application Details">
    <a href="{{ route('admin.applications.index') }}" class="d-inline-flex align-items-center gap-1 text-decoration-none small fw-semibold mb-3" style="color:#005292;">
        <i class="bi bi-arrow-left"></i> Back to all applications
    </a>

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color:#44A66D;">Application for {{ $application->subject->title }}</p>
            <h2 class="h4 fw-bold mb-0">{{ $application->fullName() }}</h2>
            @if ($application->oral_exam_at)
                <p class="small text-muted mb-0"><i class="bi bi-calendar-event me-1"></i>Oral exam: {{ $application->oral_exam_at->format('d M Y, H:i') }}</p>
            @endif
            @if ($application->program_start_at)
                <p class="small text-muted mb-0"><i class="bi bi-calendar-check me-1"></i>Program start: {{ $application->program_start_at->format('d M Y') }}</p>
            @endif
        </div>
        <x-status-badge :status="$application->status" class="fs-6 px-3 py-2" />
    </div>

    @if ($application->notification_sent_at)
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="bi bi-envelope-check-fill"></i>
            <span>Notification email sent to the candidate on {{ $application->notification_sent_at->format('d M Y, H:i') }}.</span>
        </div>
    @elseif ($application->notification_error)
        <div class="alert alert-danger mb-4" role="alert">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-envelope-exclamation-fill mt-1"></i>
                    <div>
                        <div class="fw-semibold">The notification email failed to send.</div>
                        <div class="small text-muted">{{ $application->notification_error }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.applications.resend-notification', $application) }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-arrow-clockwise me-1"></i>Resend</button>
                </form>
            </div>
        </div>
    @elseif ($application->notificationPending())
        <div class="alert alert-warning d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4" role="alert">
            <span><i class="bi bi-hourglass-split me-2"></i>Notification email hasn't been sent yet.</span>
            <form method="POST" action="{{ route('admin.applications.resend-notification', $application) }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-warning"><i class="bi bi-arrow-clockwise me-1"></i>Send now</button>
            </form>
        </div>
    @endif

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
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-2">Research Subject</h3>
                    <p class="mb-1">{{ $application->subject->title }}</p>
                    <p class="small text-muted mb-1">Supervised by {{ $application->subject->professor->name }}</p>
                    <p class="small text-muted mb-0">{{ $application->subject->department->name }}</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h3 class="h6 fw-bold mb-0"><i class="bi bi-clipboard-check me-2 text-primary"></i>Review Decision</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.applications.review', $application) }}">
                        @csrf
                        @method('PATCH')

                        @php($selectedStatus = old('status', $application->status->value))

                        <div class="mb-3">
                            <x-input-label for="status" value="New status" />
                            <select id="status" name="status" class="form-select" required>
                                @foreach ($statuses as $status)
                                    @if ($status->value !== 'pending')
                                        <option value="{{ $status->value }}" @selected($selectedStatus === $status->value)>{{ $status->label() }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status')" />
                        </div>

                        <div class="mb-3 {{ $selectedStatus === 'under_review' ? '' : 'd-none' }}" id="oral-exam-date-field">
                            <x-input-label for="oral_exam_date" value="Oral exam date & time" />
                            <x-text-input id="oral_exam_date" type="datetime-local" name="oral_exam_date"
                                value="{{ old('oral_exam_date', optional($application->oral_exam_at)->format('Y-m-d\TH:i')) }}"
                                min="{{ now()->format('Y-m-d\TH:i') }}" :required="$selectedStatus === 'under_review'" />
                            <x-input-error :messages="$errors->get('oral_exam_date')" />
                        </div>

                        <div class="mb-3 {{ $selectedStatus === 'accepted' ? '' : 'd-none' }}" id="program-start-date-field">
                            <x-input-label for="program_start_date" value="Program start date" />
                            <x-text-input id="program_start_date" type="date" name="program_start_date"
                                value="{{ old('program_start_date', optional($application->program_start_at)->format('Y-m-d')) }}"
                                min="{{ now()->toDateString() }}" :required="$selectedStatus === 'accepted'" />
                            <x-input-error :messages="$errors->get('program_start_date')" />
                        </div>

                        <div class="mb-3">
                            <x-input-label for="comment" value="Comment (optional)" />
                            <textarea id="comment" name="comment" rows="4" class="form-control" placeholder="Share feedback with the candidate...">{{ old('comment') }}</textarea>
                            <x-input-error :messages="$errors->get('comment')" />
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check2-circle me-1"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>

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
