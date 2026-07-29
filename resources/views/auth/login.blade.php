<x-guest-layout title="Sign in">
    <div class="mb-4">
        <p class="text-uppercase small fw-semibold mb-1" style="color:#44A66D; letter-spacing:.15em;">Candidate &amp; Staff Access</p>
        <h2 class="fw-bold mb-2">Sign in to continue</h2>
        <p class="text-muted">Access your doctoral application dashboard and manage your academic documents securely.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="mb-3">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                <label class="form-check-label small" for="remember_me">{{ __('Remember me') }}</label>
            </div>

            @if (Route::has('password.request'))
                <a class="small fw-semibold text-decoration-none" href="{{ route('password.request') }}" style="color:#005292;">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <x-primary-button class="w-100 py-2">
            {{ __('Log in') }}
        </x-primary-button>
    </form>

    <div class="mt-4 pt-4 border-top text-center small text-muted">
        <span>Need an account?</span>
        <a href="{{ route('register') }}" class="fw-semibold text-decoration-none ms-1" style="color:#005292;">Create one</a>
    </div>
</x-guest-layout>
