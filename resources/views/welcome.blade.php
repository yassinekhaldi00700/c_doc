<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} | Apply for doctoral studies</title>

    <link rel="icon" type="image/jpeg" href="{{ asset('img/logo_without_text.jpeg') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body>
    @include('partials.public-nav')

    <header class="hero-gradient text-white py-5">
        <div class="container py-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <p class="text-uppercase  fw-semibold mb-3" style="letter-spacing:.2em; color:#eafff0;">Apply for doctoral studies</p>
                    <h1 class="display-5 fw-bold mb-3"> Euromed University of Fès</h1>
                    <p class="lead text-white-50 mb-4">Browse list of proposals, submit a complete application online, and track its progression status in real time  all in one secure platform.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('candidate.subjects.index') }}" class="btn btn-light btn-lg px-4">
                            <i class="bi bi-search me-1"></i> Browse List of Proposals
                        </a>
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-4">Create an Account</a>
                        @endguest
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="bg-white bg-opacity-10 rounded-4 p-4 border border-white border-opacity-25">
                        <div class="row text-center g-3">
                            <div class="col-6">
                                <p class="display-6 fw-bold mb-0">{{ $totalOpenSubjects }}</p>
                                <p class="small text-white-50 mb-0">Open Subjects</p>
                            </div>
                            <div class="col-6">
                                <p class="display-6 fw-bold mb-0">3</p>
                                <p class="small text-white-50 mb-0">Doctoral Programs</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container py-5">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h2 class="h3 fw-bold mb-1">List of Proposals currently open for applications</h2>

            </div>
            <a href="{{ route('candidate.subjects.index') }}" class="btn btn-outline-primary d-none d-md-inline-flex">View all</a>
        </div>

        <div id="subjects-panel" hx-target="#subjects-results" hx-select="#subjects-results" hx-swap="outerHTML" hx-push-url="true" hx-boost="true">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form hx-get="{{ route('home') }}" hx-trigger="submit, keyup changed delay:400ms from:#search, change from:#department_id" class="row g-2 align-items-end">
                        <div class="col-md-6">
                            <x-input-label for="search" value="Search by title" />
                            <x-text-input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="e.g. Machine Learning..." autocomplete="off" />
                        </div>
                        <div class="col-md-4">
                            <x-input-label for="department_id" value="Doctoral Program" />
                            <select id="department_id" name="department_id" class="form-select">
                                <option value="">All doctoral programs</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" @selected(request('department_id') == $department->id)>{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="subjects-results">
                @if ($subjects->isEmpty())
                    <div class="alert alert-info">No research subjects match your search criteria.</div>
                @else
                    <div class="row g-4">
                        @foreach ($subjects as $subject)
                            <div class="col-md-6 col-lg-4">
                                <div class="subject-card card h-100 border-0 shadow-sm">
                                    <div class="card-body d-flex flex-column">
                                        <span class="badge bg-primary-subtle text-primary mb-2 align-self-start">{{ $subject->department->name }}</span>
                                        <h3 class="h5 fw-bold mb-2">{{ $subject->title }}</h3>
                                        <p class="text-muted small mb-3 flex-grow-1">{{ \Illuminate\Support\Str::limit($subject->description, 120) }}</p>
                                        <p class="small text-muted mb-3">
                                            <i class="bi bi-person-badge me-1"></i>{{ $subject->professor->name }}
                                        </p>
                                        @guest
                                            <a href="{{ route('login') }}" hx-boost="false" class="btn btn-outline-primary btn-sm mt-auto">Log in to Apply</a>
                                        @else
                                            <a href="{{ route('candidate.subjects.show', $subject) }}" hx-boost="false" class="btn btn-outline-primary btn-sm mt-auto">View Details</a>
                                        @endguest
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $subjects->links() }}
                    </div>
                @endif
            </div>
        </div>
    </main>

    @include('partials.public-footer')
</body>
</html>
