export const initPreviewModal = () => {
    const previewModal = document.getElementById('contentPreviewModal');
    const previewOpenBtn = document.querySelector<HTMLButtonElement>('[data-content-preview-open]');

    if (!previewModal || !previewOpenBtn) {
        return;
    }

    previewOpenBtn.addEventListener('click', (): void => previewModal.classList.add('is-active'));

    /**
     * Support all close buttons
     */
    document.querySelectorAll<HTMLButtonElement>('[data-preview-close]').forEach((button: HTMLButtonElement): void => {
        button.addEventListener('click', (): void => {
            previewModal.classList.remove('is-active');
        });
    });

    /**
     * Automatically close modal if we click the backdrop
     */
    previewModal.addEventListener('click', (event: PointerEvent): void => {
        if (event.target === previewModal) {
            previewModal.classList.remove('active');
        }
    });
};
