<section>
    <header class="mb-3">
        <h3 class="h5 fw-bold mb-1 text-danger">{{ __('Delete Account') }}</h3>
        <p class="text-muted small mb-0">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirm-user-deletion">
        {{ __('Delete Account') }}
    </button>

    <x-modal id="confirm-user-deletion" title="{{ __('Are you sure you want to delete your account?') }}">
        <form method="post" action="{{ route('profile.destroy') }}" id="delete-account-form">
            @csrf
            @method('delete')

            <p class="text-muted small">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <x-input-label for="password_delete" value="{{ __('Password') }}" class="visually-hidden" />
            <x-text-input id="password_delete" name="password" type="password" placeholder="{{ __('Password') }}" />
            <x-input-error :messages="$errors->userDeletion->get('password')" />
        </form>

        <x-slot name="footer">
            <x-secondary-button data-bs-dismiss="modal">{{ __('Cancel') }}</x-secondary-button>
            <x-danger-button type="submit" form="delete-account-form">{{ __('Delete Account') }}</x-danger-button>
        </x-slot>
    </x-modal>
</section>
