<nav class="nav flex-column gap-1">
    <a class="nav-link {{ request()->routeIs('candidate.dashboard') ? 'active' : '' }}" href="{{ route('candidate.dashboard') }}">
        <i class="bi bi-speedometer2 me-2"></i>Dashboard
    </a>
    <a class="nav-link {{ request()->routeIs('candidate.subjects.*') ? 'active' : '' }}" href="{{ route('candidate.subjects.index') }}">
        <i class="bi bi-search me-2"></i>Browse Subjects
    </a>
    <a class="nav-link {{ request()->routeIs('candidate.applications.*') ? 'active' : '' }}" href="{{ route('candidate.applications.index') }}">
        <i class="bi bi-file-earmark-text me-2"></i>My Applications
    </a>
    <a class="nav-link {{ request()->routeIs('candidate.profile.*') ? 'active' : '' }}" href="{{ route('candidate.profile.overview') }}" hx-boost="true">
        <i class="bi bi-person-badge me-2"></i>My Profile
        @unless (auth()->user()->profile?->isComplete())
            <span class="badge bg-warning-subtle text-warning ms-1">Incomplete</span>
        @endunless
    </a>
    <a class="nav-link {{ request()->routeIs('feedback.*') ? 'active' : '' }}" href="{{ route('feedback.index') }}">
        <i class="bi bi-chat-square-text me-2"></i>Feedback
    </a>
    <hr>
    <a class="nav-link" href="{{ route('home') }}">
        <i class="bi bi-globe me-2"></i>Public Site
    </a>
</nav>
