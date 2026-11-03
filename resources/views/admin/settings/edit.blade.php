<x-app-layout title="Deadline & Exam Dates">
    <div class="card border-0 shadow-sm" style="max-width: 40rem;">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                @method('PUT')

                <h2 class="h6 fw-bold mb-1">Application deadline</h2>
                <p class="text-muted small">Shown to everyone in the announcement bar at the top of every page.</p>
                <div class="mb-4">
                    <x-input-label for="application_deadline" value="Application deadline" />
                    <x-text-input id="application_deadline" type="date" name="application_deadline"
                        value="{{ old('application_deadline', $settings['application_deadline']) }}" required />
                    <x-input-error :messages="$errors->get('application_deadline')" />
                </div>

                <h2 class="h6 fw-bold mb-1">Oral exam period</h2>
                <p class="text-muted small">Professors can only propose oral exam dates within this period (first and last day included).</p>
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <x-input-label for="oral_exam_min_date" value="From" />
                        <x-text-input id="oral_exam_min_date" type="date" name="oral_exam_min_date"
                            value="{{ old('oral_exam_min_date', $settings['oral_exam_min_date']) }}" required />
                        <x-input-error :messages="$errors->get('oral_exam_min_date')" />
                    </div>
                    <div class="col-sm-6">
                        <x-input-label for="oral_exam_max_date" value="To" />
                        <x-text-input id="oral_exam_max_date" type="date" name="oral_exam_max_date"
                            value="{{ old('oral_exam_max_date', $settings['oral_exam_max_date']) }}" required />
                        <x-input-error :messages="$errors->get('oral_exam_max_date')" />
                    </div>
                </div>

                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Save</button>
            </form>
        </div>
    </div>
</x-app-layout>
