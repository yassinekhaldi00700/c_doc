<x-app-layout title="Oral Exam Picks">
    <h2 class="h4 fw-bold mb-1">Oral Exam Picks</h2>
    <p class="text-muted">Choose a subject to pick up to 5 candidates you'd like to invite to the oral exam. This is a recommendation for the admin team — it doesn't change any application's status.</p>
    <div class="list-group">
        @forelse ($subjects as $subject)
            <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center gap-3 py-3" href="{{ route('professor.oral-exam-picks.edit', $subject) }}">
                <span>
                    {{ $subject->title }}
                    <span class="badge text-bg-light ms-2">{{ $subject->applications_count }} applicant(s)</span>
                    @if ($subject->favorited_count > 0)
                        <span class="badge bg-success-subtle text-success ms-1"><i class="bi bi-star-fill"></i> {{ $subject->favorited_count }} picked</span>
                    @endif
                </span>
                <span class="text-primary">Pick candidates &rarr;</span>
            </a>
        @empty
            <p class="alert alert-info">No research subjects are assigned to you.</p>
        @endforelse
    </div>
    <div class="mt-3">{{ $subjects->links() }}</div>
</x-app-layout>
