<x-app-layout title="Manage Doctoral Programs">
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.departments.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-1"></i> New Doctoral Program
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="text-muted small text-uppercase">
                            <th>Name</th>
                            <th>Code</th>
                            <th>Users</th>
                            <th>Subjects</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($departments as $department)
                            <tr>
                                <td class="fw-semibold">{{ $department->name }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $department->code }}</span></td>
                                <td>{{ $department->users_count }}</td>
                                <td>{{ $department->subjects_count }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <form method="POST" action="{{ route('admin.departments.destroy', $department) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this doctoral program?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $departments->links() }}
        </div>
    </div>
</x-app-layout>
