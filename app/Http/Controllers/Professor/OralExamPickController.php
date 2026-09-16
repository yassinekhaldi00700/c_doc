<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Professor\OralExamPicksRequest;
use App\Models\ResearchSubject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * A professor's informal shortlist of candidates they'd like to invite to
 * the oral exam — separate from the official application status (still set
 * by an admin) and from the PV/RecruitmentReport. It only marks candidates
 * for the admin's attention; the admin decides and sends invitations
 * themselves via the existing application review flow.
 */
class OralExamPickController extends Controller
{
    public function index(Request $request): View
    {
        return view('professor.oral-exam-picks.index', [
            'subjects' => $request->user()->subjects()
                ->withCount('applications')
                ->withCount(['applications as favorited_count' => fn ($query) => $query->whereNotNull('professor_favorited_at')])
                ->latest()
                ->paginate(15),
        ]);
    }

    public function edit(ResearchSubject $subject): View
    {
        abort_unless((int) $subject->professor_id === (int) request()->user()->id, 403);

        return view('professor.oral-exam-picks.edit', [
            'subject' => $subject,
            'applications' => $subject->applications()->with('candidate.profile')->orderBy('id')->get(),
            'minDate' => OralExamPicksRequest::MIN_DATE,
            'maxDate' => OralExamPicksRequest::MAX_DATE,
        ]);
    }

    public function update(OralExamPicksRequest $request, ResearchSubject $subject): RedirectResponse
    {
        DB::transaction(function () use ($request, $subject) {
            $subject->applications()->update(['professor_favorited_at' => null, 'professor_proposed_exam_at' => null]);
            $dates = $request->validated('dates', []);
            foreach ($request->validated('application_ids', []) as $id) {
                $subject->applications()->where('id', $id)->update([
                    'professor_favorited_at' => now(),
                    'professor_proposed_exam_at' => \Carbon\Carbon::createFromFormat(OralExamPicksRequest::DATETIME_FORMAT, $dates[$id]),
                ]);
            }
        });

        return redirect()->route('professor.oral-exam-picks.edit', $subject)
            ->with('success', 'Your picks for the oral exam have been saved. The admin will review them and send invitations.');
    }
}
