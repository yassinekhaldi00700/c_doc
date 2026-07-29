<x-app-layout title="My Research Subjects">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-10">
                    <x-input-label for="search" value="Search by title" />
                    <x-text-input id="search" type="text" name="search" value="{{ request('search') }}" />
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if ($subjects->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-journal-richtext display-4 text-muted"></i>
                    <p class="text-muted mt-3 mb-0">
                        @if (request('search'))
                            No research subjects match your search.
                        @else
                            No research subjects have been assigned to you yet. Contact an administrator to have one added.
                        @endif
                    </p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th>Title</th>
                                <th>Doctoral Program</th>
                                <th>Applicants</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subjects as $subject)
                                <tr>
                                    <td class="fw-semibold">{{ $subject->title }}</td>
                                    <td>{{ $subject->department->name }}</td>
                                    <td>{{ $subject->applications_count }}</td>
                                    <td>
                                        <span class="badge {{ $subject->is_open ? 'bg-success' : 'bg-danger' }}">
                                            {{ $subject->is_open ? 'Open' : 'Closed' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('professor.subjects.show', $subject) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        <a href="{{ route('professor.subjects.edit', $subject) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $subjects->links() }}
            @endif
        </div>
    </div>
</x-app-layout>
