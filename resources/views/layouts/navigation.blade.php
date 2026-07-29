<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background-color:#005292;">
    <div class="container-fluid px-3 px-lg-4">
        <button class="btn btn-link text-white d-lg-none me-2 p-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas">
            <i class="bi bi-list fs-3"></i>
        </button>

        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
            <x-app-logo height="50" />
        </a>

        <div class="d-flex align-items-center gap-2 ms-auto">
            @auth
                <span class="badge {{ auth()->user()->role->badgeClass() }} d-none d-md-inline-flex align-items-center">
                    {{ auth()->user()->role->label() }}
                </span>

                <div class="dropdown">
                    <button class="btn btn-outline-light btn-sm dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i>
                        <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>{{ __('Profile') }}</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>{{ __('Log Out') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth
        </div>
    </div>
</nav>
