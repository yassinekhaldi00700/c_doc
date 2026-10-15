<x-app-layout title="Bulk Add Research Subjects">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h2 class="h5 fw-bold mb-3">Bulk Add Research Subjects</h2>

            <p class="text-muted">
                Upload a CSV with one subject per row. As with a single subject, only the title, professor, and
                doctoral program are set here — the professor fills in the description and the rest themselves.
            </p>

            <div class="card bg-light border-0 mb-4">
                <div class="card-body">
                    <h3 class="h6 fw-semibold mb-2">Required columns</h3>
                    <p class="mb-2 small text-muted">The first row must be a header with these column names (any order):</p>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0 bg-white">
                            <thead>
                                <tr><th>title</th><th>professor_email</th><th>doctoral_program</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>Machine Learning for Climate Modeling</td><td>jane.doe@example.com</td><td>CS</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="small text-muted mb-0 mt-2">
                        <code>professor_email</code> must match an existing professor account's email.
                        <code>doctoral_program</code> must match an existing doctoral program's <strong>code</strong>
                        (see <a href="{{ route('admin.departments.index') }}">Doctoral Programs</a>).
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.subjects.bulk-store') }}" enctype="multipart/form-data">
                @csrf

                <x-input-label for="file" value="CSV file" />
                <input type="file" id="file" name="file" accept=".csv,text/csv,text/plain" class="form-control" required>
                <x-input-error :messages="$errors->get('file')" />

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">Import Subjects</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
