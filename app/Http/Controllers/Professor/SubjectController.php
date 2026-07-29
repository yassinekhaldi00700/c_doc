<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubjectRequest;
use App\Models\Department;
use App\Models\ResearchSubject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(Request $request): View
    {
        $subjects = $request->user()->subjects()
            ->withCount('applications')
            ->with('department')
            ->when($request->filled('search'), fn ($query) => $query->where('title', 'like', '%'.$request->search.'%'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('professor.subjects.index', [
            'subjects' => $subjects,
        ]);
    }

    public function show(ResearchSubject $subject): View
    {
        $this->authorize('view', $subject);

        return view('professor.subjects.show', [
            'subject' => $subject->load('department'),
            'applications' => $subject->applications()->with('candidate')->latest()->paginate(10),
        ]);
    }

    public function edit(ResearchSubject $subject): View
    {
        $this->authorize('update', $subject);

        return view('professor.subjects.edit', [
            'subject' => $subject,
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    /**
     * Doctoral program and title are fixed at creation and never sent by
     * the edit form (shown read-only there) — only the content fields are
     * updatable here. Open/closed is handled separately by toggleOpen(),
     * so a professor can flip it without being blocked by the content
     * fields' all-or-nothing validation (e.g. on older subjects created
     * before "Candidate profile" existed, which may have the other three
     * filled but that one still empty).
     */
    public function update(SubjectRequest $request, ResearchSubject $subject): RedirectResponse
    {
        $subject->update([
            'description' => $request->validated('description'),
            'responsibilities' => $request->validated('responsibilities'),
            'candidate_profile' => $request->validated('candidate_profile'),
            'keywords' => $request->validated('keywords'),
        ]);

        return redirect()->route('professor.subjects.show', $subject)
            ->with('success', 'Research subject updated successfully.');
    }

    public function toggleOpen(ResearchSubject $subject): RedirectResponse
    {
        $this->authorize('update', $subject);

        $subject->update(['is_open' => ! $subject->is_open]);

        return back()->with('success', $subject->is_open ? 'Subject opened for applications.' : 'Subject closed to applications.');
    }

    public function destroy(ResearchSubject $subject): RedirectResponse
    {
        $this->authorize('delete', $subject);

        $subject->delete();

        return redirect()->route('professor.subjects.index')
            ->with('success', 'Research subject removed.');
    }
}
