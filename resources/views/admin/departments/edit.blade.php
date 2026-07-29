<x-app-layout title="Edit Doctoral Program">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.departments.update', $department) }}">
                @csrf
                @method('PUT')
                @include('admin.departments._form', ['department' => $department])
            </form>
        </div>
    </div>
</x-app-layout>
