export const initPreviewModal = () => {
    const previewModal = document.getElementById('contentPreviewModal');
    const previewOpenBtn = document.querySelector('[data-preview-open]');
    const previewCloseBtn = document.querySelector('[data-preview-close]');

    if (!previewModal || !previewOpenBtn || !previewCloseBtn) {
        return;
    }

    previewOpenBtn.addEventListener('click', () => {
        previewModal.classList.add('active');
    });

    previewCloseBtn.addEventListener('click', () => {
        previewModal.classList.remove('active');
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
