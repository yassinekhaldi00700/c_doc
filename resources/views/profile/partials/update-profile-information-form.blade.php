@php $readOnly = $user->isProfessor(); @endphp

<section>
    <header class="mb-4">
        <h3 class="h5 fw-bold mb-1">{{ __('Profile Information') }}</h3>
        <p class="text-muted small mb-0">
            {{ $readOnly ? __('Your profile information, managed by an administrator.') : __("Update your account's profile information and email address.") }}
        </p>
    </header>

    @if ($readOnly)
        <div class="mb-3">
            <x-input-label value="{{ __('Name') }}" />
            <p class="form-control-plaintext fw-semibold">{{ $user->name }}</p>
        </div>

        <div class="mb-3">
            <x-input-label value="{{ __('Email') }}" />
            <p class="form-control-plaintext fw-semibold">{{ $user->email }}</p>
        </div>

        <div class="mb-0">
            <x-input-label value="{{ __('Phone number') }}" />
            <p class="form-control-plaintext fw-semibold">{{ $user->phone ?: '—' }}</p>
        </div>

        <p class="small text-muted mb-0"><i class="bi bi-info-circle me-1"></i>{{ __('Contact an administrator if any of this needs to change.') }}</p>
    @else
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')

            <div class="mb-3">
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" />
            </div>

            <div class="mb-3">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2">
                        <p class="small text-body mb-1">
                            {{ __('Your email address is unverified.') }}
                            <button form="send-verification" class="btn btn-link btn-sm p-0 align-baseline">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="small text-success mb-0">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="mb-3">
                <x-input-label for="phone" :value="__('Phone number')" />
                <x-text-input id="phone" name="phone" type="text" :value="old('phone', $user->phone)" autocomplete="tel" />
                <x-input-error :messages="$errors->get('phone')" />
            </div>

            <div class="d-flex align-items-center gap-3">
                <x-primary-button>{{ __('Save') }}</x-primary-button>

                @if (session('status') === 'profile-updated')
                    <p class="small text-success mb-0">{{ __('Saved.') }}</p>
                @endif
            </div>
        </form>
    @endif
</section>
