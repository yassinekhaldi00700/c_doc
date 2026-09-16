<x-app-layout title="Oral Exam Picks">
    <a href="{{ route('professor.oral-exam-picks.index') }}" class="d-inline-flex align-items-center gap-1 text-decoration-none small fw-semibold mb-3" style="color:#005292;">
        <i class="bi bi-arrow-left"></i> Back to my subjects
    </a>

    <div class="mb-4">
        <p class="text-uppercase small fw-semibold mb-1" style="color:#44A66D;">{{ $subject->title }}</p>
        <h2 class="h4 fw-bold mb-0">Oral Exam Picks</h2>
    </div>

    <div class="alert alert-info" role="alert">
        <i class="bi bi-info-circle me-1"></i>
        Choose up to 5 candidates you'd like to invite to the oral exam, propose a date &amp; time between {{ \Carbon\Carbon::parse($minDate)->format('d/m/Y') }} and {{ \Carbon\Carbon::parse($maxDate)->format('d/m/Y') }} for each, then confirm your selection below. This is only a recommendation for the admin team — it does not change any application's status or send anything to candidates. The admin reviews your picks, can adjust the proposed date, and sends the actual oral exam invitations themselves.
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if ($applications->isEmpty())
                <p class="text-muted mb-0">No applications received yet for this subject.</p>
            @else
                <form method="POST" action="{{ route('professor.oral-exam-picks.update', $subject) }}" data-oral-exam-picks-form data-max="5">
                    @csrf
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr class="text-muted small text-uppercase">
                                    <th></th>
                                    <th>Candidate</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Proposed exam date &amp; time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($applications as $application)
                                    @php($checked = old('application_ids') ? in_array((string) $application->id, old('application_ids'), true) : (bool) $application->professor_favorited_at)
                                    <tr>
                                        <td>
                                            <input class="form-check-input" type="checkbox" name="application_ids[]" value="{{ $application->id }}" id="application_{{ $application->id }}" data-oral-exam-pick-checkbox @checked($checked)>
                                        </td>
                                        <td>
                                            <label class="form-check-label" for="application_{{ $application->id }}">{{ $application->fullName() }}</label>
                                            @if ($application->professor_favorited_at)
                                                <span class="badge bg-success-subtle text-success ms-1">Picked</span>
                                            @endif
                                        </td>
                                        <td><x-status-badge :status="$application->status" /></td>
                                        <td class="text-muted small">{{ $application->submitted_at?->format('d M Y') }}</td>
                                        <td>
                                            <input type="datetime-local" class="form-control form-control-sm" style="min-width: 12rem"
                                                name="dates[{{ $application->id }}]"
                                                data-oral-exam-pick-date
                                                min="{{ $minDate }}T00:00" max="{{ $maxDate }}T23:59"
                                                value="{{ old('dates.'.$application->id, optional($application->professor_proposed_exam_at)->format('Y-m-d\TH:i')) }}"
                                                @required($checked)>
                                            <x-input-error :messages="$errors->get('dates.'.$application->id)" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="small text-muted" data-oral-exam-picks-counter>0 / 5 selected</p>
                    <button type="submit" class="btn btn-primary">Confirm my picks for the oral exam</button>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
