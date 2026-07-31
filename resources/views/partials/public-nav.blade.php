<nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#005292;">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            <x-app-logo height="60" />
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNavbar" aria-controls="publicNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="publicNavbar">
            <ul class="navbar-nav me-auto ms-lg-4">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active fw-semibold' : '' }}" href="{{ route('home') }}">Home</a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('candidate.subjects.*') ? 'active fw-semibold' : '' }}" href="{{ route('candidate.subjects.index') }}">List of Proposals </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://ueuromed.org/fr" target="_blank" rel="noopener">About Euromed University of Fes</a>
                </li>
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('feedback.*') ? 'active fw-semibold' : '' }}" href="{{ route('feedback.index') }}">Feedback</a>
                    </li>
                @endauth
            </ul>
            <div class="d-flex gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-box-arrow-right me-1"></i>Log Out
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Log in</a>
                    <a href="{{ route('register') }}" class="btn btn-success btn-sm">Register</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

@include('partials.announcement-bar')
