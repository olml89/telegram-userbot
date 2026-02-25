import { createProgressBar } from './progress.js';
import { formatSize, getEmoji, humanizeError } from '../../formatter.js';

export const createFileItem = (file) => {
    const mime = file.type || 'application/octet-stream';
    const emoji = getEmoji(mime);
    const objectUrl = URL.createObjectURL(file);

    const item = document.createElement('div');
    item.className = 'file-item';
    item.dataset.fileSize = String(file.size);

    const thumb = document.createElement('div');
    thumb.className = 'file-thumb';

    if (mime.startsWith('image/')) {
        const img = document.createElement('img');
        img.src = objectUrl;
        img.alt = file.name;
        thumb.appendChild(img);
    } else {
        const placeholder = document.createElement('div');
        placeholder.className = 'file-placeholder';
        placeholder.textContent = emoji;
        thumb.appendChild(placeholder);
    }

    const metaLeft = document.createElement('div');
    metaLeft.className = 'file-meta';
    metaLeft.innerHTML = `
        <div class="file-title" title="${file.name}">${file.name}</div>
        <div class="file-row">
            <span class="file-label">Type:</span>
            <span class="file-value file-muted">${emoji} ${mime}</span>
        </div>
    `;

    /**
     * Resolution
     */
    if (mime.startsWith('image/') || mime.startsWith('audio/')) {
        const resolutionRow = document.createElement('div');
        resolutionRow.className = 'file-row';
        resolutionRow.innerHTML = `
            <span class="file-label">Resolution:</span>
            <span class="file-value file-muted" data-resolution>Loading…</span>
        `;
        metaLeft.appendChild(resolutionRow);
    }

    const metaRight = document.createElement('div');
    metaRight.className = 'file-meta';
    metaRight.innerHTML = `
        <div class="file-row">
            <span class="file-label">Size:</span>
            <span class="file-value" data-file-size-value>${formatSize(file.size)}</span>
        </div>
    `;

    /**
     * File length
     */
    if (mime.startsWith('video/') || mime.startsWith('audio/')) {
        const lengthRow = document.createElement('div');
        lengthRow.className = 'file-row';
        lengthRow.innerHTML = `
            <span class="file-label">Length:</span>
            <span class="file-value" data-length>Loading…</span>
        `;
        metaRight.appendChild(lengthRow);
    }

    const actions = document.createElement('div');
    actions.className = 'file-actions';
    actions.innerHTML = `
        <button type="button" class="btn btn-secondary btn-hover-outline btn-sm" data-cancel-file hidden>✖ Cancel</button>
        <button type="button" class="btn btn-secondary btn-hover-outline btn-sm" data-retry-file hidden>🔄 Retry</button>
                <button type="button" class="btn btn-secondary btn-hover-outline btn-sm" data-remove-file hidden>🗑️ Remove</button>    
    `;

    const progress = createProgressBar();

    item.appendChild(thumb);
    item.appendChild(metaLeft);
    item.appendChild(metaRight);
    item.appendChild(actions);
    item.appendChild(progress.wrap);
    item.appendChild(progress.status);

    const removeBtn = actions.querySelector('[data-remove-file]');
    const retryBtn = actions.querySelector('[data-retry-file]');
    const cancelBtn = actions.querySelector('[data-cancel-file]');

    const setProgressBarVisible = (isVisible) => {
        item.classList.toggle('is-progress-bar-visible', isVisible);
    };

    const setStatusVisible = (isVisible) => {
        item.classList.toggle('is-status-visible', isVisible);
    }

    const setUploadingState = (isUploading) => {
        if (cancelBtn) {
            cancelBtn.hidden = !isUploading;
        }

        if (removeBtn) {
            removeBtn.hidden = isUploading;
        }

        setProgressBarVisible(isUploading);

        const hasError = item.classList.contains('file-item-error') || item.classList.contains('file-item-warning');
        setStatusVisible(isUploading || hasError);
    };

    const setStatusLines = (lines) => {
        progress.status.innerHTML = '';

        lines.forEach((message) => {
            const line = document.createElement('span');
            line.textContent = message;
            progress.status.appendChild(line);
        });
    };

    const setProgressMessage = (message) => {
        progress.status.classList.remove('is-error', 'is-warning');
        item.classList.remove('file-item-error', 'file-item-warning');

        setStatusLines([message]);
        setStatusVisible(Boolean(message));
    };

    const formatErrors = (error) => {
        return error?.errors
            ? Object
                .entries(error.errors)
                .map(([field, message]) => humanizeError(field, message))
            : [error.message];
    }

    const setError = (error) => {
        item.classList.add('file-item-error');
        progress.status.classList.add('is-error');

        setStatusLines(formatErrors(error));
        setStatusVisible(true);
    };

    const setWarning = (error) => {
        item.classList.add('file-item-warning');
        progress.status.classList.add('is-warning');

        setStatusLines(formatErrors(error));
        setStatusVisible(true);
    };

    const clearError = () => {
        item.classList.remove('file-item-error', 'file-item-warning');
        progress.status.classList.remove('is-error', 'is-warning');

        setStatusLines([]);
        setStatusVisible(false);
        setProgressBarVisible(false);
    };

    const detachProgress = () => {
        setStatusVisible(false);
        setProgressBarVisible(false);
    };

    const setSize = (bytes) => {
        item.dataset.fileSize = String(bytes);
        const fileSizeValueEl = item.querySelector('[data-file-size-value]');
        if (fileSizeValueEl) {
            fileSizeValueEl.textContent = formatSize(bytes);
        }
    };

    return {
        element: item,
        progress,
        setUploadingState,
        setProgressMessage,
        setProgressBarVisible,
        setError,
        setWarning,
        clearError,
        detachProgress,
        setSize,
        cancelBtn,
        retryBtn,
        removeBtn,
        mime,
        objectUrl,
    };
};
