<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(): View
    {
        $departments = Department::withCount(['users', 'subjects'])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.departments.index', [
            'departments' => $departments,
        ]);
    }

    public function create(): View
    {
        return view('admin.departments.create');
    }

    public function store(DepartmentRequest $request): RedirectResponse
    {
        Department::create($request->validated());

        return redirect()->route('admin.departments.index')->with('success', 'Doctoral Program created successfully.');
    }

    public function edit(Department $department): View
    {
        return view('admin.departments.edit', [
            'department' => $department,
        ]);
    }

    public function update(DepartmentRequest $request, Department $department): RedirectResponse
    {
        $department->update($request->validated());

        return redirect()->route('admin.departments.index')->with('success', 'Doctoral Program updated successfully.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        if ($department->users()->exists() || $department->subjects()->exists()) {
            return back()->with('error', 'Cannot delete a doctoral program that still has users or subjects assigned.');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')->with('success', 'Doctoral Program removed.');
    }
}
