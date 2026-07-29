@props(['document'])

{{--
    No nested <form> here on purpose: this chip is rendered inside the
    page's big Personal/Academic <form>, and browsers silently break nested
    forms (the delete button's fields get hoisted into the outer form,
    submitting to the outer form's action instead). A plain button with
    formaction/formmethod safely overrides the outer form's target for just
    this one submit, without nesting.
--}}
<div class="d-flex align-items-center gap-2 mb-2">
    <a href="{{ route('candidate.profile.documents.preview', $document) }}"
       target="_blank"
       rel="noopener"
       class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
        <i class="bi {{ $document->type->icon() }}"></i>
        <span>{{ $document->original_name }}</span>
        <i class="bi bi-box-arrow-up-right small"></i>
    </a>
    {{-- hx-boost="false": the enclosing page form is hx-boosted, but htmx's
         boosted submit handling reads the <form>'s own action/method and
         does not honor a submitter button's formaction/formmethod override
         the way native browser submission does — so this one button must
         opt back out and submit natively (still a fast, targeted request;
         only this one action does a normal navigation). --}}
    <button type="submit"
            formaction="{{ route('candidate.profile.documents.destroy', $document) }}?_method=DELETE"
            formmethod="POST"
            formnovalidate
            hx-boost="false"
            class="btn btn-sm btn-outline-danger"
            title="Remove"
            onclick="return confirm('Remove this document?');">
        <i class="bi bi-trash"></i>
    </button>
</div>
