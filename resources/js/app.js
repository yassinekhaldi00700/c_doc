import './bootstrap';
import './recruitment-report';
import './oral-exam-picks';
import * as bootstrap from 'bootstrap';
import htmx from 'htmx.org';

window.bootstrap = bootstrap;
window.htmx = htmx;

/**
 * Client-side PDF document validation.
 *
 * Server-side validation is the source of truth, but browsers cannot
 * repopulate <input type="file"> after a redirect, so catching type/size
 * mistakes before submit avoids forcing candidates to re-select every
 * document after a single validation error.
 *
 * Re-run on every htmx swap (not just the initial page load), since the
 * candidate profile forms are hx-boosted and their markup — including
 * these file inputs — gets replaced without a full page reload.
 */
function initPdfInputValidation() {
    document.querySelectorAll('.js-pdf-input').forEach((input) => {
        const feedback = input.parentElement.querySelector('.js-pdf-input-feedback');
        const maxBytes = parseFloat(input.dataset.maxSizeMb || '10') * 1024 * 1024;

        input.addEventListener('change', () => {
            const files = Array.from(input.files || []);
            const invalidType = files.find((file) => file.type && file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf'));
            const invalidSize = files.find((file) => file.size > maxBytes);

            if (invalidType) {
                showPdfInputError(input, feedback, `"${invalidType.name}" is not a PDF file.`);
            } else if (invalidSize) {
                showPdfInputError(input, feedback, `"${invalidSize.name}" exceeds the ${input.dataset.maxSizeMb} MB limit.`);
            } else {
                clearPdfInputError(input, feedback);
            }
        });
    });

    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const hasInvalidFile = form.querySelector('.js-pdf-input.is-invalid');

            if (hasInvalidFile) {
                event.preventDefault();
                event.stopImmediatePropagation();
                hasInvalidFile.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', initPdfInputValidation);
document.body.addEventListener('htmx:afterSwap', initPdfInputValidation);

function initPrivacyConsent() {
    document.querySelectorAll('[data-bs-toggle="tooltip"][data-bs-custom-class="cndp-tooltip"]').forEach((element) => {
        bootstrap.Tooltip.getOrCreateInstance(element);
    });

    document.querySelectorAll('[data-privacy-consent-form]').forEach((form) => {
        if (form.dataset.privacyConsentBound === 'true') {
            return;
        }

        form.dataset.privacyConsentBound = 'true';
        const consent = form.querySelector('input[name="privacy_consent"]');

        if (!consent) {
            return;
        }

        const error = document.getElementById(`${consent.id}_errors`);

        consent.addEventListener('change', () => {
            if (consent.checked) {
                consent.classList.remove('is-invalid');

                if (error?.dataset.clientError === 'true') {
                    error.textContent = '';
                    delete error.dataset.clientError;
                }
            }
        });

        form.addEventListener('submit', (event) => {
            if (event.submitter?.formNoValidate || consent.checked) {
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();
            consent.classList.add('is-invalid');

            if (error) {
                error.textContent = 'You must consent to the processing of your personal data before continuing.';
                error.dataset.clientError = 'true';
            }

            consent.focus();
            consent.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    });
}

document.addEventListener('DOMContentLoaded', initPrivacyConsent);
document.body.addEventListener('htmx:afterSwap', initPrivacyConsent);

/**
 * Some profile fields are only required for a specific degree track (e.g.
 * the license/bachelor certificate is required for Master, optional for
 * Engineering). The server already enforces this, but without this handler
 * the required/optional indicator only reflects the track chosen on the
 * last page load — flipping the radio wouldn't update it until saving.
 *
 * It also has to clear any leftover "required" error from a previous submit
 * (the is-invalid class + error text) once a field becomes optional under
 * the newly selected track. Otherwise the stale is-invalid class on the
 * file input keeps tripping the submit-blocking check in
 * initPdfInputValidation() above, which silently no-ops the next submit —
 * looking exactly like the server is still rejecting the (now-optional)
 * field, when really the form never made it to the server at all.
 */
function initDegreeTrackToggle() {
    const radios = document.querySelectorAll('input[name="degree_track"]');

    if (!radios.length) {
        return;
    }

    const applyTrack = (track) => {
        document.querySelectorAll('[data-required-for-track]').forEach((field) => {
            const required = field.dataset.requiredForTrack === track;
            field.required = required;

            const badge = document.getElementById(`${field.id}_required_badge`);
            if (badge) {
                badge.textContent = required ? '*' : '(optional)';
                badge.className = required ? 'text-danger' : 'text-muted small';
            }

            if (!required) {
                field.classList.remove('is-invalid');

                const feedback = document.getElementById(`${field.id}_errors`)
                    || field.parentElement.querySelector('.js-pdf-input-feedback');
                if (feedback) {
                    feedback.textContent = '';
                    feedback.style.display = 'none';
                }
            }
        });
    };

    radios.forEach((radio) => {
        radio.addEventListener('change', () => applyTrack(radio.value));
    });

    const checked = document.querySelector('input[name="degree_track"]:checked');
    if (checked) {
        applyTrack(checked.value);
    }
}

document.addEventListener('DOMContentLoaded', initDegreeTrackToggle);
document.body.addEventListener('htmx:afterSwap', initDegreeTrackToggle);

/**
 * On the professor's "Edit Research Subject" form, description/
 * responsibilities/candidate profile/keywords are meant to move together —
 * a subject shouldn't end up with keywords but no stated responsibilities
 * or candidate profile. The server enforces this too
 * (SubjectRequest::withValidator), but catching it client-side avoids a
 * round trip for the common case of forgetting one of the four fields.
 */
function initSubjectContentGroup() {
    const description = document.getElementById('description');
    const responsibilities = document.getElementById('responsibilities');
    const candidateProfile = document.getElementById('candidate_profile');
    const keywords = document.getElementById('keywords');
    const errorBox = document.getElementById('content-group-error');

    if (!description || !responsibilities || !candidateProfile || !keywords || !errorBox) {
        return;
    }

    const form = description.closest('form');
    if (!form) {
        return;
    }

    const fields = [description, responsibilities, candidateProfile, keywords];

    form.addEventListener('submit', (event) => {
        const filledCount = fields.filter((field) => field.value.trim() !== '').length;
        const inconsistent = filledCount > 0 && filledCount < fields.length;

        fields.forEach((field) => field.classList.toggle('is-invalid', inconsistent && field.value.trim() === ''));
        errorBox.classList.toggle('d-none', !inconsistent);

        if (inconsistent) {
            event.preventDefault();
            event.stopImmediatePropagation();
            errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
}

document.addEventListener('DOMContentLoaded', initSubjectContentGroup);
document.body.addEventListener('htmx:afterSwap', initSubjectContentGroup);

/**
 * On the admin "Review Decision" form, the oral exam date / program start
 * date only make sense (and are only required) for their matching status —
 * show/require each just for that choice, so the browser's native
 * required-field validation blocks submission until the right date is
 * picked.
 */
function initReviewDecisionForm() {
    const statusSelect = document.getElementById('status');

    if (!statusSelect) {
        return;
    }

    const dateFields = [
        { status: 'under_review', field: document.getElementById('oral-exam-date-field'), input: document.getElementById('oral_exam_date') },
        { status: 'accepted', field: document.getElementById('program-start-date-field'), input: document.getElementById('program_start_date') },
    ].filter(({ field, input }) => field && input);

    if (!dateFields.length) {
        return;
    }

    const sync = () => {
        dateFields.forEach(({ status, field, input }) => {
            const isActive = statusSelect.value === status;
            field.classList.toggle('d-none', !isActive);
            input.required = isActive;
        });
    };

    statusSelect.addEventListener('change', sync);
    sync();
}

document.addEventListener('DOMContentLoaded', initReviewDecisionForm);
document.body.addEventListener('htmx:afterSwap', initReviewDecisionForm);

/**
 * htmx boosts full-page navigations/form submits into AJAX requests that
 * swap the response's <body> in place, so multi-step flows like the
 * candidate profile wizard feel instant instead of doing a hard reload —
 * while every route keeps working exactly the same for non-JS clients,
 * since the server still returns full normal Blade pages either way.
 */
document.body.addEventListener('htmx:beforeSwap', (event) => {
    // Laravel returns a normal HTML error page for non-2xx/3xx responses
    // (e.g. a 403/419/500); still swap it in so the message is visible
    // instead of htmx silently no-op'ing on "unexpected" status codes.
    if (event.detail.xhr.status >= 400) {
        event.detail.shouldSwap = true;
        event.detail.isError = false;
    }
});

function showPdfInputError(input, feedback, message) {
    input.classList.add('is-invalid');
    input.value = '';

    if (feedback) {
        feedback.textContent = message;
        feedback.style.display = 'block';
    }
}

function clearPdfInputError(input, feedback) {
    input.classList.remove('is-invalid');

    if (feedback) {
        feedback.textContent = '';
        feedback.style.display = 'none';
    }
}
