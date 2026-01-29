export const initCustomSelects = () => {
    document.querySelectorAll('.custom-select').forEach((select) => {
        const trigger = select.querySelector('.select-trigger');
        const value = select.querySelector('.select-value');
        const options = select.querySelectorAll('.select-option');

        if (!trigger || !value) {
            return;
        }

        trigger.addEventListener('click', () => {
            const isOpen = select.classList.contains('open');
            document.querySelectorAll('.custom-select.open').forEach((el) => el.classList.remove('open'));
            if (!isOpen) {
                select.classList.add('open');
            }
        });

        options.forEach((option) => {
            option.addEventListener('click', () => {
                value.textContent = option.textContent;
                select.dataset.value = option.textContent;
                select.classList.remove('open');
            });
        });
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('.custom-select')) {
            document.querySelectorAll('.custom-select.open').forEach((el) => el.classList.remove('open'));
        }
    });
};
