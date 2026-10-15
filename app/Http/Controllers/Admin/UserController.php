<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkProfessorImportRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Department;
use App\Models\User;
use App\Services\CsvReader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::with('department')
            ->when($request->filled('role'), fn ($query) => $query->where('role', UserRole::fromName($request->role)))
            ->when($request->filled('department_id'), fn ($query) => $query->where('department_id', $request->department_id))
            ->when($request->filled('status'), fn ($query) => $query->where('is_active', $request->status === 'active'))
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('email', 'like', '%'.$request->search.'%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        User::create([
            ...$request->validated(),
            'role' => UserRole::fromName($request->validated('role')),
            'password' => Hash::make($request->validated('password')),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User account created successfully.');
    }

    public function bulkCreate(): View
    {
        return view('admin.users.bulk-create');
    }

    /**
     * Every professor created in one batch shares the password typed on this
     * form (e.g. a temporary one like "test tets") — they're nudged to pick
     * their own via needsPasswordChange() the same way an admin-reset
     * password already does, since password_changed_at is left unset.
     */
    public function bulkStore(BulkProfessorImportRequest $request): RedirectResponse
    {
        $rows = CsvReader::rows($request->file('file')->getRealPath());
        $password = Hash::make($request->validated('password'));

        $created = 0;
        $errors = [];

        foreach ($rows as $i => $row) {
            $line = $i + 2; // 1 = header

            $validator = Validator::make($row, [
                'name' => ['required', 'string', 'max:150'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'doctoral_program' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                $errors[] = "Line $line: ".implode(' ', $validator->errors()->all());

                continue;
            }

            $department = Department::where('code', $row['doctoral_program'])->first();
            if (! $department) {
                $errors[] = "Line $line: doctoral program \"{$row['doctoral_program']}\" not found.";

                continue;
            }

            User::create([
                'name' => $row['name'],
                'email' => $row['email'],
                'password' => $password,
                'role' => UserRole::Professor,
                'department_id' => $department->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            $created++;
        }

        return redirect()->route('admin.users.index')
            ->with($created > 0 ? 'success' : 'error', $created.' professor account(s) created.'.($errors ? ' '.count($errors).' row(s) skipped.' : ''))
            ->with('bulk_import_errors', $errors);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $data['role'] = UserRole::fromName($data['role']);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
            // An admin-assigned password is, by definition, not yet
            // changed by the account holder — reset the flag so they're
            // nudged to pick their own again.
            $data['password_changed_at'] = null;
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User account updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), 403, 'You cannot delete your own account.');

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User account deleted.');
    }

    public function toggleActive(User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), 403, 'You cannot deactivate your own account.');

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', $user->is_active ? 'User activated.' : 'User deactivated.');
    }
}
