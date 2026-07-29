<x-app-layout title="Candidate Dashboard">
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Total Applications" :value="$overview['total_applications']" icon="bi-file-earmark-text" color="primary" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Pending" :value="$overview['status_counts']['pending']" icon="bi-hourglass-split" color="secondary" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Accepted for Oral Exam" :value="$overview['status_counts']['under_review']" icon="bi-search" color="primary" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Accepted" :value="$overview['status_counts']['accepted']" icon="bi-check-circle-fill" color="success" />
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex align-items-center justify-content-between">
                    <h3 class="h6 fw-bold mb-0">My Applications</h3>
                    <a href="{{ route('candidate.applications.index') }}" class="small text-decoration-none">View all</a>
                </div>
                <div class="card-body">
                    @if ($overview['applications']->isEmpty())
                        <p class="text-muted mb-0">You haven't submitted any applications yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr class="text-muted small text-uppercase">
                                        <th>Subject</th>
                                        <th>Status</th>
                                        <th>Submitted</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($overview['applications']->take(5) as $application)
                                        <tr>
                                            <td>{{ $application->subject->title }}</td>
                                            <td><x-status-badge :status="$application->status" /></td>
                                            <td class="text-muted small">{{ $application->submitted_at?->format('d M Y') }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('candidate.applications.show', $application) }}" class="btn btn-sm btn-outline-primary">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            @if (! $overview['profile_complete'])
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <p class="text-uppercase small fw-semibold mb-2 text-warning" style="letter-spacing:.15em;">Next step</p>
                        <h3 class="h5 fw-bold"><i class="bi bi-person-badge me-2 text-warning"></i>Complete your profile</h3>
                        <p class="text-muted">You must complete your profile before you can apply to any subject.</p>
                        <ul class="small text-muted mb-3">
                            @foreach ($overview['profile_missing'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                        <a href="{{ route('candidate.profile.overview') }}" class="btn btn-warning mt-auto">
                            <i class="bi bi-arrow-right-circle me-1"></i> Complete Profile
                        </a>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm hero-gradient text-white h-100">
                    <div class="card-body d-flex flex-column">
                        <p class="text-uppercase small fw-semibold mb-2" style="letter-spacing:.15em; color:#eafff0;">Next step</p>
                        <h3 class="h5 fw-bold">Explore open research subjects</h3>
                        <p class="text-white-50 flex-grow-1">Browse subjects proposed by our professors and submit your doctoral application with all required documents.</p>
                        <a href="{{ route('candidate.subjects.index') }}" class="btn btn-light mt-3">
                            <i class="bi bi-search me-1"></i> Browse Subjects
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
