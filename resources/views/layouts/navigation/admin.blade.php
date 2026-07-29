<nav class="nav flex-column gap-1">
    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
        <i class="bi bi-speedometer2 me-2"></i>Dashboard
    </a>
    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
        <i class="bi bi-people me-2"></i>Users
    </a>
    <a class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}" href="{{ route('admin.departments.index') }}">
        <i class="bi bi-building me-2"></i>Doctoral Programs
    </a>
    <a class="nav-link {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}" href="{{ route('admin.subjects.index') }}">
        <i class="bi bi-journal-richtext me-2"></i>Research Subjects
    </a>
    <a class="nav-link {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}" href="{{ route('admin.applications.index') }}">
        <i class="bi bi-file-earmark-text me-2"></i>Applications
    </a>
    <a class="nav-link {{ request()->routeIs('admin.feedback.*') ? 'active' : '' }}" href="{{ route('admin.feedback.index') }}">
        <i class="bi bi-inboxes me-2"></i>Feedback Inbox
    </a>
    <a class="nav-link {{ request()->routeIs('feedback.*') ? 'active' : '' }}" href="{{ route('feedback.index') }}">
        <i class="bi bi-chat-square-text me-2"></i>Feedback
    </a>
    <hr>
    <a class="nav-link" href="{{ route('home') }}">
        <i class="bi bi-globe me-2"></i>Public Site
    </a>
</nav>
