export const initIntensity = () => {
    const range = document.querySelector('[data-intensity-range]');
    const value = document.querySelector('[data-intensity-value]');

    if (!range || !value) {
        return;
    }

    value.textContent = range.value;

    range.addEventListener('input', () => {
        value.textContent = range.value;
    });
};
