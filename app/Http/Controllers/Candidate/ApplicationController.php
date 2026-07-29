<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Candidate\StoreApplicationRequest;
use App\Models\Application;
use App\Models\ResearchSubject;
use App\Services\ApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function __construct(protected ApplicationService $applicationService)
    {
    }

    public function index(Request $request): View
    {
        $applications = $request->user()->applications()
            ->with(['subject.department', 'subject.professor'])
            ->latest()
            ->paginate(10);

        return view('candidate.applications.index', [
            'applications' => $applications,
        ]);
    }

    public function create(Request $request, ResearchSubject $subject): View|RedirectResponse
    {
        if (! $subject->is_open) {
            return redirect()->route('candidate.subjects.show', $subject)
                ->with('error', 'This research subject is no longer accepting applications.');
        }

        $alreadyApplied = $subject->applications()->where('candidate_id', $request->user()->id)->exists();

        if ($alreadyApplied) {
            return redirect()->route('candidate.subjects.show', $subject)
                ->with('error', 'You have already submitted an application for this subject.');
        }

        if ($request->session()->get('intended_subject') === $subject->id) {
            $request->session()->forget('intended_subject');
        }

        return view('candidate.applications.create', [
            'subject' => $subject->load(['professor', 'department']),
            'profile' => $request->user()->profile()->with('documents')->first(),
        ]);
    }

    public function store(StoreApplicationRequest $request, ResearchSubject $subject): RedirectResponse
    {
        if (! $subject->is_open) {
            return redirect()->route('candidate.subjects.show', $subject)
                ->with('error', 'This research subject is no longer accepting applications.');
        }

        $alreadyApplied = $subject->applications()->where('candidate_id', $request->user()->id)->exists();

        if ($alreadyApplied) {
            return redirect()->route('candidate.subjects.show', $subject)
                ->with('error', 'You have already submitted an application for this subject.');
        }

        $application = $this->applicationService->submit(
            $request->user(),
            $subject,
            $request->validated('motivation_summary'),
            $request->documentFiles()['motivation_letter'],
        );

        return redirect()->route('candidate.applications.show', $application)
            ->with('success', 'Your application has been submitted successfully.');
    }

    public function show(Request $request, Application $application): View
    {
        $this->authorize('view', $application);

        return view('candidate.applications.show', [
            'application' => $application->load(['subject.department', 'subject.professor', 'documents', 'statusLogs.changedBy', 'candidate.profile']),
        ]);
    }
}
