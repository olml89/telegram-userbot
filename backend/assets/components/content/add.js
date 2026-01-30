export const initAddModal = () => {
    const addModal = document.getElementById('contentAddModal');
    const addOpenBtn = document.querySelector('[data-add-open]');
    const addCloseBtn = document.querySelector('[data-add-close]');

    if (!addModal || !addOpenBtn || !addCloseBtn) {
        return;
    }

    addOpenBtn.addEventListener('click', () => {
        addModal.classList.add('active');
    });

    addCloseBtn.addEventListener('click', () => {
        addModal.classList.remove('active');
    })

    /**
     * Automatically close modal if we click the backdrop
     */
    addModal.addEventListener('click', (event) => {
        if (event.target === addModal) {
            addModal.classList.remove('active');
        }
    });
};
