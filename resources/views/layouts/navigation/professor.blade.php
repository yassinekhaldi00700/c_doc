<nav class="nav flex-column gap-1">
    <a class="nav-link {{ request()->routeIs('professor.dashboard') ? 'active' : '' }}" href="{{ route('professor.dashboard') }}">
        <i class="bi bi-speedometer2 me-2"></i>Dashboard
    </a>
    <a class="nav-link {{ request()->routeIs('professor.subjects.*') ? 'active' : '' }}" href="{{ route('professor.subjects.index') }}">
        <i class="bi bi-journal-richtext me-2"></i>My Research Subjects
    </a>
    <a class="nav-link {{ request()->routeIs('professor.applications.*') ? 'active' : '' }}" href="{{ route('professor.applications.index') }}">
        <i class="bi bi-file-earmark-text me-2"></i>Applicants
    </a>
    <a class="nav-link {{ request()->routeIs('feedback.*') ? 'active' : '' }}" href="{{ route('feedback.index') }}">
        <i class="bi bi-chat-square-text me-2"></i>Feedback
    </a>
    <a class="nav-link d-flex align-items-center" href="{{ route('profile.edit') }}#update-password-form">
        <i class="bi bi-key me-2"></i>Change Password
        @if (auth()->user()->needsPasswordChange())
            <i class="bi bi-exclamation-triangle-fill text-warning ms-2" title="You haven't changed your password yet — please update it."></i>
        @endif
    </a>
    <hr>
    <a class="nav-link" href="{{ route('home') }}">
        <i class="bi bi-globe me-2"></i>Public Site
    </a>
</nav>
