<x-app-layout title="New Research Subject">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <p class="text-muted small mb-4">Set up who the subject belongs to. The supervising professor will add the description, responsibilities, candidate profile, and keywords, then open it up for applications from their own subject editor.</p>

            <form method="POST" action="{{ route('admin.subjects.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <x-input-label for="professor_id" value="Professor" />
                        <select id="professor_id" name="professor_id" class="form-select" required>
                            <option value="">Select a professor...</option>
                            @foreach ($professors as $professor)
                                <option value="{{ $professor->id }}" @selected(old('professor_id') == $professor->id)>
                                    {{ $professor->name }} &mdash; {{ $professor->email }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('professor_id')" />
                    </div>

                    <div class="col-md-6">
                        <x-input-label for="department_id" value="Doctoral Program" />
                        <select id="department_id" name="department_id" class="form-select" required>
                            <option value="">Select a doctoral program...</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('department_id')" />
                    </div>

                    <div class="col-12">
                        <x-input-label for="title" value="Subject title" />
                        <x-text-input id="title" name="title" value="{{ old('title') }}" required />
                        <x-input-error :messages="$errors->get('title')" />
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">Create Subject</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
