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
                if (dateInput) {
                    dateInput.disabled = ! box.checked;
                    dateInput.required = box.checked;
                }
            });
            if (counter) counter.textContent = `${checked} / ${max} selected`;
        };

        checkboxes.forEach((box) => box.addEventListener('change', sync));
        sync();
    });
}
document.addEventListener('DOMContentLoaded', initOralExamPicks);
document.addEventListener('htmx:afterSwap', initOralExamPicks);
