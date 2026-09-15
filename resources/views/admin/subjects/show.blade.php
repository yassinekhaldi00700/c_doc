<x-app-layout title="Subject Details">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <div>
            <span class="badge bg-primary-subtle text-primary mb-2">{{ $subject->department->name }}</span>
            <h2 class="h4 fw-bold mb-0">{{ $subject->title }}</h2>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-outline-secondary">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Delete this subject and all its applications?')">
                    <i class="bi bi-trash me-1"></i>Delete Subject
                </button>
            </form>
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
                        <p class="text-muted mb-0">No applications received yet.</p>
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
                                                    <br>
                                                    <span class="badge bg-success-subtle text-success mt-1">
                                                        <i class="bi bi-star-fill"></i> Professor's pick — suggests {{ optional($application->professor_proposed_exam_at)->format('d M Y') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td><x-status-badge :status="$application->status" /></td>
                                            <td class="text-muted small">{{ $application->submitted_at?->format('d M Y') }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.applications.show', $application) }}" class="btn btn-sm btn-outline-primary">View</a>
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
                    <p class="mb-1"><i class="bi bi-person-badge me-2 text-primary"></i>{{ $subject->professor->name }}</p>
                    <p class="mb-0">
                        <span class="badge {{ $subject->is_open ? 'bg-success' : 'bg-danger' }}">
                            {{ $subject->is_open ? 'Open for applications' : 'Closed' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
