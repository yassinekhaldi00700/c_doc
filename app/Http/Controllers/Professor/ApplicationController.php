<?php

namespace App\Http\Controllers\Professor;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $subjectIds = $request->user()->subjects()->pluck('id');

        $applications = Application::whereIn('subject_id', $subjectIds)
            ->with(['candidate.profile', 'subject'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('subject_id'), fn ($query) => $query->where('subject_id', $request->subject_id))
            ->when($request->filled('search'), fn ($query) => $query->whereHas(
                'candidate.profile',
                fn ($profileQuery) => $profileQuery->where('first_name', 'like', '%'.$request->search.'%')
                    ->orWhere('last_name', 'like', '%'.$request->search.'%')
            ))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('professor.applications.index', [
            'applications' => $applications,
            'subjects' => $request->user()->subjects()->orderBy('title')->get(),
            'statuses' => ApplicationStatus::cases(),
        ]);
    }

    public function show(Application $application): View
    {
        $this->authorize('view', $application);

        return view('professor.applications.show', [
            'application' => $application->load(['candidate.profile', 'subject', 'documents', 'statusLogs.changedBy']),
        ]);
    }
}
