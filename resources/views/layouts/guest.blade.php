<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} | {{ $title ?? 'Welcome' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div class="d-flex min-vh-100">
        <div class="d-none d-lg-flex col-lg-5 flex-column text-white p-5 hero-gradient position-relative overflow-hidden">
            <div class="position-relative" style="z-index:1;">
                <a href="{{ route('home') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none">
                    <x-app-logo height="50" />
                </a>
            </div>
            <div class="position-relative" style="z-index:1; margin-top:300px;">
                <p class="text-uppercase small fw-semibold mb-3" style="letter-spacing:.2em; color:#eafff0;">Apply for doctoral studies</p>
                <h1 class="display-6 fw-bold mb-3">Euromed University of Fès</h1>
                <p class="text-white-50">Browse List of Proposals , submit a complete application online, and track its review status in real time all in one secure platform.</p>
                <div class="d-flex align-items-center gap-3 mt-5">
                </div>
            </div>

            <div class="position-absolute top-0 end-0 opacity-25" style="width:22rem; height:22rem; transform:translate(30%,-30%); border-radius:50%; background:rgba(255,255,255,.08);"></div>
            <div class="position-absolute bottom-0 start-0 opacity-25" style="width:16rem; height:16rem; transform:translate(-30%,30%); border-radius:50%; background:rgba(255,255,255,.08);"></div>
        </div>

        <div class="flex-fill d-flex flex-column justify-content-center px-4 px-sm-5 px-lg-6 py-5 bg-white">
            <div class="d-lg-none mb-4 d-flex align-items-center gap-2">
                <x-app-logo height="32" />
                <span class="fw-bold fs-5" style="color:#005292;">Euromed Doctorate</span>
            </div>

            <div class="auth-card mx-auto w-100">
                {{ $slot }}
            </div>

            <p class="auth-card mx-auto w-100 mt-5 small text-muted">
                &copy; {{ date('Y') }} Euromed University of Fès. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
