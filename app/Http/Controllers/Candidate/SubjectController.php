<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\ResearchSubject;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(Request $request): View
    {
        $subjects = ResearchSubject::open()
            ->with(['professor', 'department'])
            ->when($request->filled('department_id'), fn ($query) => $query->where('department_id', $request->department_id))
            ->when($request->filled('search'), fn ($query) => $query->where('title', 'like', '%'.$request->search.'%'))
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('candidate.subjects.index', [
            'subjects' => $subjects,
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function show(ResearchSubject $subject): View
    {
        $subject->load(['professor', 'department']);

        $alreadyApplied = $subject->applications()
            ->where('candidate_id', auth()->id())
            ->exists();

        return view('candidate.subjects.show', [
            'subject' => $subject,
            'alreadyApplied' => $alreadyApplied,
            'profileComplete' => auth()->user()->profile?->isComplete() ?? false,
        ]);
    }
}
