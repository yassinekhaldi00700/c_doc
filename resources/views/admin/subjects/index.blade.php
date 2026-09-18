<x-app-layout title="Research Subjects">
    <div class="alert {{ $applicationsPaused ? 'alert-warning' : 'alert-success' }} d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4" role="status">
        <div>
            <h2 class="h6 fw-bold mb-1">
                <i class="bi {{ $applicationsPaused ? 'bi-pause-circle-fill' : 'bi-play-circle-fill' }} me-1"></i>
                Application submissions are {{ $applicationsPaused ? 'paused' : 'active' }}
            </h2>
            <p class="small mb-0">
                @if ($applicationsPaused)
                    Candidates can see open subjects, but they cannot apply until submissions are resumed.
                @else
                    Candidates can currently apply to any subject marked Open.
                @endif
            </p>
        </div>
        <form method="POST" action="{{ route('admin.subjects.toggle-application-access') }}" class="flex-shrink-0">
            @csrf
            @method('PATCH')
            <button
                type="submit"
                class="btn {{ $applicationsPaused ? 'btn-success' : 'btn-warning' }}"
                @unless ($applicationsPaused) onclick="return confirm('Pause all new applications? Open subjects will remain visible, but candidates will not be able to apply.')" @endunless
            >
                <i class="bi {{ $applicationsPaused ? 'bi-play-fill' : 'bi-pause-fill' }} me-1"></i>
                {{ $applicationsPaused ? 'Resume applications' : 'Pause all applications' }}
            </button>
        </form>
    </div>

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> New Subject
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <x-input-label for="search" value="Search by title" />
                    <x-text-input id="search" type="text" name="search" value="{{ request('search') }}" />
                </div>
                <div class="col-md-3">
                    <x-input-label for="professor_id" value="Professor" />
                    <select id="professor_id" name="professor_id" class="form-select">
                        <option value="">All professors</option>
                        @foreach ($professors as $professor)
                            <option value="{{ $professor->id }}" @selected(request('professor_id') == $professor->id)>{{ $professor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <x-input-label for="department_id" value="Doctoral Program" />
                    <select id="department_id" name="department_id" class="form-select">
                        <option value="">All doctoral programs</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" @selected(request('department_id') == $department->id)>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status" class="form-select">
                        <option value="">Open &amp; Closed</option>
                        <option value="open" @selected(request('status') === 'open')>Open</option>
                        <option value="closed" @selected(request('status') === 'closed')>Closed</option>
                    </select>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-funnel me-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="text-muted small text-uppercase">
                            <th>Title</th>
                            <th>Professor</th>
                            <th>Doctoral Program</th>
                            <th>Applicants</th>
                            <th>Prof. Picks</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subjects as $subject)
                            <tr>
                                <td class="fw-semibold">{{ $subject->title }}</td>
                                <td>{{ $subject->professor->name }}</td>
                                <td>{{ $subject->department->name }}</td>
                                <td>{{ $subject->applications_count }}</td>
                                <td>
                                    @if ($subject->professor_picks_count > 0)
                                        <span class="badge bg-success-subtle text-success" title="The professor picked {{ $subject->professor_picks_count }} candidate(s) for the oral exam">
                                            <i class="bi bi-star-fill"></i> {{ $subject->professor_picks_count }}
                                        </span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $subject->is_open ? 'bg-success' : 'bg-danger' }}">
                                        {{ $subject->is_open ? 'Open' : 'Closed' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.subjects.show', $subject) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this subject and all its applications?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $subjects->links() }}
        </div>
    </div>
</x-app-layout>
