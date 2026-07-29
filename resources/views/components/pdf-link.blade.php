@props(['document'])

<a href="{{ route('documents.preview', $document) }}"
   target="_blank"
   rel="noopener"
   {{ $attributes->merge(['class' => 'btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1']) }}>
    <i class="bi {{ $document->type->icon() }}"></i>
    <span>{{ $document->type->label() }}</span>
    <i class="bi bi-box-arrow-up-right small"></i>
</a>
