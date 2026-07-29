<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>About | {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body>
    @include('partials.public-nav')

    <header class="hero-gradient text-white py-5">
        <div class="container py-4 text-center">
            <p class="text-uppercase small fw-semibold mb-3" style="letter-spacing:.2em; color:#eafff0;">About Euromed University of Fès</p>
            <h1 class="display-6 fw-bold mb-3">Plus qu'un diplôme, un profil Euromed</h1>
            <p class="lead text-white-50 mb-0 mx-auto" style="max-width: 46rem;">
                More than a degree — an Euromed profile. UEMF promotes intercultural dialogue, academic
                cooperation, and scientific research of excellence across the Euro-Mediterranean region.
            </p>
        </div>
    </header>

    <main class="container py-5">
        <div class="row g-3 mb-5">
            <div class="col-6 col-lg-3">
                <div class="stat-card p-3 h-100 text-center">
                    <p class="h3 fw-bold mb-1 text-primary">11</p>
                    <p class="small text-muted mb-0">Institutions, across 3 academic poles</p>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card p-3 h-100 text-center">
                    <p class="h3 fw-bold mb-1 text-primary">~40</p>
                    <p class="small text-muted mb-0">Programs, from preparatory cycles to doctorate</p>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card p-3 h-100 text-center">
                    <p class="h3 fw-bold mb-1 text-primary">50+</p>
                    <p class="small text-muted mb-0">Nationalities on campus</p>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card p-3 h-100 text-center">
                    <p class="h3 fw-bold mb-1 text-primary">43</p>
                    <p class="small text-muted mb-0">Union for the Mediterranean member nations</p>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h2 class="h4 fw-bold mb-3"><i class="bi bi-mortarboard me-2 text-primary"></i>Who we are</h2>
                        <p class="mb-3">
                            Euromed University of Fès is the first non-profit university in Morocco to hold
                            public-utility status, established under the patronage of His Majesty King
                            Mohammed VI. It carries the label of the Union for the Mediterranean, reflecting
                            its mission to serve academic cooperation and sustainable development across the
                            Euro-Mediterranean region.
                        </p>
                        <p class="mb-0">
                            The university brings together 11 institutions organized into three academic
                            poles — Engineering &amp; Architecture, Humanities &amp; Social Sciences, and
                            Health Sciences — offering close to 40 programs from preparatory cycles through
                            undergraduate, master's, engineering, and doctoral studies, several through
                            double-diploma partnerships with other Euro-Mediterranean universities.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h2 class="h4 fw-bold mb-3"><i class="bi bi-globe-europe-africa me-2 text-primary"></i>Campus &amp; community</h2>
                        <ul class="list-unstyled mb-0 small">
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>International eco-campus, home to students of 50+ nationalities</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Morocco's largest university sports complex</li>
                            <li class="mb-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Residential facilities and conference centers</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-5">
            <div class="card-body p-4">
                <h2 class="h4 fw-bold mb-3"><i class="bi bi-award me-2 text-primary"></i>Distinctions</h2>
                <div class="row g-3">
                    <div class="col-md-6 col-lg-3">
                        <p class="fw-semibold mb-1 small text-uppercase text-muted">Digital Engineering</p>
                        <p class="mb-0 small">Ranked among Africa's top engineering schools for digital engineering and AI.</p>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <p class="fw-semibold mb-1 small text-uppercase text-muted">3D Printing</p>
                        <p class="mb-0 small">Morocco's first 3D printing platform, with 70+ machines.</p>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <p class="fw-semibold mb-1 small text-uppercase text-muted">Industry Research</p>
                        <p class="mb-0 small">A national leader in industry-funded research contracts.</p>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <p class="fw-semibold mb-1 small text-uppercase text-muted">Industry 4.0</p>
                        <p class="mb-0 small">A dedicated 4.0 factory campus facility for applied research.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm hero-gradient text-white">
            <div class="card-body p-4 p-md-5 text-center">
                <h2 class="h4 fw-bold mb-2">Doctoral research at Euromed</h2>
                <p class="text-white-50 mb-4 mx-auto" style="max-width: 40rem;">
                    This platform is where that mission continues — browse open doctoral research subjects
                    proposed by our faculty, submit a complete application online, and track its review
                    status in real time.
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="{{ route('candidate.subjects.index') }}" class="btn btn-light btn-lg px-4">
                        <i class="bi bi-search me-1"></i> Browse Research Subjects
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-4">Create an Account</a>
                    @endguest
                </div>
            </div>
        </div>
    </main>

    @include('partials.public-footer')
</body>
</html>
