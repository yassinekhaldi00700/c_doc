<x-app-layout title="Browse Research Subjects">
    @if ($applicationsPaused)
        <div class="alert alert-warning d-flex align-items-start gap-2" role="status">
            <i class="bi bi-pause-circle-fill mt-1" aria-hidden="true"></i>
            <div>
                <strong>Applications are temporarily paused.</strong>
                Open subjects remain available to browse, but applications cannot be submitted right now.
            </div>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <x-input-label for="search" value="Search by title" />
                    <x-text-input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="e.g. Machine Learning..." />
                </div>
                <div class="col-md-4">
                    <x-input-label for="department_id" value="Doctoral Program" />
                    <select id="department_id" name="department_id" class="form-select">
                        <option value="">All doctoral programs</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" @selected(request('department_id') == $department->id)>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    @if ($subjects->isEmpty())
        <div class="alert alert-info">No research subjects match your search criteria.</div>
    @else
        <div class="row g-4">
            @foreach ($subjects as $subject)
                <div class="col-md-6 col-lg-4">
                    <div class="subject-card card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <span class="badge bg-primary-subtle text-primary mb-2 align-self-start">{{ $subject->department->name }}</span>
                            <h3 class="h5 fw-bold mb-2">{{ $subject->title }}</h3>
                            <p class="text-muted small mb-3 flex-grow-1">{{ \Illuminate\Support\Str::limit($subject->description, 110) }}</p>
                            <p class="small text-muted mb-3">
                                <i class="bi bi-person-badge me-1"></i>{{ $subject->professor->name }}
                            </p>
                            <a href="{{ route('candidate.subjects.show', $subject) }}" class="btn btn-outline-primary btn-sm mt-auto">View Details</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $subjects->links() }}
        </div>
    @endif
</x-app-layout>
