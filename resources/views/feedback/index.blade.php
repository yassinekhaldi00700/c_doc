<x-app-layout title="Feedback">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <p class="text-uppercase small fw-semibold mb-1" style="color:#44A66D;">Share your thoughts</p>
            <h2 class="h4 fw-bold mb-1">Send Feedback</h2>
            <p class="text-muted mb-0">Spotted a bug, or have an idea to make the platform better? Let us know below.</p>
        </div>
    </div>

    @php $limitReached = $submittedToday >= $dailyLimit; @endphp

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            @if ($limitReached)
                <div class="alert alert-warning mb-0" role="alert">
                    <i class="bi bi-hourglass-split me-2"></i>You've reached today's limit of {{ $dailyLimit }} feedback submissions. Please try again tomorrow.
                </div>
            @else
                <form method="POST" action="{{ route('feedback.store') }}">
                    @csrf

                    <x-input-label for="message" value="Your feedback" />
                    <textarea id="message" name="message" rows="5" class="form-control @error('message') is-invalid @enderror" placeholder="Tell us what's on your mind..." required>{{ old('message') }}</textarea>
                    <x-input-error :messages="$errors->get('message')" />

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="text-muted small">{{ $submittedToday }} of {{ $dailyLimit }} used today</span>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-send me-1"></i> Submit
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    @if ($feedbackItems->isNotEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h3 class="h6 fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Your Previous Feedback</h3>
            </div>
            <div class="card-body">
                @foreach ($feedbackItems as $item)
                    <div class="d-flex justify-content-between align-items-start gap-3 py-2 {{ ! $loop->last ? 'border-bottom' : '' }}">
                        <p class="mb-0">{{ $item->message }}</p>
                        <span class="text-muted small flex-shrink-0">{{ $item->created_at->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-app-layout>
