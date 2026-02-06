export const initModePrice = () => {
    const modeSelect = document.querySelector('[data-mode-select]');
    const priceInput = document.querySelector('[data-price-input]');

    if (!modeSelect || !priceInput) {
        return;
    }

    modeSelect.querySelectorAll('.select-option').forEach((option) => {
        option.addEventListener('click', () => {
            const modeValue = (option.dataset.optionValue || '').toLowerCase();
            const isTeasing = modeValue === 'teasing';

            priceInput.disabled = isTeasing;

            if (isTeasing) {
                priceInput.value = '';
            }
        });
    });
};
