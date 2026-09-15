<x-app-layout title="Subject Details">
    <a href="{{ route('professor.subjects.index') }}" class="d-inline-flex align-items-center gap-1 text-decoration-none small fw-semibold mb-3" style="color:#005292;">
        <i class="bi bi-arrow-left"></i> Back to my research subjects
    </a>

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <div>
            <span class="badge bg-primary-subtle text-primary mb-2">{{ $subject->department->name }}</span>
            <h2 class="h4 fw-bold mb-0">{{ $subject->title }}</h2>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('professor.oral-exam-picks.edit', $subject) }}" class="btn btn-outline-success">
                <i class="bi bi-star me-1"></i>Oral Exam Picks
            </a>
            <a href="{{ route('professor.subjects.edit', $subject) }}" class="btn btn-outline-secondary">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#delete-subject-modal">
                <i class="bi bi-trash me-1"></i>Delete
            </button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="h6 fw-bold text-uppercase text-muted mb-2">Description</h3>
                    <p class="mb-3">{{ $subject->description }}</p>

                    @if ($subject->responsibilities)
                        <h3 class="h6 fw-bold text-uppercase text-muted mb-2">PhD Student's Responsibilities</h3>
                        <p class="mb-3">{{ $subject->responsibilities }}</p>
                    @endif

                    @if ($subject->candidate_profile)
                        <h3 class="h6 fw-bold text-uppercase text-muted mb-2">Candidate Profile</h3>
                        <p class="mb-3">{{ $subject->candidate_profile }}</p>
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

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="h6 fw-bold mb-0">Applicants</h3>
                </div>
                <div class="card-body">
                    @if ($applications->isEmpty())
                        <p class="text-muted mb-0">No applications received yet for this subject.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr class="text-muted small text-uppercase">
                                        <th>Candidate</th>
                                        <th>Status</th>
                                        <th>Submitted</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($applications as $application)
                                        <tr>
                                            <td>
                                                {{ $application->fullName() }}
                                                @if ($application->professor_favorited_at)
                                                    <span class="badge bg-success-subtle text-success ms-1"><i class="bi bi-star-fill"></i> Picked</span>
                                                @endif
                                            </td>
                                            <td><x-status-badge :status="$application->status" /></td>
                                            <td class="text-muted small">{{ $application->submitted_at?->format('d M Y') }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('professor.applications.show', $application) }}" class="btn btn-sm btn-outline-primary">Review</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $applications->links() }}
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-3">Subject Info</h3>
                    <p class="mb-1"><i class="bi bi-hash me-2 text-primary"></i>{{ $applications->total() }} applicant(s)</p>
                    <p class="mb-0">
                        <span class="badge {{ $subject->is_open ? 'bg-success' : 'bg-danger' }}">
                            {{ $subject->is_open ? 'Open for applications' : 'Closed' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <x-modal id="delete-subject-modal" title="Delete this research subject?">
        <p class="mb-0">This will permanently delete "{{ $subject->title }}" and all associated applications. This action cannot be undone.</p>
        <x-slot name="footer">
            <x-secondary-button data-bs-dismiss="modal">Cancel</x-secondary-button>
            <form method="POST" action="{{ route('professor.subjects.destroy', $subject) }}">
                @csrf
                @method('DELETE')
                <x-danger-button>Delete Permanently</x-danger-button>
            </form>
        </x-slot>
    </x-modal>
</x-app-layout>
