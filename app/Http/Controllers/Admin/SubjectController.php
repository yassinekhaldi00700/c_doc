<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkSubjectImportRequest;
use App\Http\Requests\SubjectRequest;
use App\Models\AdmissionSetting;
use App\Models\Department;
use App\Models\ResearchSubject;
use App\Models\User;
use App\Services\CsvReader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SubjectController extends Controller
{
    private const PENDING_DESCRIPTION = 'Full description pending — to be completed by the supervising professor.';

    public function index(Request $request): View
    {
        $subjects = ResearchSubject::with(['professor', 'department'])
            ->withCount('applications')
            ->withCount(['applications as professor_picks_count' => fn ($query) => $query->whereNotNull('professor_favorited_at')])
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
            'applicationsPaused' => AdmissionSetting::applicationsArePaused(),
        ]);
    }

    public function toggleApplicationAccess(Request $request): RedirectResponse
    {
        $applicationsPaused = DB::transaction(function () use ($request): bool {
            $setting = AdmissionSetting::query()->lockForUpdate()->firstOrCreate();
            $paused = ! $setting->applications_paused;

            $setting->update([
                'applications_paused' => $paused,
                'paused_at' => $paused ? now() : null,
                'paused_by' => $paused ? $request->user()->id : null,
            ]);

            return $paused;
        });

        return back()->with(
            'success',
            $applicationsPaused
                ? 'All new applications have been paused. Open subjects remain visible to candidates.'
                : 'Applications have resumed. Candidates can apply to open subjects again.'
        );
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
            'description' => self::PENDING_DESCRIPTION,
            'is_open' => false,
        ]);

        return redirect()->route('admin.subjects.show', $subject)
            ->with('success', 'Research subject created. The professor can now complete its details.');
    }

    public function bulkCreate(): View
    {
        $this->authorize('create', ResearchSubject::class);

        return view('admin.subjects.bulk-create');
    }

    /**
     * Same minimal philosophy as store(): only who/what/where. The
     * professor is matched by email (the CSV's "association"), the
     * doctoral program by its department code.
     */
    public function bulkStore(BulkSubjectImportRequest $request): RedirectResponse
    {
        $this->authorize('create', ResearchSubject::class);

        $rows = CsvReader::rows($request->file('file')->getRealPath());

        $created = 0;
        $errors = [];

        foreach ($rows as $i => $row) {
            $line = $i + 2; // 1 = header

            $validator = Validator::make($row, [
                'title' => ['required', 'string', 'max:255'],
                'professor_email' => ['required', 'email'],
                'doctoral_program' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                $errors[] = "Line $line: ".implode(' ', $validator->errors()->all());

                continue;
            }

            $professor = User::where('email', $row['professor_email'])->where('role', UserRole::Professor)->first();
            if (! $professor) {
                $errors[] = "Line $line: no professor account found for \"{$row['professor_email']}\".";

                continue;
            }

            $department = Department::where('code', $row['doctoral_program'])->first();
            if (! $department) {
                $errors[] = "Line $line: doctoral program \"{$row['doctoral_program']}\" not found.";

                continue;
            }

            ResearchSubject::create([
                'professor_id' => $professor->id,
                'department_id' => $department->id,
                'title' => $row['title'],
                'description' => self::PENDING_DESCRIPTION,
                'is_open' => false,
            ]);
            $created++;
        }

        return redirect()->route('admin.subjects.index')
            ->with($created > 0 ? 'success' : 'error', $created.' research subject(s) created.'.($errors ? ' '.count($errors).' row(s) skipped.' : ''))
            ->with('bulk_import_errors', $errors);
    }

    public function show(ResearchSubject $subject): View
    {
        return view('admin.subjects.show', [
            'subject' => $subject->load(['professor', 'department']),
            'applications' => $subject->applications()->with('candidate')
                ->orderByDesc('professor_favorited_at')
                ->latest()
                ->paginate(10),
        ]);
    }

    /**
     * Unlike the professor's editor (which can't touch professor/department
     * /title and enforces the content fields as all-or-nothing), an admin
     * can edit everything — including reassigning the subject to a
     * different professor or doctoral program.
     */
    public function edit(ResearchSubject $subject): View
    {
        $this->authorize('update', $subject);

        return view('admin.subjects.edit', [
            'subject' => $subject->load(['professor', 'department']),
            'departments' => Department::orderBy('name')->get(),
            'professors' => User::where('role', UserRole::Professor)->orderBy('name')->get(),
        ]);
    }

    /**
     * Open/closed is handled separately by toggleOpen(), same reasoning as
     * the professor's editor: it shouldn't be blocked by the content
     * fields' all-or-nothing validation (many existing subjects predate
     * "Candidate profile" and still have it empty).
     */
    public function update(SubjectRequest $request, ResearchSubject $subject): RedirectResponse
    {
        $subject->update([
            'professor_id' => $request->validated('professor_id'),
            'department_id' => $request->validated('department_id'),
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'responsibilities' => $request->validated('responsibilities'),
            'candidate_profile' => $request->validated('candidate_profile'),
            'keywords' => $request->validated('keywords'),
        ]);

        return redirect()->route('admin.subjects.show', $subject)
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
        $subject->delete();

        return redirect()->route('admin.subjects.index')->with('success', 'Research subject removed.');
    }
}
