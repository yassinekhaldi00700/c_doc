<x-app-layout title="Admin Dashboard">
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Total Users" :value="$overview['total_users']" icon="bi-people" color="primary" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Candidates" :value="$overview['total_candidates']" icon="bi-person-workspace" color="success" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Professors" :value="$overview['total_professors']" icon="bi-person-badge" color="primary" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Doctoral Programs" :value="$overview['total_departments']" icon="bi-building" color="secondary" />
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Research Subjects" :value="$overview['total_subjects']" icon="bi-journal-richtext" color="primary" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Open Subjects" :value="$overview['open_subjects']" icon="bi-unlock" color="success" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Total Applications" :value="$overview['total_applications']" icon="bi-file-earmark-text" color="primary" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Pending Review" :value="$overview['status_counts']['pending'] + $overview['status_counts']['under_review']" icon="bi-hourglass-split" color="secondary" />
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h3 class="h6 fw-bold mb-0">Applications by Status</h3>
                </div>
                <div class="card-body">
                    @foreach ($overview['status_counts'] as $statusValue => $count)
                        @php $status = \App\Enums\ApplicationStatus::from($statusValue); @endphp
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <x-status-badge :status="$status" />
                            <span class="fw-bold">{{ $count }}</span>
                        </div>
                        @php $total = max(1, $overview['total_applications']); @endphp
                        <div class="progress mb-3" style="height:6px;">
                            <div class="progress-bar {{ $status->badgeClass() }}" style="width: {{ $count / $total * 100 }}%"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h3 class="h6 fw-bold mb-0">Applications per Doctoral Program</h3>
                </div>
                <div class="card-body">
                    @if ($overview['applications_per_department']->isEmpty())
                        <p class="text-muted mb-0">No applications submitted yet.</p>
                    @else
                        @php $maxTotal = $overview['applications_per_department']->max('total') ?: 1; @endphp
                        @foreach ($overview['applications_per_department'] as $row)
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="small">{{ $row->department }}</span>
                                <span class="small fw-bold">{{ $row->total }}</span>
                            </div>
                            <div class="progress mb-3" style="height:6px;">
                                <div class="progress-bar bg-primary" style="width: {{ $row->total / $maxTotal * 100 }}%"></div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <h3 class="h6 fw-bold mb-0">Recent Applications</h3>
            <a href="{{ route('admin.applications.index') }}" class="small text-decoration-none">View all</a>
        </div>
        <div class="card-body">
            @if ($overview['recent_applications']->isEmpty())
                <p class="text-muted mb-0">No applications submitted yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th>Candidate</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($overview['recent_applications'] as $application)
                                <tr>
                                    <td>{{ $application->fullName() }}</td>
                                    <td>{{ $application->subject->title }}</td>
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
            @endif
        </div>
    </div>
</x-app-layout>
