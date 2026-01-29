export const initPreviewModal = () => {
    const previewModal = document.getElementById('contentPreviewModal');
    if (!previewModal) {
        return;
    }

    document.querySelectorAll('[data-preview-open]').forEach((button) => {
        button.addEventListener('click', () => {
            previewModal.classList.add('active');
        });
    });

    document.querySelectorAll('[data-preview-close]').forEach((button) => {
        button.addEventListener('click', () => {
            previewModal.classList.remove('active');
        });
    });

    previewModal.addEventListener('click', (event) => {
        if (event.target === previewModal) {
            previewModal.classList.remove('active');
        }
    });
};
