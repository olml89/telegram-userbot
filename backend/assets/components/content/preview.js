export const initPreviewModal = () => {
    const previewModal = document.getElementById('contentPreviewModal');
    const previewOpenBtn = document.querySelector('[data-preview-open]');

    if (!previewModal || !previewOpenBtn) {
        return;
    }

    previewOpenBtn.addEventListener('click', () => {
        previewModal.classList.add('active');
    });

    /**
     * Support all close buttons
     */
    document.querySelectorAll('[data-preview-close]').forEach((button) => {
        button.addEventListener('click', () => {
            previewModal.classList.remove('active');
        });
    });

    /**
     * Automatically close modal if we click the backdrop
     */
    previewModal.addEventListener('click', (event) => {
        if (event.target === previewModal) {
            previewModal.classList.remove('active');
        }
    });
};
