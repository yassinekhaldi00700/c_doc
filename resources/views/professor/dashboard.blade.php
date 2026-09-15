<x-app-layout title="Professor Dashboard">
    <div class="mb-4">
        <a href="{{ route('professor.recruitment.index') }}" class="btn btn-primary">
            <i class="bi bi-file-earmark-word me-2" aria-hidden="true"></i>PV de recrutement
        </a>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="My Subjects" :value="$overview['total_subjects']" icon="bi-journal-richtext" color="primary" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Open Subjects" :value="$overview['open_subjects']" icon="bi-unlock" color="success" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Total Applicants" :value="$overview['total_applicants']" icon="bi-people" color="primary" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Pending Review" :value="$overview['status_counts']['pending'] + $overview['status_counts']['under_review']" icon="bi-hourglass-split" color="secondary" />
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex align-items-center justify-content-between">
                    <h3 class="h6 fw-bold mb-0">Recent Applicants</h3>
                    <a href="{{ route('professor.applications.index') }}" class="small text-decoration-none">View all</a>
                </div>
                <div class="card-body">
                    @if ($overview['recent_applications']->isEmpty())
                        <p class="text-muted mb-0">No applications received yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr class="text-muted small text-uppercase">
                                        <th>Candidate</th>
                                        <th>Subject</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($overview['recent_applications'] as $application)
                                        <tr>
                                            <td>{{ $application->fullName() }}</td>
                                            <td>{{ $application->subject->title }}</td>
                                            <td><x-status-badge :status="$application->status" /></td>
                                            <td class="text-end">
                                                <a href="{{ route('professor.applications.show', $application) }}" class="btn btn-sm btn-outline-primary">Review</a>
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
            <div class="card border-0 shadow-sm hero-gradient text-white h-100">
                <div class="card-body d-flex flex-column">
                    <p class="text-uppercase small fw-semibold mb-2" style="letter-spacing:.15em; color:#eafff0;">Manage</p>
                    <h3 class="h5 fw-bold">Your research subjects</h3>
                    <p class="text-white-50 flex-grow-1">Review and update the description, requirements, and status of the subjects assigned to you. New subjects are added by an administrator.</p>
                    <a href="{{ route('professor.subjects.index') }}" class="btn btn-light mt-3">
                        <i class="bi bi-journal-richtext me-1"></i> View My Subjects
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
