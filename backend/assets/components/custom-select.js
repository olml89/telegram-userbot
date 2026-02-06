export const initCustomSelects = () => {
    document.querySelectorAll('.custom-select').forEach((select) => {
        const trigger = select.querySelector('.select-trigger');
        const valueEl = select.querySelector('.select-value');
        const options = select.querySelectorAll('.select-option');

        if (!trigger || !valueEl) {
            return;
        }

        trigger.addEventListener('click', () => {
            const isOpen = select.classList.contains('open');
            document.querySelectorAll('.custom-select.open').forEach((el) => el.classList.remove('open'));

            if (!options.length) {
                return;
            }

            if (!isOpen) {
                select.classList.add('open');
            }
        });

        options.forEach((option) => {
            option.addEventListener('click', () => {
                const label = option.dataset.optionLabel || option.textContent;
                const selectedValue = option.dataset.optionValue || option.textContent;

                if (selectedValue) {
                    select.dataset.value = selectedValue;
                }

                if (label) {
                    select.dataset.label = label;
                    valueEl.textContent = label;
                }

                if (option.dataset.optionId) {
                    select.dataset.id = option.dataset.optionId;
                    select.dataset.name = option.dataset.optionName || label;
                }

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
