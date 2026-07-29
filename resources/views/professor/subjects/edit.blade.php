<x-app-layout title="Edit Research Subject">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('professor.subjects.update', $subject) }}">
                @csrf
                @method('PUT')
                @include('professor.subjects._form', ['subject' => $subject])
            </form>
        </div>
    </div>
</x-app-layout>
