<x-app-layout title="Feedback Inbox">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if ($feedback->isEmpty())
                <p class="text-muted mb-0">No feedback has been submitted yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th>From</th>
                                <th>Role</th>
                                <th>Message</th>
                                <th>Submitted</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($feedback as $item)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $item->user->name }}</div>
                                        <div class="text-muted small">{{ $item->user->email }}</div>
                                    </td>
                                    <td><span class="badge {{ $item->user->role->badgeClass() }}">{{ $item->user->role->label() }}</span></td>
                                    <td style="max-width: 420px;">{{ $item->message }}</td>
                                    <td class="text-muted small text-nowrap">{{ $item->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $feedback->links() }}
            @endif
        </div>
    </div>
</x-app-layout>
