import { renderContentRow } from './content-row.js';

export const initContentSave = (getFileIds) => {
    const saveBtn = document.querySelector('[data-content-add]');
    const titleInput = document.querySelector('[data-content-title]');
    const descriptionInput = document.querySelector('[data-content-description]');
    const priceInput = document.querySelector('[data-price-input]');
    const intensityInput = document.querySelector('[data-intensity-range]');
    const categorySelect = document.querySelector('[data-content-category]');
    const languageSelect = document.querySelector('[data-content-language]');
    const statusSelect = document.querySelector('[data-content-status]');
    const modeSelect = document.querySelector('[data-mode-select]');
    const tagsSelected = document.querySelector('[data-tags-selected]');
    const tableBody = document.querySelector('[data-library-table-body]');

    const fieldErrors = {
        title: document.querySelector('[data-error-for="title"]'),
        description: document.querySelector('[data-error-for="description"]'),
        categoryId: document.querySelector('[data-error-for="categoryId"]'),
        language: document.querySelector('[data-error-for="language"]'),
        status: document.querySelector('[data-error-for="status"]'),
        mode: document.querySelector('[data-error-for="mode"]'),
        price: document.querySelector('[data-error-for="price"]'),
        intensity: document.querySelector('[data-error-for="intensity"]'),
        tagIds: document.querySelector('[data-error-for="tagIds"]'),
        fileIds: document.querySelector('[data-error-for="fileIds"]'),
    };

    if (
        !saveBtn || !titleInput || !descriptionInput || !priceInput || !intensityInput ||
        !categorySelect || !languageSelect || !statusSelect || !modeSelect ||
        !tagsSelected || !getFileIds
    ) {
        return;
    }

    let uploadsActive = false;

    const getTagIds = () => {
        const selected = tagsSelected.querySelectorAll('.tag');

        return Array.from(selected)
            .map((el) => (el.dataset.tagId || '').trim())
            .filter(Boolean);
    };

    const getSelectedValue = (selectEl) => (selectEl?.dataset.value || '').trim();
    const getSelectedId = (selectEl) => (selectEl?.dataset.id || '').trim();

    const validate = () => {
        const title = titleInput.value.trim();
        const description = descriptionInput.value.trim();
        const price = Number(priceInput.value);
        const intensity = Number(intensityInput.value);
        const language = getSelectedValue(languageSelect);
        const status = getSelectedValue(statusSelect);
        const mode = getSelectedValue(modeSelect);
        const categoryId = getSelectedId(categorySelect);
        const tagIds = getTagIds();
        const fileIds = getFileIds();

        const errors = [];

        if (!title) {
            errors.title = 'Title is required.';
        }
        if (!description) {
            errors.description = 'Description is required.';
        }
        if (Number.isNaN(price)) {
            errors.price = 'Please provide a valid price.';
        }
        if (Number.isNaN(intensity)) {
            errors.intensity = 'Please provide a valid intensity.';
        }
        if (!language) {
            errors.language = 'Please select a language.';
        }
        if (!status) {
            errors.status = 'Please select a status.';
        }
        if (!mode) {
            errors.mode = 'Please select a mode.';
        }
        if (!categoryId) {
            errors.categoryId = 'Please select a category.';
        }
        if (tagIds.length === 0) {
            errors.tagIds = 'Please select at least one tag.';
        }
        if (fileIds.length === 0) {
            errors.fileIds = 'Please upload at least one file.';
        }

        return {
            errors,
            title,
            description,
            price,
            intensity,
            language,
            status,
            mode,
            categoryId,
            tagIds,
            fileIds,
        };
    };

    const clearFieldErrors = () => {
        Object.values(fieldErrors).forEach((el) => {
            if (!el) {
                return;
            }

            el.textContent = '';
            el.hidden = true;
        });
    };

    const setFieldError = (key, message) => {
        const el = fieldErrors[key];

        if (!el) {
            return false;
        }

        el.textContent = message;
        el.hidden = false;

        return true;
    };

    const setErrors = (errors) => {
        clearFieldErrors();

        Object.entries(errors || {}).forEach(([key, value]) => {
            const normalized = key.replace(/\[\d+]/g, '');
            const list = Array.isArray(value) ? value : [value];
            list.forEach((message) => setFieldError(normalized, message));
        });
    };

    const setSaveDisabled = () => {
        const { errors } = validate();
        saveBtn.disabled = uploadsActive || Object.keys(errors).length > 0;
    };

    const observeTags = new MutationObserver(() => setSaveDisabled());
    observeTags.observe(tagsSelected, { childList: true });

    titleInput.addEventListener('input', setSaveDisabled);
    descriptionInput.addEventListener('input', setSaveDisabled);
    priceInput.addEventListener('input', setSaveDisabled);
    intensityInput.addEventListener('input', setSaveDisabled);

    document.addEventListener('click', (event) => {
        if (event.target.closest('.custom-select')) {
            setTimeout(setSaveDisabled, 0);
        }
    });

    window.addEventListener('uploads:changed', setSaveDisabled);

    window.addEventListener('uploads:active', (event) => {
        uploadsActive = Boolean(event.detail?.active);
        setSaveDisabled();
    });

    setSaveDisabled();

    saveBtn.addEventListener('click', async () => {
        const {
            errors,
            title,
            description,
            price,
            intensity,
            language,
            status,
            mode,
            categoryId,
            tagIds,
            fileIds,
        } = validate();

        setErrors(errors);

        if (Object.keys(errors).length > 0) {
            return;
        }

        saveBtn.disabled = true;

        const response = await fetch('/api/content', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                title,
                description,
                language,
                price,
                intensity,
                status,
                mode,
                categoryId,
                tagIds,
                fileIds,
            }),
        });

        if (!response.ok) {
            let payload = null;

            try {
                payload = await response.json();
            } catch (e) {
                payload = null;
            }

            const serverErrors = payload?.errors || { form: payload?.message || 'Failed to save content.' };
            setErrors(serverErrors);
            setSaveDisabled();

            return;
        }

        const payload = await response.json().catch(() => ({}));

        if (tableBody) {
            const row = document.createElement('tbody');
            row.innerHTML = renderContentRow(payload).trim();
            tableBody.prepend(row.firstElementChild);
        }

        setErrors([]);
        saveBtn.disabled = false;
    });
};
