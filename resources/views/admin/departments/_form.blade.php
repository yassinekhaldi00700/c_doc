@php $department = $department ?? null; @endphp

<div class="row g-3">
    <div class="col-md-8">
        <x-input-label for="name" value="Doctoral Program name" />
        <x-text-input id="name" name="name" value="{{ old('name', $department?->name) }}" required autofocus />
        <x-input-error :messages="$errors->get('name')" />
    </div>

    <div class="col-md-4">
        <x-input-label for="code" value="Code" />
        <x-text-input id="code" name="code" value="{{ old('code', $department?->code) }}" placeholder="e.g. INFO" required />
        <x-input-error :messages="$errors->get('code')" />
    </div>

    <div class="col-12">
        <x-input-label for="description" value="Description (optional)" />
        <textarea id="description" name="description" rows="3" class="form-control">{{ old('description', $department?->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" />
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary">Cancel</a>
    <button type="submit" class="btn btn-primary px-4">{{ $department ? 'Save Changes' : 'Create Doctoral Program' }}</button>
</div>
