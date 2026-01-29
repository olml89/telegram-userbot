export const initAddModal = () => {
    const addModal = document.getElementById('contentAddModal');
    const addOpenBtn = document.getElementById('openModalBtn');

    if (!addModal || !addOpenBtn) {
        return;
    }

    addOpenBtn.addEventListener('click', () => {
        addModal.classList.add('active');
    });

    document.querySelectorAll('[data-add-close]').forEach((button) => {
        button.addEventListener('click', () => {
            addModal.classList.remove('active');
        });
    });

    addModal.addEventListener('click', (event) => {
        if (event.target === addModal) {
            addModal.classList.remove('active');
        }
    });
};
