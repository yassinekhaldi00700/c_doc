<x-app-layout title="New Doctoral Program">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.departments.store') }}">
                @csrf
                @include('admin.departments._form', ['department' => null])
            </form>
        </div>
    </div>
</x-app-layout>
