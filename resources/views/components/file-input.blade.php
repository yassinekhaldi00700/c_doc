@props(['name', 'label', 'icon' => 'bi-file-earmark-pdf', 'required' => false, 'multiple' => false, 'hint' => null, 'maxSizeMb' => 10])

@php
    $fieldName = 'documents['.$name.']'.($multiple ? '[]' : '');
    $errorKey = 'documents.'.$name.($multiple ? '.0' : '');
    $inputId = 'document_'.$name;
@endphp

<div class="mb-3">
    <x-input-label :for="$inputId">
        <i class="bi {{ $icon }} me-1 text-primary"></i>{{ $label }}
        <span id="{{ $inputId }}_required_badge" class="{{ $required ? 'text-danger' : 'text-muted small' }}">{{ $required ? '*' : '(optional)' }}</span>
    </x-input-label>

    {{-- The enclosing <form> carries `novalidate`, so this `required`
         attribute never silently blocks submission via a native browser
         tooltip (which can appear off-screen, e.g. next to a hidden radio
         input) — it's only an accessibility/semantic hint. The actual
         requirement is enforced server-side, where the error is always
         shown clearly on the page. --}}
    <input type="file"
           id="{{ $inputId }}"
           name="{{ $fieldName }}"
           @if($multiple) multiple @endif
           @if($required) required @endif
           accept="application/pdf"
           data-max-size-mb="{{ $maxSizeMb }}"
           {{ $attributes->class(['form-control js-pdf-input', 'is-invalid' => $errors->has($errorKey)]) }} />

    <div class="invalid-feedback js-pdf-input-feedback"></div>

    @if ($hint)
        <div class="form-text">{{ $hint }} Maximum {{ $maxSizeMb }} MB per file.</div>
    @else
        <div class="form-text">Maximum {{ $maxSizeMb }} MB per file.</div>
    @endif

    <x-input-error :id="$inputId.'_errors'" :messages="collect($errors->get('documents.'.$name.'*'))->flatten()->all()" />
</div>
