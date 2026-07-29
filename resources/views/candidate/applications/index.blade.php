<x-app-layout title="My Applications">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if ($applications->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-file-earmark-text display-4 text-muted"></i>
                    <p class="text-muted mt-3 mb-3">You haven't submitted any applications yet.</p>
                    <a href="{{ route('candidate.subjects.index') }}" class="btn btn-primary">Browse Research Subjects</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th>Subject</th>
                                <th>Professor</th>
                                <th>Doctoral Program</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($applications as $application)
                                <tr>
                                    <td class="fw-semibold">{{ $application->subject->title }}</td>
                                    <td>{{ $application->subject->professor->name }}</td>
                                    <td>{{ $application->subject->department->name }}</td>
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

                {{ $applications->links() }}
            @endif
        </div>
    </div>
</x-app-layout>
