<x-app-layout title="Edit Research Subject">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h3 class="h6 fw-bold mb-1">Accepting applications</h3>
                <p class="small text-muted mb-0">This can be switched on or off any time, independently of the content below.</p>
            </div>
            <form method="POST" action="{{ route('professor.subjects.toggle-open', $subject) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn {{ $subject->is_open ? 'btn-success' : 'btn-danger' }}">
                    <i class="bi {{ $subject->is_open ? 'bi-unlock-fill' : 'bi-lock-fill' }} me-1"></i>
                    {{ $subject->is_open ? 'Open for applications' : 'Closed to applications' }}
                </button>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('professor.subjects.update', $subject) }}">
                @csrf
                @method('PUT')
                @include('professor.subjects._form', ['subject' => $subject])
            </form>
        </div>
    </div>
</x-app-layout>
