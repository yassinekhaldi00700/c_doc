<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApplicationStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewApplicationRequest;
use App\Models\Application;
use App\Models\User;
use App\Services\ApplicationDecisionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function __construct(protected ApplicationDecisionService $decisionService)
    {
    }

    public function index(Request $request): View
    {
        $applications = Application::with(['candidate.profile', 'subject.department', 'subject.professor'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('professor_id'), fn ($query) => $query->whereHas(
                'subject',
                fn ($subjectQuery) => $subjectQuery->where('professor_id', $request->professor_id)
            ))
            ->when($request->filled('search'), fn ($query) => $query->where(
                fn ($searchQuery) => $searchQuery
                    ->whereHas('candidate.profile', fn ($profileQuery) => $profileQuery->where('first_name', 'like', '%'.$request->search.'%')
                        ->orWhere('last_name', 'like', '%'.$request->search.'%'))
                    ->orWhereHas('subject', fn ($subjectQuery) => $subjectQuery->where('title', 'like', '%'.$request->search.'%'))
            ))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.applications.index', [
            'applications' => $applications,
            'statuses' => ApplicationStatus::cases(),
            'professors' => User::where('role', UserRole::Professor)->orderBy('name')->get(),
        ]);
    }

    public function show(Application $application): View
    {
        return view('admin.applications.show', [
            'application' => $application->load(['candidate.profile', 'subject.department', 'subject.professor', 'documents', 'statusLogs.changedBy']),
            'statuses' => ApplicationStatus::cases(),
        ]);
    }

    public function review(ReviewApplicationRequest $request, Application $application): RedirectResponse
    {
        $status = ApplicationStatus::from($request->validated('status'));

        $this->decisionService->decide(
            application: $application,
            status: $status,
            reviewer: $request->user(),
            comment: $request->validated('comment'),
            oralExamDate: $request->validated('oral_exam_date'),
            programStartDate: $request->validated('program_start_date'),
        );

        return redirect()->route('admin.applications.show', $application)
            ->with('success', 'Application status updated to "'.$status->label().'".');
    }

    public function resendNotification(Application $application): RedirectResponse
    {
        $this->decisionService->resendNotification($application);

        return redirect()->route('admin.applications.show', $application)
            ->with('success', 'Notification email re-queued.');
    }
}
