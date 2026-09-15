<x-app-layout title="PV de recrutement">
    <p class="text-muted">Choisissez un sujet pour préparer et télécharger son procès-verbal de recrutement.</p>
    <div class="list-group">
        @forelse ($subjects as $subject)
            <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center gap-3 py-3" href="{{ route('professor.recruitment.edit', $subject) }}">
                <span>{{ $subject->title }} <span class="badge text-bg-light ms-2">{{ $subject->applications_count }} candidature(s)</span></span>
                <span class="text-primary">Préparer le PV &rarr;</span>
            </a>
        @empty
            <p class="alert alert-info">Aucun sujet ne vous est attribué.</p>
        @endforelse
    </div>
    <div class="mt-3">{{ $subjects->links() }}</div>
</x-app-layout>
