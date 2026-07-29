@props(['label', 'value', 'icon' => 'bi-bar-chart', 'color' => 'primary'])

<div class="stat-card p-3 h-100">
    <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center justify-content-center rounded-3 bg-{{ $color }}-subtle text-{{ $color }}" style="width:3rem; height:3rem;">
            <i class="bi {{ $icon }} fs-4"></i>
        </div>
        <div>
            <p class="text-muted small mb-1">{{ $label }}</p>
            <p class="h4 fw-bold mb-0">{{ $value }}</p>
        </div>
    </div>
</div>
