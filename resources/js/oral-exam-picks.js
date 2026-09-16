function initOralExamPicks() {
    document.querySelectorAll('[data-oral-exam-picks-form]').forEach((form) => {
        if (form.dataset.bound) return;
        form.dataset.bound = 'true';
        const max = parseInt(form.dataset.max || '5', 10);
        const checkboxes = Array.from(form.querySelectorAll('[data-oral-exam-pick-checkbox]'));
        const counter = form.querySelector('[data-oral-exam-picks-counter]');
        const dateInputFor = (box) => box.closest('tr')?.querySelector('[data-oral-exam-pick-date]');

        const sync = () => {
            const checked = checkboxes.filter((box) => box.checked).length;
            checkboxes.forEach((box) => {
                box.disabled = ! box.checked && checked >= max;
                const dateInput = dateInputFor(box);
                if (dateInput) dateInput.required = box.checked;
            });
            if (counter) counter.textContent = `${checked} / ${max} selected`;
        };

        checkboxes.forEach((box) => box.addEventListener('change', sync));

        // Date fields are always editable. Typing a date picks that candidate
        // automatically, instead of forcing the checkbox first — as long as
        // there's still room under the 5-candidate cap.
        checkboxes.forEach((box) => {
            const dateInput = dateInputFor(box);
            if (! dateInput) return;
            dateInput.addEventListener('input', () => {
                if (! dateInput.value || box.checked) return;
                const checked = checkboxes.filter((b) => b.checked).length;
                if (checked >= max) {
                    dateInput.value = '';
                    return;
                }
                box.checked = true;
                sync();
            });
        });

        sync();
    });
}
document.addEventListener('DOMContentLoaded', initOralExamPicks);
document.addEventListener('htmx:afterSwap', initOralExamPicks);
