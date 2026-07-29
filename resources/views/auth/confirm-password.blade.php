<x-guest-layout title="Confirm password">
    <div class="mb-4">
        <p class="text-uppercase small fw-semibold mb-1" style="color:#44A66D; letter-spacing:.15em;">Security check</p>
        <h2 class="fw-bold mb-2">Confirm your password</h2>
        <p class="text-muted">This is a secure area of the application. Please confirm your password before continuing.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <x-primary-button class="w-100 py-2">
            {{ __('Confirm') }}
        </x-primary-button>
    </form>
</x-guest-layout>
