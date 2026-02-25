import { renderContentRow } from './content-row.js';
import { parseApiError } from '../http/api-error-parser.js';

export const initContentAdd = (getFileIds) => {
    const tableBody = document.querySelector('[data-library-table-body]');
    const addModal = document.getElementById('contentAddModal');
    const titleInput = document.querySelector('[data-content-title]');
    const descriptionInput = document.querySelector('[data-content-description]');
    const priceInput = document.querySelector('[data-price-input]');
    const intensityInput = document.querySelector('[data-intensity-range]');
    const categorySelect = document.querySelector('[data-content-category]');
    const languageSelect = document.querySelector('[data-content-language]');
    const statusSelect = document.querySelector('[data-content-status]');
    const modeSelect = document.querySelector('[data-mode-select]');
    const tagsSelected = document.querySelector('[data-tags-selected]');
    const saveBtn = document.querySelector('[data-content-add]');

    if (
        !tableBody || !addModal ||
        !titleInput || !descriptionInput || !priceInput || !intensityInput ||
        !categorySelect || !languageSelect || !statusSelect || !modeSelect ||
        !tagsSelected || !getFileIds ||
        !saveBtn
    ) {
        return;
    }

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
        form: document.querySelector('[data-error-for="form"]'),
    };

    const setModalBusy = (isBusy) => {
        if (!addModal) {
            return;
        }

        saveBtn.disabled = isBusy;

        addModal.querySelectorAll('input, textarea').forEach((el) => {
            el.disabled = isBusy;
        });

        addModal.querySelectorAll('button, select').forEach((el) => {
            if (el === saveBtn) {
                return;
            }

            el.disabled = isBusy;
        });

        addModal.querySelectorAll('[data-add-close]').forEach((el) => {
            el.disabled = isBusy;
        });

        addModal.querySelectorAll('.custom-select .select-trigger').forEach((el) => {
            el.disabled = isBusy;
            el.setAttribute('aria-disabled', String(isBusy));
        });

        addModal.querySelectorAll('.file-actions button').forEach((el) => {
            el.disabled = isBusy;
        });

        addModal.querySelectorAll('[data-file-id] [data-remove-file]').forEach((el) => {
            el.disabled = isBusy;
        });

        addModal.querySelectorAll('[data-tags-input]').forEach((el) => {
            el.disabled = isBusy;
        });

        addModal.querySelectorAll('[data-upload-card]').forEach((el) => {
            el.classList.toggle('is-disabled', isBusy);
        });

        addModal.querySelectorAll('[data-file-list] .file-item').forEach((el) => {
            el.classList.toggle('is-disabled', isBusy);
        });

        addModal.classList.toggle('is-busy', isBusy);
    };

    const setSubmitLoading = (isLoading) => {
        saveBtn.classList.toggle('is-loading', isLoading);
        saveBtn.disabled = isLoading;

        const label = saveBtn.querySelector('.btn-label');
        label.textContent = isLoading ? saveBtn.dataset.labelLoading : saveBtn.dataset.labelDefault;
    };

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

    const clearTagItemErrors = () => {
        tagsSelected.querySelectorAll('[data-tag-id].is-error').forEach((el) => {
            el.classList.remove('is-error');
            el.removeAttribute('title');
        });
    };

    const clearFileItemErrors = () => {
        document.querySelectorAll('[data-file-id].file-item-error').forEach((item) => {
            item.classList.remove('file-item-error');
            const status = item.querySelector('.file-progress-status');

            if (status) {
                status.classList.remove('is-error');
                status.textContent = '';
            }
            item.classList.remove('is-status-visible');
        });
    };

    const setTagItemError = (tagId, message) => {
        const tag = tagsSelected.querySelector(`[data-tag-id="${tagId}"]`);

        if (!tag) {
            return;
        }

        tag.classList.add('is-error');

        if (message) {
            tag.setAttribute('title', message);
        }
    };

    const setFileItemError = (fileId, message) => {
        const file = document.querySelector(`[data-file-id="${fileId}"]`);

        if (!file) {
            return;
        }

        file.classList.add('file-item-error');
        file.classList.add('is-status-visible');

        const status = file.querySelector('.file-progress-status');

        if (status) {
            status.classList.add('is-error');
            status.textContent = message || 'File error';
        }
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
        clearTagItemErrors();
        clearFileItemErrors();

        const tagItemErrors = [];
        const fileItemErrors = [];
        const fieldErrorsList = [];
        console.log(errors);
        Object.entries(errors).forEach(([key, value]) => {
            const normalized = key.replace(/\[\d+]/g, '');
            const list = Array.isArray(value) ? value : [value];

            list.forEach((message) => {
                const text = String(message || '');

                if (normalized === 'tagIds') {
                    const match = text.match(/^Tag\s+([0-9a-f-]{36})\b/i);

                    if (match) {
                        const cleaned = text
                            .replace(match[1], '')
                            .replace(/\s+/g, ' ')
                            .trim();
                        tagItemErrors.push({ id: match[1], message: cleaned });

                        return;
                    }
                }

                if (normalized === 'fileIds') {
                    const match = text.match(/^File\s+([0-9a-f-]{36})\b/i);

                    if (match) {
                        const cleaned = text
                            .replace(match[1], '')
                            .replace(/\s+/g, ' ')
                            .trim();
                        fileItemErrors.push({ id: match[1], message: cleaned });

                        return;
                    }
                }

                fieldErrorsList.push({ key: normalized, message: text });
            });
        });

        fieldErrorsList.forEach(({ key, message }) => setFieldError(key, message));
        tagItemErrors.forEach(({ id, message }) => setTagItemError(id, message));
        fileItemErrors.forEach(({ id, message }) => setFileItemError(id, message));
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

    const addContent = async(content) => {
        const response = await fetch('/api/content', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(content),
        });

        if (!response.ok) {
            throw await parseApiError(
                response,
                'Failed to add content',
            );
        }

        return response.json();
    }

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

        setModalBusy(true);
        setSubmitLoading(true);

        try {
            const content = await addContent({
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
            });

            const row = document.createElement('tbody');
            row.innerHTML = renderContentRow(content).trim();
            tableBody.prepend(row.firstElementChild);

            setErrors([]);
            setSubmitLoading(false);
            setModalBusy(false);
        } catch (e) {
            console.error(e.consoleMessage);
            setErrors(e?.errors || { form: e.message });
            setSubmitLoading(false);
            setSaveDisabled();
            setModalBusy(false);
        }
    });
};
