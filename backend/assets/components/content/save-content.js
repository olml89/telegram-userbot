export const initContentSave = (getFileIds) => {
    const saveBtn = document.querySelector('[data-content-add]');
    const titleInput = document.querySelector('[data-content-title]');
    const descriptionInput = document.querySelector('[data-content-description]');

    if (!saveBtn || !titleInput || !descriptionInput || !getFileIds) {
        return;
    }

    const getTags = () => {
        const selected = document.querySelectorAll('[data-tags-selected] .tag');

        return Array.from(selected).map((el) => el.dataset.tag).filter(Boolean);
    };

    saveBtn.addEventListener('click', async () => {
        const title = titleInput.value.trim();
        const description = descriptionInput.value.trim();
        const fileIds = getFileIds();
        const tags = getTags();

        if (!title) {
            alert('Title is required.');

            return;
        }

        if (!description) {
            alert('Description is required.');

            return;
        }

        if (fileIds.length === 0) {
            alert('Please upload at least one file.');

            return;
        }

        const response = await fetch('/api/content', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                title,
                description,
                tags,
                fileIds,
            }),
        });

        if (!response.ok) {
            alert('Failed to save content.');

            return;
        }

        alert('Content saved.');
    });
};
