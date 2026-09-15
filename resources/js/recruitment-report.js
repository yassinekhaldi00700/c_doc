function initRecruitmentReport() {
    document.querySelectorAll('[data-recruitment-form]').forEach((form) => {
        if (form.dataset.bound) return;
        form.dataset.bound = 'true';
        const sync = () => {
            const ids = Array.from(form.querySelectorAll('[data-result-select="shortlist"]')).map((select) => select.value).filter(Boolean);
            form.querySelectorAll('[data-result-select="interviews"]').forEach((select) => {
                Array.from(select.options).forEach((option) => {
                    option.disabled = Boolean(option.value && !ids.includes(option.value));
                    option.hidden = option.disabled;
                });
                if (select.value && !ids.includes(select.value)) {
                    select.value = '';
                    form.querySelector(`#${select.id}_score`).value = '';
                }
            });
            form.querySelectorAll('[data-committee-row]').forEach((row) => {
                const selected = Boolean(row.querySelector('[data-committee-professor]').value);
                row.querySelectorAll('[data-committee-manual]').forEach((input) => { input.disabled = selected; });
            });
        };
        form.addEventListener('change', sync);
        sync();
    });
}
document.addEventListener('DOMContentLoaded', initRecruitmentReport);
document.addEventListener('htmx:afterSwap', initRecruitmentReport);
