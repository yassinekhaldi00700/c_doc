<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\Department;
use App\Models\Profile;
use App\Models\ResearchSubject;
use App\Models\User;

class DashboardStatsService
{
    /**
     * @return array<string, mixed>
     */
    public function adminOverview(): array
    {
        $statusCounts = Application::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $departmentCounts = Department::withCount('subjects')
            ->orderByDesc('subjects_count')
            ->get(['id', 'name']);

        $applicationsPerDepartment = Application::join('research_subjects', 'applications.subject_id', '=', 'research_subjects.id')
            ->join('departments', 'research_subjects.department_id', '=', 'departments.id')
            ->selectRaw('departments.name as department, count(*) as total')
            ->groupBy('departments.name')
            ->orderByDesc('total')
            ->get();

        return [
            'total_users' => User::count(),
            'total_candidates' => User::where('role', UserRole::Candidate)->count(),
            'total_professors' => User::where('role', UserRole::Professor)->count(),
            'total_departments' => Department::count(),
            'total_subjects' => ResearchSubject::count(),
            'open_subjects' => ResearchSubject::open()->count(),
            'total_applications' => Application::count(),
            'status_counts' => collect(ApplicationStatus::cases())->mapWithKeys(
                fn (ApplicationStatus $status) => [$status->value => (int) ($statusCounts[$status->value] ?? 0)]
            ),
            'department_subject_counts' => $departmentCounts,
            'applications_per_department' => $applicationsPerDepartment,
            'recent_applications' => Application::with(['candidate.profile', 'subject'])->latest()->take(8)->get(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function professorOverview(User $professor): array
    {
        $subjectIds = $professor->subjects()->pluck('id');

        $statusCounts = Application::whereIn('subject_id', $subjectIds)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'total_subjects' => $subjectIds->count(),
            'open_subjects' => $professor->subjects()->open()->count(),
            'total_applicants' => Application::whereIn('subject_id', $subjectIds)->count(),
            'status_counts' => collect(ApplicationStatus::cases())->mapWithKeys(
                fn (ApplicationStatus $status) => [$status->value => (int) ($statusCounts[$status->value] ?? 0)]
            ),
            'recent_applications' => Application::with(['candidate.profile', 'subject'])
                ->whereIn('subject_id', $subjectIds)
                ->latest()
                ->take(8)
                ->get(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function candidateOverview(User $candidate): array
    {
        $applications = $candidate->applications()->with('subject')->latest()->get();
        $profile = $candidate->profile ?? new Profile(['user_id' => $candidate->id]);

        return [
            'total_applications' => $applications->count(),
            'status_counts' => collect(ApplicationStatus::cases())->mapWithKeys(
                fn (ApplicationStatus $status) => [$status->value => $applications->where('status', $status)->count()]
            ),
            'applications' => $applications,
            'profile_complete' => $profile->isComplete(),
            'profile_missing' => $profile->missingRequirements(),
        ];
    }
}
