@php
    $user = $user ?? null;
    $currentRole = old('role', $user?->role ? strtolower($user->role->name) : null);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <x-input-label for="name" value="Full name" />
        <x-text-input id="name" name="name" value="{{ old('name', $user?->name) }}" required autofocus />
        <x-input-error :messages="$errors->get('name')" />
    </div>

    <div class="col-md-6">
        <x-input-label for="email" value="Email" />
        <x-text-input id="email" type="email" name="email" value="{{ old('email', $user?->email) }}" required />
        <x-input-error :messages="$errors->get('email')" />
    </div>

    <div class="col-md-6">
        <x-input-label for="role" value="Role" />
        <select id="role" name="role" class="form-select" required onchange="document.getElementById('department-field').style.display = this.value === 'professor' ? 'block' : 'none'">
            <option value="">Select a role...</option>
            <option value="admin" @selected($currentRole === 'admin')>Administrator</option>
            <option value="professor" @selected($currentRole === 'professor')>Professor</option>
            <option value="candidate" @selected($currentRole === 'candidate')>Candidate</option>
        </select>
        <x-input-error :messages="$errors->get('role')" />
    </div>

    <div class="col-md-6" id="department-field" style="{{ $currentRole === 'professor' ? '' : 'display:none' }}">
        <x-input-label for="department_id" value="Doctoral Program" />
        <select id="department_id" name="department_id" class="form-select">
            <option value="">Select a doctoral program...</option>
            @foreach ($departments as $department)
                <option value="{{ $department->id }}" @selected(old('department_id', $user?->department_id) == $department->id)>
                    {{ $department->name }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('department_id')" />
    </div>

    <div class="col-md-6">
        <x-input-label for="phone" value="Phone number (optional)" />
        <x-text-input id="phone" name="phone" value="{{ old('phone', $user?->phone) }}" />
        <x-input-error :messages="$errors->get('phone')" />
    </div>

    <div class="col-md-6">
        <x-input-label for="password" :value="$user ? 'New password (leave blank to keep current)' : 'Password'" />
        <x-text-input id="password" type="password" name="password" :required="! $user" autocomplete="new-password" />
        <x-input-error :messages="$errors->get('password')" />
    </div>

    @if ($user)
        <div class="col-12">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" @checked(old('is_active', $user->is_active))>
                <label class="form-check-label" for="is_active">Account active</label>
            </div>
        </div>
    @endif
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
    <button type="submit" class="btn btn-primary px-4">{{ $user ? 'Save Changes' : 'Create User' }}</button>
</div>
