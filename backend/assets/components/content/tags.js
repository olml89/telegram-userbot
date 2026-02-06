export const initTags = () => {
    const tagsInput = document.querySelector('[data-tags-input]');
    const selectedContainer = document.querySelector('[data-tags-selected]');
    const dropdown = document.querySelector('[data-tags-dropdown]');

    if (!tagsInput || !selectedContainer || !dropdown) {
        return;
    }

    let activeIndex = -1;
    let preventRefocus = false;
    let isBusy = false;

    const getOptions = () => Array.from(dropdown.querySelectorAll('.tag-option:not(.tag-loading)'));

    const setActiveOption = (index) => {
        const options = getOptions();

        if (!options.length) {
            activeIndex = -1;

            return;
        }

        const safeIndex = Math.max(
            0,
            Math.min(index, options.length - 1),
        );

        options.forEach((option) => option.classList.remove('is-active'));
        options[safeIndex].classList.add('is-active');
        activeIndex = safeIndex;
    };

    const focusInputEnd = () => {
        tagsInput.classList.add('is-focused');
        tagsInput.focus();
        const length = tagsInput.value.length;
        tagsInput.setSelectionRange(length, length);
    };

    const showDropdownMessage = (message, type = 'loading') => {
        const className = type === 'error' ? 'tag-option tag-error' : 'tag-option tag-loading';
        dropdown.innerHTML = `<div class="${className}">${message}</div>`;
        dropdown.hidden = false;
        dropdown.style.display = 'block';
        dropdown.setAttribute('aria-hidden', 'false');
    };

    const setBusy = (busy, message = '') => {
        isBusy = busy;
        tagsInput.disabled = busy;

        if (busy) {
            tagsInput.blur();
            tagsInput.classList.remove('is-focused');

            if (tagsInput.value.trim() !== '') {
                showDropdownMessage(message || 'Loading...', 'loading');
            }

            return;
        }

        focusInputEnd();
    };

    const setError = (message) => {
        if (!message) {
            return;
        }

        showDropdownMessage(message, 'error');
    };

    const normalize = (value) => value.trim().toLowerCase();
    const getTagId = (el) => (el.dataset.tagId || '').trim();

    const createSelectedTag = ({ id, name }) => {
        const pill = document.createElement('button');
        pill.type = 'button';
        pill.className = 'tag tag-selected';
        pill.dataset.tagId = id;
        pill.dataset.tagName = name;
        pill.textContent = `${name} ✕`;

        pill.addEventListener('click', () => {
            pill.remove();
        });

        return pill;
    };

    const addToSelected = ({ id, name }) => {
        const normalized = normalize(name);

        if (!normalized || !id) {
            return;
        }

        const existing = [...selectedContainer.querySelectorAll('[data-tag-id]')].find(
            (el) => getTagId(el) === id
        );

        if (existing) {
            return;
        }

        const pill = createSelectedTag({ id, name });
        selectedContainer.appendChild(pill);
    };

    const hideDropdown = (refocus = false) => {
        dropdown.hidden = true;
        dropdown.style.display = 'none';
        dropdown.setAttribute('aria-hidden', 'true');
        dropdown.innerHTML = '';
        activeIndex = -1;

        if (refocus && !preventRefocus) {
            focusInputEnd();
        }
    };

    const showDropdown = (tags) => {
        dropdown.innerHTML = '';

        if (!tags.length) {
            hideDropdown(true);

            return;
        }

        tags.forEach((tag) => {
            const option = document.createElement('button');
            option.type = 'button';
            option.className = 'tag-option';
            option.dataset.tagId = tag.id;
            option.dataset.tagName = tag.name;
            option.textContent = tag.name;

            option.addEventListener('click', () => {
                addToSelected({ id: tag.id, name: tag.name });
                tagsInput.value = '';
                hideDropdown();
            });

            dropdown.appendChild(option);
        });

        dropdown.hidden = false;
        dropdown.style.display = 'block';
        dropdown.setAttribute('aria-hidden', 'false');
        setActiveOption(0);
    };

    const fetchTags = async (query) => {
        const response = await fetch(`/api/tags?query=${encodeURIComponent(query)}`);

        if (!response || !response.ok) {
            const payload = await response.json().catch(() => ({}));
            const message = payload?.message || 'Failed to fetch tags.';

            throw new Error(message);
        }

        return response.json();
    };

    const createTag = async (name) => {
        const response = await fetch('/api/tags', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                name,
            }),
        });

        if (!response.ok) {
            const payload = await response.json().catch(() => ({}));
            const message = payload?.message || 'Failed to create tag.';

            throw new Error(message);
        }

        return response.json();
    };

    let searchTimeout = null;

    tagsInput.addEventListener('input', () => {
        if (isBusy) {
            return;
        }

        const value = tagsInput.value.trim();

        if (!value) {
            hideDropdown();
            setError('');

            return;
        }

        clearTimeout(searchTimeout);

        searchTimeout = setTimeout(async () => {
            try {
                setBusy(true, 'Fetching tags...');
                const tags = await fetchTags(value);

                showDropdown(tags.map((t) => ({
                    id: t.id,
                    name: t.name,
                })));
            } catch (e) {
                console.error(e);
                showDropdownMessage('Could not load tags. Please try again.', 'error');
            } finally {
                setBusy(false);
            }
        }, 250);
    });

    tagsInput.addEventListener('keydown', async (event) => {
        if (event.isComposing) {
            return;
        }

        const options = getOptions();
        const isDropdownOpen = !dropdown.hidden && options.length > 0;

        if (event.key === 'ArrowDown' && isDropdownOpen) {
            event.preventDefault();
            setActiveOption(activeIndex + 1);

            return;
        }

        if (event.key === 'ArrowUp' && isDropdownOpen) {
            event.preventDefault();
            setActiveOption(activeIndex - 1);

            return;
        }

        const isEnter = event.key === 'Enter' || event.keyCode === 13;

        if (!isEnter) {
            return;
        }

        event.preventDefault();

        if (isDropdownOpen && activeIndex >= 0) {
            const option = options[activeIndex];

            addToSelected({
                id: option.dataset.tagId,
                name: option.dataset.tagName,
            });

            tagsInput.value = '';
            hideDropdown();
            setError('');

            return;
        }

        const value = tagsInput.value.trim();

        if (!value) {
            return;
        }

        try {
            setBusy(true, 'Creating tag...');
            const created = await createTag(value);
            const id = created.id;
            const name = created.name;

            addToSelected({ id, name });
            tagsInput.value = '';
            hideDropdown();
            setError('');
        } catch (e) {
            console.error(e);
            showDropdownMessage('Could not create tag. Please try again.', 'error');
        } finally {
            setBusy(false);
        }
    });

    tagsInput.addEventListener('blur', () => {
        if (isBusy) {
            return;
        }

        preventRefocus = true;
        tagsInput.classList.remove('is-focused');

        setTimeout(() => {
            hideDropdown();
        }, 150);
    });

    tagsInput.addEventListener('focus', () => {
        if (isBusy) {
            tagsInput.blur();
            return;
        }

        preventRefocus = false;
        tagsInput.classList.add('is-focused');
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('[data-tags-dropdown]') && event.target !== tagsInput) {
            hideDropdown();
        }
    });
};
