<x-app-layout title="Application Deadline">
    <div class="card border-0 shadow-sm" style="max-width: 40rem;">
        <div class="card-body p-4">
            <p class="text-muted">This date is shown to everyone in the announcement bar at the top of every page.</p>
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <x-input-label for="application_deadline" value="Application deadline" />
                    <x-text-input id="application_deadline" type="date" name="application_deadline"
                        value="{{ old('application_deadline', $deadline->format('Y-m-d')) }}" required />
                    <x-input-error :messages="$errors->get('application_deadline')" />
                </div>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Save</button>
            </form>
        </div>
    </div>
</x-app-layout>
