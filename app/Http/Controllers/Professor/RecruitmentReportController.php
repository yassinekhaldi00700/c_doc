<?php

namespace App\Http\Controllers\Professor;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Professor\RecruitmentReportRequest;
use App\Models\RecruitmentReport;
use App\Models\ResearchSubject;
use App\Models\User;
use App\Services\RecruitmentReportDocument;
use Illuminate\Http\Request;

class RecruitmentReportController extends Controller
{
    public function index(Request $request)
    {
        return view('professor.recruitment.index', [
            'subjects' => $request->user()->subjects()->withCount('applications')->latest()->paginate(15),
        ]);
    }

    public function edit(Request $request, ResearchSubject $subject)
    {
        abort_unless((int) $subject->professor_id === (int) $request->user()->id, 403);

        return view('professor.recruitment.edit', [
            'subject' => $subject->load('professor'),
            'applications' => $subject->applications()->with('candidate.profile')->orderBy('id')->get(),
            'professors' => User::where('role', UserRole::Professor)->orderBy('name')->get(),
            'data' => RecruitmentReport::where('subject_id', $subject->id)->first()?->data ?? [],
        ]);
    }

    public function update(RecruitmentReportRequest $request, ResearchSubject $subject, RecruitmentReportDocument $document)
    {
        $data = $request->reportData();
        RecruitmentReport::updateOrCreate(['subject_id' => $subject->id], ['data' => $data]);

        if ($request->input('action') === 'download') {
            $path = $document->generate($subject, $data);
            return response()->download($path, 'PV-Recrutement-'.$subject->id.'.docx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])->deleteFileAfterSend(true);
        }

        return redirect()->route('professor.recruitment.edit', $subject)->with('success', 'PV de recrutement enregistré.');
    }
}
