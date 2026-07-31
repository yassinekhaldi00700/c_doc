<x-app-layout title="Edit Research Subject">
    <a href="{{ route('admin.subjects.show', $subject) }}" class="d-inline-flex align-items-center gap-1 text-decoration-none small fw-semibold mb-3" style="color:#005292;">
        <i class="bi bi-arrow-left"></i> Back to subject
    </a>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h3 class="h6 fw-bold mb-1">Accepting applications</h3>
                <p class="small text-muted mb-0">This can be switched on or off any time, independently of the content below.</p>
            </div>
            <form method="POST" action="{{ route('admin.subjects.toggle-open', $subject) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn {{ $subject->is_open ? 'btn-success' : 'btn-danger' }}">
                    <i class="bi {{ $subject->is_open ? 'bi-unlock-fill' : 'bi-lock-fill' }} me-1"></i>
                    {{ $subject->is_open ? 'Open for applications' : 'Closed to applications' }}
                </button>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.subjects.update', $subject) }}">
                @csrf
                @method('PUT')

                @php $selectedProfessorId = old('professor_id', $subject->professor_id); @endphp

                <div class="row g-3">
                    <div class="col-md-6">
                        <x-input-label for="professor_id" value="Professor" />
                        <select id="professor_id" name="professor_id" class="form-select" required>
                            @foreach ($professors as $professor)
                                <option value="{{ $professor->id }}" @selected($selectedProfessorId == $professor->id)>
                                    {{ $professor->name }} &mdash; {{ $professor->email }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('professor_id')" />
                    </div>

                    <div class="col-md-6">
                        <x-input-label for="department_id" value="Doctoral Program" />
                        <select id="department_id" name="department_id" class="form-select" required>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" @selected(old('department_id', $subject->department_id) == $department->id)>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('department_id')" />
                    </div>

                    <div class="col-12">
                        <x-input-label for="title" value="Subject title" />
                        <x-text-input id="title" name="title" value="{{ old('title', $subject->title) }}" required />
                        <x-input-error :messages="$errors->get('title')" />
                    </div>

                    <div class="col-12">
                        <div id="content-group-error" class="alert alert-danger d-none mb-0" role="alert">
                            Fill in Description, PhD student's responsibilities, Candidate profile, and Keywords together, or leave all four empty.
                        </div>
                    </div>

                    <div class="col-12">
                        <x-input-label for="description" value="Description" />
                        <textarea id="description" name="description" rows="5" class="form-control" required>{{ old('description', $subject->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" />
                    </div>

                    <div class="col-12">
                        <x-input-label for="responsibilities" value="PhD student's responsibilities" />
                        <textarea id="responsibilities" name="responsibilities" rows="3" class="form-control">{{ old('responsibilities', $subject->responsibilities) }}</textarea>
                        <x-input-error :messages="$errors->get('responsibilities')" />
                    </div>

                    <div class="col-12">
                        <x-input-label for="candidate_profile" value="Candidate profile" />
                        <textarea id="candidate_profile" name="candidate_profile" rows="3" class="form-control">{{ old('candidate_profile', $subject->candidate_profile) }}</textarea>
                        <x-input-error :messages="$errors->get('candidate_profile')" />
                    </div>

                    <div class="col-12">
                        <x-input-label for="keywords" value="Keywords (comma-separated)" />
                        <x-text-input id="keywords" name="keywords" value="{{ old('keywords', $subject->keywords) }}" placeholder="e.g. machine learning, NLP, data science" />
                        <x-input-error :messages="$errors->get('keywords')" />
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.subjects.show', $subject) }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
