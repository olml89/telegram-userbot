export const initTags = () => {
    const tagsInput = document.querySelector('[data-tags-input]');
    const selectedContainer = document.querySelector('[data-tags-selected]');
    const availableContainer = document.querySelector('[data-tags-available]');

    if (!tagsInput || !selectedContainer || !availableContainer) {
        return;
    }

    const normalize = (value) => value.trim().toLowerCase();

    const getTagLabel = (el) => (el.dataset.tag || el.textContent || '').trim();

    const createSelectedTag = (label) => {
        const pill = document.createElement('button');
        pill.type = 'button';
        pill.className = 'tag tag-selected';
        pill.dataset.tag = label;
        pill.textContent = `${label} ✕`;

        pill.addEventListener('click', () => {
            pill.remove();
        });

        return pill;
    };

    const addToSelected = (label) => {
        const normalized = normalize(label);
        if (!normalized) return;

        const existing = [...selectedContainer.querySelectorAll('[data-tag]')].find(
            (el) => normalize(getTagLabel(el)) === normalized
        );
        if (existing) return;

        const pill = createSelectedTag(label);
        selectedContainer.appendChild(pill);
    };

    const ensureAvailable = (label) => {
        const normalized = normalize(label);
        const exists = [...availableContainer.querySelectorAll('[data-tag]')].find(
            (el) => normalize(getTagLabel(el)) === normalized
        );
        if (exists) return;

        const pill = document.createElement('button');
        pill.type = 'button';
        pill.className = 'tag tag-available';
        pill.dataset.tag = label;
        pill.textContent = label;
        availableContainer.appendChild(pill);
    };

    const filterAvailable = (query) => {
        const q = normalize(query);
        availableContainer.querySelectorAll('[data-tag]').forEach((pill) => {
            const label = getTagLabel(pill);
            const match = q === '' || normalize(label).startsWith(q);
            pill.style.display = match ? 'inline-flex' : 'none';
        });
    };

    availableContainer.addEventListener('click', (event) => {
        const pill = event.target.closest('[data-tag]');
        if (!pill) return;
        addToSelected(getTagLabel(pill));
    });

    tagsInput.addEventListener('input', () => {
        filterAvailable(tagsInput.value);
    });

    tagsInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            const value = tagsInput.value.trim();
            if (!value) return;

            addToSelected(value);
            ensureAvailable(value);

            tagsInput.value = '';
            filterAvailable('');
        }
    });

    filterAvailable('');
};
