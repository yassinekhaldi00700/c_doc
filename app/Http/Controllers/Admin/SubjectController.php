<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubjectRequest;
use App\Models\Department;
use App\Models\ResearchSubject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(Request $request): View
    {
        $subjects = ResearchSubject::with(['professor', 'department'])
            ->withCount('applications')
            ->when($request->filled('department_id'), fn ($query) => $query->where('department_id', $request->department_id))
            ->when($request->filled('professor_id'), fn ($query) => $query->where('professor_id', $request->professor_id))
            ->when($request->filled('search'), fn ($query) => $query->where('title', 'like', '%'.$request->search.'%'))
            ->when($request->filled('status'), fn ($query) => $query->where('is_open', $request->status === 'open'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.subjects.index', [
            'subjects' => $subjects,
            'departments' => Department::orderBy('name')->get(),
            'professors' => User::where('role', UserRole::Professor)->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', ResearchSubject::class);

        return view('admin.subjects.create', [
            'departments' => Department::orderBy('name')->get(),
            'professors' => User::where('role', UserRole::Professor)->orderBy('name')->get(),
        ]);
    }

    /**
     * The admin only sets up who/what/where — professor, doctoral program,
     * title. The professor fills in the description, responsibilities,
     * candidate profile, and keywords themselves, then opens it up, via
     * their own subject editor.
     */
    public function store(SubjectRequest $request): RedirectResponse
    {
        $subject = ResearchSubject::create([
            'professor_id' => $request->validated('professor_id'),
            'department_id' => $request->validated('department_id'),
            'title' => $request->validated('title'),
            'description' => 'Full description pending — to be completed by the supervising professor.',
            'is_open' => false,
        ]);

        return redirect()->route('admin.subjects.show', $subject)
            ->with('success', 'Research subject created. The professor can now complete its details.');
    }

    public function show(ResearchSubject $subject): View
    {
        return view('admin.subjects.show', [
            'subject' => $subject->load(['professor', 'department']),
            'applications' => $subject->applications()->with('candidate')->latest()->paginate(10),
        ]);
    }

    public function destroy(ResearchSubject $subject): RedirectResponse
    {
        $subject->delete();

        return redirect()->route('admin.subjects.index')->with('success', 'Research subject removed.');
    }
}
