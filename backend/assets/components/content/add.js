export const initAddModal = () => {
    const addModal = document.getElementById('contentAddModal');
    const addOpenBtn = document.querySelector('[data-add-open]');

    if (!addModal || !addOpenBtn) {
        return;
    }

    addOpenBtn.addEventListener('click', () => {
        addModal.classList.add('active');
    });

    /**
     * Support all close buttons
     */
    document.querySelectorAll('[data-add-close]').forEach((button) => {
        button.addEventListener('click', () => {
            addModal.classList.remove('active');
        });
    });

    /**
     * Automatically close modal if we click the backdrop
     */
    addModal.addEventListener('click', (event) => {
        if (event.target === addModal) {
            addModal.classList.remove('active');
        }
    });
};
