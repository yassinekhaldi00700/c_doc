<x-app-layout title="Edit User">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')
                @include('admin.users._form', ['user' => $user])
            </form>
        </div>
    </div>
</x-app-layout>
