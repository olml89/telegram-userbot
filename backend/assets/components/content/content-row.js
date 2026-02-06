export const renderContentRow = (content) => {
    const files = Array.isArray(content.files) ? content.files : [];
    const filesCount = files.length;
    const mediaClass = filesCount === 1 ? 'media-single' : filesCount === 2 ? 'media-pair' : 'media-grid';
    const visibleFiles = files.slice(0, 4);
    const moreCount = Math.max(0, filesCount - 4);

    const counts = files.reduce(
        (acc, file) => {
            const mimeType = (file?.mimeType || '').toLowerCase();
            if (mimeType.startsWith('image/')) {
                acc.images += 1;
            } else if (mimeType.startsWith('video/')) {
                acc.videos += 1;
            } else if (mimeType.startsWith('audio/')) {
                acc.audios += 1;
            } else if (mimeType) {
                acc.docs += 1;
            }
            return acc;
        },
        { images: 0, videos: 0, audios: 0, docs: 0 }
    );

    const formatPrice = (value) => {
        if (value === null || value === undefined || value === '') {
            return '-';
        }

        if (typeof value === 'string' && value.trim().startsWith('$')) {
            return value.trim();
        }

        const numeric = typeof value === 'number' ? value : Number(value);

        if (Number.isNaN(numeric)) {
            return String(value);
        }

        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(numeric);
    };

    const statusClass = content.status.value === 'active' ? 'pill-success' : 'pill-danger';
    const salesLabel = content.sales === 0 ? '-' : content.sales;

    return `
        <tr>
            <td>
                <div class="media-thumb ${mediaClass}">
                    ${visibleFiles
                        .map(
                            (file) => `<img src="${file.thumbnail}" alt="Media thumbnail" class="media-img">`
                        )
                        .join('')
                    }
                    ${moreCount > 0 ? `<span class="media-more">+${moreCount}</span>` : ''}
                </div>
            </td>
            <td>
                <div class="content-info">
                    <div class="content-title">${content.title}</div>
                    <div class="content-description">${content.description}</div>
                    <div class="tag-list">
                        ${content.tags.map((tag) => `<span class="tag">${tag.name}</span>`).join('')}
                    </div>
                </div>
            </td>
            <td><span class="pill">${content.category.name}</span></td>
            <td>
                Images ${counts.images} · Videos ${counts.videos} · Audio ${counts.audios} · Docs ${counts.docs}
            </td>
            <td>
                <div class="price-info">
                    <div class="price-value">${formatPrice(content.price)}</div>
                    <span class="pill pill-muted">${content.mode.label}</span>
                </div>
            </td>
            <td>${salesLabel}</td>
            <td><span class="pill ${statusClass}">${content.status.label}</span></td>
            <td>${content.createdAt}</td>
            <td>
                <div class="actions-menu">
                    <button class="menu-trigger" type="button" aria-label="More actions">⋯</button>
                    <div class="menu-dropdown">
                        <button type="button" class="menu-item" data-preview-open>
                            <span class="menu-icon">👁️</span>
                            Preview
                        </button>
                        <button type="button" class="menu-item">
                            <span class="menu-icon">📝</span>
                            Edit
                        </button>
                        <button type="button" class="menu-item">
                            <span class="menu-icon">🗐</span>
                            Duplicate
                        </button>
                        <button type="button" class="menu-item menu-danger">
                            <span class="menu-icon">🗑️</span>
                            Delete
                        </button>
                    </div>
                </div>
            </td>
        </tr>
    `;
};
