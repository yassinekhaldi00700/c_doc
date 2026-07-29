<footer class="border-top py-4 bg-light">
    <div class="container d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="text-muted small">
            &copy; {{ date('Y') }} Euromed University of Fès &mdash; Doctoral Admission Platform. All rights reserved.
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('home') }}" class="small text-muted text-decoration-none">Home</a>
            <a href="https://ueuromed.org/fr" target="_blank" rel="noopener" class="small text-muted text-decoration-none">About</a>
            <a href="{{ route('candidate.subjects.index') }}" class="small text-muted text-decoration-none">Research Subjects</a>
        </div>
    </div>
</footer>
