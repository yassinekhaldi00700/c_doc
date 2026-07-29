<x-app-layout title="Applicants">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <x-input-label for="search" value="Search by name" />
                    <x-text-input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Candidate name..." />
                </div>
                <div class="col-md-3">
                    <x-input-label for="subject_id" value="Research subject" />
                    <select id="subject_id" name="subject_id" class="form-select">
                        <option value="">All subjects</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected(request('subject_id') == $subject->id)>{{ $subject->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status" class="form-select">
                        <option value="">All statuses</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if ($applications->isEmpty())
                <p class="text-muted mb-0 text-center py-4">No applicants match your filters.</p>
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
                            @foreach ($applications as $application)
                                <tr>
                                    <td>{{ $application->fullName() }}</td>
                                    <td>{{ $application->subject->title }}</td>
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
</x-app-layout>
