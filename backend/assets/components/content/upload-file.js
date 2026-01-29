export const initFileUpload = () => {
    const fileInput = document.querySelector('[data-file-input]');
    const uploadBtn = document.querySelector('[data-upload-btn]');
    const fileList = document.querySelector('[data-file-list]');

    if (!fileInput || !uploadBtn || !fileList) {
        return;
    }

    /**
     * Emoji calculation
     *
     * @param mime
     * @returns {string}
     */
    const getEmoji = (mime) => {
        if (mime.startsWith('video/')) return '📹';
        if (mime.startsWith('image/')) return '🖼️';
        if (mime.startsWith('audio/')) return '🎵';
        return '📄';
    };

    /**
     * File size formatting
     *
     * @param bytes
     * @returns {string}
     */
    const formatSize = (bytes) => {
        const kb = bytes / 1024;
        const mb = kb / 1024;
        const gb = mb / 1024;

        if (mb < 1) {
            return `${Math.round(kb)} KB`;
        }
        if (mb < 1000) {
            return `${mb.toFixed(2)} MB`;
        }
        return `${gb.toFixed(2)} GB`;
    };

    uploadBtn.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', () => {
        const file = fileInput.files[0];
        if (!file) return;

        const mime = file.type || 'application/octet-stream';
        const emoji = getEmoji(mime);
        const objectUrl = URL.createObjectURL(file);

        const item = document.createElement('div');
        item.className = 'file-item';

        /**
         * Thumbnail
         *
         * @type {HTMLDivElement}
         */
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

        /**
         * File meta: name and mimeType
         *
         * @type {HTMLDivElement}
         */
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
         *
         * @type {HTMLDivElement}
         */
        const resolutionRow = document.createElement('div');
        resolutionRow.className = 'file-row';
        if (mime.startsWith('image/')) {
            resolutionRow.innerHTML = `
                <span class="file-label">Resolution:</span>
                <span class="file-value file-muted" data-resolution>Loading…</span>
            `;
            metaLeft.appendChild(resolutionRow);
        }

        /**
         * Filesize
         *
         * @type {HTMLDivElement}
         */
        const metaRight = document.createElement('div');
        metaRight.className = 'file-meta';
        metaRight.innerHTML = `
            <div class="file-row">
                <span class="file-label">Size:</span>
                <span class="file-value">${formatSize(file.size)}</span>
            </div>
        `;

        /**
         * File length
         */
        if (mime.startsWith('video/')) {
            const lengthRow = document.createElement('div');
            lengthRow.className = 'file-row';
            lengthRow.innerHTML = `
                <span class="file-label">Length:</span>
                <span class="file-value" data-length>Loading…</span>
            `;
            metaRight.appendChild(lengthRow);
        }

        if (mime.startsWith('audio/')) {
            const lengthRow = document.createElement('div');
            lengthRow.className = 'file-row';
            lengthRow.innerHTML = `
                <span class="file-label">Length:</span>
                <span class="file-value" data-audio-length>Loading…</span>
            `;
            metaRight.appendChild(lengthRow);
        }

        /**
         * Remove button
         *
         * @type {HTMLDivElement}
         */
        const actions = document.createElement('div');
        actions.className = 'file-actions';
        actions.innerHTML = `
            <button type="button" class="btn btn-secondary btn-hover-muted btn-sm" data-remove-file>🗑️ Remove</button>
        `;

        item.appendChild(thumb);
        item.appendChild(metaLeft);
        item.appendChild(metaRight);
        item.appendChild(actions);

        fileList.appendChild(item);
        fileInput.value = '';

        const removeBtn = item.querySelector('[data-remove-file]');
        if (removeBtn) {
            removeBtn.addEventListener('click', () => {
                item.remove();
            });
        }

        /**
         * Image resolution calculation
         */
        if (mime.startsWith('image/')) {
            const imgProbe = new Image();
            imgProbe.onload = () => {
                const resEl = item.querySelector('[data-resolution]');
                if (resEl) {
                    resEl.textContent = `${imgProbe.width}×${imgProbe.height}`;
                }
                URL.revokeObjectURL(objectUrl);
            };
            imgProbe.src = objectUrl;
        }

        /**
         * Video thumbnail and length calculation
         */
        if (mime.startsWith('video/')) {
            const videoProbe = document.createElement('video');
            videoProbe.preload = 'metadata';
            videoProbe.muted = true;
            videoProbe.playsInline = true;

            videoProbe.onloadedmetadata = () => {
                const lengthEl = item.querySelector('[data-length]');
                if (lengthEl) {
                    lengthEl.textContent = `${Math.round(videoProbe.duration)}s`;
                }

                // capture a thumbnail frame
                videoProbe.currentTime = Math.min(0.1, videoProbe.duration || 0.1);
            };

            videoProbe.onseeked = () => {
                const canvas = document.createElement('canvas');
                canvas.width = videoProbe.videoWidth || 320;
                canvas.height = videoProbe.videoHeight || 180;

                const ctx = canvas.getContext('2d');
                if (ctx) {
                    ctx.drawImage(videoProbe, 0, 0, canvas.width, canvas.height);
                    const img = document.createElement('img');
                    img.src = canvas.toDataURL('image/jpeg');
                    img.alt = file.name;

                    // Replace placeholder with thumbnail image
                    thumb.innerHTML = '';
                    thumb.appendChild(img);
                }

                URL.revokeObjectURL(objectUrl);
            };

            videoProbe.src = objectUrl;
        }

        /**
         * Audio length calculation
         */
        if (mime.startsWith('audio/')) {
            const audioProbe = document.createElement('audio');
            audioProbe.preload = 'metadata';
            audioProbe.onloadedmetadata = () => {
                const lengthEl = item.querySelector('[data-audio-length]');
                if (lengthEl) {
                    lengthEl.textContent = `${Math.round(audioProbe.duration)}s`;
                }
                URL.revokeObjectURL(objectUrl);
            };
            audioProbe.src = objectUrl;
        }

        if (!mime.startsWith('image/') && !mime.startsWith('video/') && !mime.startsWith('audio/')) {
            URL.revokeObjectURL(objectUrl);
        }
    });
};
