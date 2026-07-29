@props(['status'])

<span {{ $attributes->merge(['class' => 'badge rounded-pill status-badge '.$status->badgeClass()]) }}>
    <i class="bi {{ $status->icon() }} me-1"></i>{{ $status->label() }}
</span>
