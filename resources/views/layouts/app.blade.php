<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title.' | '.config('app.name') : config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell">
        @include('layouts.navigation')

        <div class="container-fluid flex-fill">
            <div class="row">
                <aside class="col-lg-3 col-xl-2 px-0 d-none d-lg-block sidebar">
                    <div class="p-3">
                        @include('layouts.navigation.' . (auth()->user()?->role?->name ? strtolower(auth()->user()->role->name) : 'candidate'))
                    </div>
                </aside>

                <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="sidebarOffcanvas">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title">Menu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                    </div>
                    <div class="offcanvas-body">
                        @include('layouts.navigation.' . (auth()->user()?->role?->name ? strtolower(auth()->user()->role->name) : 'candidate'))
                    </div>
                </div>

                <main class="col-lg-9 col-xl-10 app-main">
                    @if ($title)
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                            <h1 class="h3 fw-bold mb-0 text-body">{{ $title }}</h1>
                        </div>
                    @endif

                    <x-flash-messages />

                    {{ $slot }}
                </main>
            </div>
        </div>
    </div>
</body>
</html>
