import { createFileItem } from './file-item.js';
import { formatSize, formatProgress } from './format.js';
import { media } from './media.js';
import { createTusUploader } from './tus-uploader.js';

export const initFileUpload = () => {
    const fileInput = document.querySelector('[data-file-input]');
    const uploadCard = document.querySelector('[data-upload-card]');
    const uploadCount = document.querySelector('[data-upload-count]');
    const totalSizeEl = document.querySelector('[data-total-size]');
    const fileList = document.querySelector('[data-file-list]');
    const addContentBtn = document.querySelector('[data-content-add]');
    const uploadedFileIds = new Set();

    if (!fileInput || !uploadCard || !fileList || !uploadCount || !totalSizeEl) {
        return;
    }

    let activeUploads = 0;
    let pendingDeletes = 0;
    const cancelHandlers = new Set();

    const setUploadsActive = () => {
        const active = activeUploads > 0 || pendingDeletes > 0;

        if (addContentBtn) {
            addContentBtn.disabled = active;
        }

        window.dispatchEvent(new CustomEvent('uploads:active', { detail: { active } }));
    };

    const updateTotals = () => {
        const uploadedCount = uploadedFileIds.size;
        uploadCount.textContent = `${uploadedCount} ${uploadedCount === 1 ? 'file' : 'files'} uploaded`;

        const totalBytes = Array.from(fileList.querySelectorAll('[data-file-size][data-file-id]'))
            .map((node) => Number(node.dataset.fileSize || 0))
            .reduce((a, b) => a + b, 0);

        totalSizeEl.textContent = `Total size: ${formatSize(totalBytes)}`;
        window.dispatchEvent(new CustomEvent('uploads:changed', { detail: { count: uploadedCount } }));
    };

    const setLabelContrast = (progress, pct) => {
        const wrapRect = progress.wrap.getBoundingClientRect();
        const labelRect = progress.label.getBoundingClientRect();
        const filledPx = (pct / 100) * wrapRect.width;
        const labelCenter = (labelRect.left - wrapRect.left) + (labelRect.width / 2);

        if (filledPx >= labelCenter) {
            progress.label.classList.add('is-on-bar');
        } else {
            progress.label.classList.remove('is-on-bar');
        }
    };

    const parseApiError = async (response, prefix) => {
        let message = '';
        let uiMessage = '';

        const humanizeField = (field) => field
            .replace(/([a-z0-9])([A-Z])/g, '$1 $2')
            .replace(/[_-]+/g, ' ')
            .replace(/\s+/g, ' ')
            .trim()
            .toLowerCase()
            .replace(/^\w/, (c) => c.toUpperCase());

        const lowerFirst = (text) => text
            ? text.replace(/^\w/, (c) => c.toLowerCase())
            : text;

        try {
            const data = await response.json();
            message = data?.message;

            if (response.status === 422 && data?.errors) {
                const errors = Object.entries(data.errors)
                    .map(([field, msg]) => `${humanizeField(field)}: ${lowerFirst(String(msg))}`)
                    .join(' · ');
                if (errors) {
                    message = errors;
                }
            }

            uiMessage = message.replace(/\s*\(\d{3}\)\s*$/, '');

            if (response.status === 404) {
                uiMessage = 'Upload not found';
            }
        } catch (e) {
            //Ignore JSON parsing errors
        }

        return {
            uiMessage,
            consoleMessage: `${prefix} (${response.status}): ${message}`,
        };
    };

    const fileValidate = async (file) => {
        const response = await fetch('/api/files/validation', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                originalName: file.name,
                mimeType: file.type || null,
                size: file.size,
            }),
        });

        if (!response.ok) {
            const { uiMessage, consoleMessage } = await parseApiError(
                response,
                'Failed to validate file',
            );

            const err = new Error(uiMessage);
            err.uiMessage = uiMessage;
            err.consoleMessage = consoleMessage;
            throw err;
        }
    }

    const fileUpload = async ({ uploadId }) => {
        const response = await fetch('/api/files', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                uploadId,
            }),
        });

        if (!response.ok) {
            const { uiMessage, consoleMessage } = await parseApiError(
                response,
                'Failed to save file',
            );

            const err = new Error(uiMessage);
            err.uiMessage = uiMessage;
            err.consoleMessage = consoleMessage;
            throw err;
        }

        return response.json();
    };

    const fileDelete = async ({ fileId }) => {
        const response = await fetch(`/api/files/${fileId}`, {
            method: 'DELETE',
        });

        if (!response.ok) {
            const { uiMessage, consoleMessage } = await parseApiError(
                response,
                'Failed to delete file',
            );

            const err = new Error(uiMessage);
            err.uiMessage = uiMessage;
            err.consoleMessage = consoleMessage;
            throw err;
        }
    };

    const handleFile = (file) => {
        const ui = createFileItem(file);
        const {
            element,
            progress,
            setUploadingState,
            setProgressMessage,
            setProgressBarVisible,
            setError,
            setWarning,
            clearError,
            detachProgress,
            cancelBtn,
            retryBtn,
            removeBtn,
            mime,
            objectUrl,
        } = ui;

        fileList.appendChild(element);

        let uploader = null;
        let uploadUrl = null;
        let isCanceled = false;
        let isFinalized = false;

        const finalizeUpload = () => {
            if (isFinalized) {
                return;
            }

            isFinalized = true;
            activeUploads = Math.max(0, activeUploads - 1);
            setUploadsActive();
        };

        const cancelUploadAndRemove = () => {
            isCanceled = true;

            if (uploader) {
                uploader.abort(true);
            }

            if (uploadUrl && !element.dataset.fileId) {
                void fetch(uploadUrl, {
                    method: 'DELETE',
                    headers: {
                        'Tus-Resumable': '1.0.0',
                    },
                }).catch((e) => console.error(e));
            }

            finalizeUpload();
            removeItem();
        };

        const handleDeleteError = (message) => {
            setWarning(message);
            finalizeUpload();

            if (cancelBtn) {
                cancelBtn.disabled = false;
            }

            if (retryBtn) {
                retryBtn.disabled = false;
            }

            if (removeBtn) {
                removeBtn.disabled = false;
            }
        };

        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => cancelUploadAndRemove());
        }

        cancelHandlers.add(cancelUploadAndRemove);

        const removeItem = () => {
            const id = element.dataset.fileId;

            if (id) {
                uploadedFileIds.delete(id);
            }

            cancelHandlers.delete(cancelUploadAndRemove);
            element.remove();
            updateTotals();
        };

        media({ mime, objectUrl, element });
        
        const startUpload = async () => {
            if (retryBtn) {
                retryBtn.hidden = true;
            }

            isCanceled = false;
            isFinalized = false;
            uploadUrl = null;
            setUploadingState(true);

            setProgressMessage('Validating file...');

            try {
                await fileValidate(file);
            } catch (e) {
                const uiMessage = e?.uiMessage || 'Failed to validate file. Please retry.';
                const consoleMessage = e?.consoleMessage || uiMessage;

                console.error(consoleMessage);
                setError(uiMessage);
                setUploadingState(false);

                if (retryBtn) {
                    retryBtn.hidden = false;
                }

                return;
            }

            setProgressMessage(`${formatProgress()}`);
            ++activeUploads;
            setUploadsActive();
            const uploadStartedAt = Date.now();

            /**
             * Exponential Moving Average (EMA) to smooth out speed peaks.
             */
            let emaSpeed = null;
            const emaAlpha = 0.2;

            uploader = createTusUploader({
                file,
                endpoint: `${window.location.origin}/tus/uploads`,
                chunkSize: 5 * 1024 * 1024,
                retryDelays: [],
                metadata: {
                    filename: file.name,
                    filetype: file.type || 'application/octet-stream',
                },
                onAfterResponse: () => {
                    uploadUrl = uploader?.url || uploadUrl;
                },
                onProgress: (bytesSent, bytesTotal) => {
                    if (isCanceled) {
                        return;
                    }

                    const pct = (bytesSent / bytesTotal) * 100;
                    const text = `${pct.toFixed(1)}%`;
                    progress.bar.style.width = text;
                    progress.label.textContent = text;

                    setLabelContrast(progress, pct);

                    const elapsedSeconds = (Date.now() - uploadStartedAt) / 1000;
                    const instantSpeed = bytesSent / Math.max(elapsedSeconds, 0.001);

                    emaSpeed = emaSpeed === null
                        ? instantSpeed
                        : emaAlpha * instantSpeed + (1 - emaAlpha) * emaSpeed;

                    const remainingSeconds = (bytesTotal !== bytesSent)
                        ? (bytesTotal - bytesSent) / Math.max(emaSpeed, 0.001)
                        : 0;

                    progress.status.textContent = `${formatProgress(bytesSent, bytesTotal, emaSpeed, remainingSeconds)}`;
                },
                onError: (error) => {
                    if (isCanceled) {
                        return;
                    }

                    console.error(error);

                    detachProgress();
                    setError('Failed to upload file. Please retry.');
                    setUploadingState(false);
                    finalizeUpload();

                    if (retryBtn) {
                        retryBtn.hidden = false;
                    }
                },
                onSuccess: async () => {
                    if (isCanceled) {
                        return;
                    }

                    detachProgress();
                    setProgressBarVisible(false);
                    setProgressMessage('Saving file...');

                    if (cancelBtn) {
                        cancelBtn.hidden = true;
                    }

                    try {
                        const result = await fileUpload({ uploadId: uploader.getId() });
                        element.dataset.fileId = result.publicId;
                        uploadedFileIds.add(result.publicId);
                        clearError();
                        setProgressMessage('');
                        setUploadingState(false);
                        finalizeUpload();
                        updateTotals();
                    } catch (e) {
                        const uiMessage = e?.uiMessage || 'Failed to save file. Please retry.';
                        const consoleMessage = e?.consoleMessage || uiMessage;

                        console.error(consoleMessage);
                        setError(uiMessage);
                        setUploadingState(false);
                        finalizeUpload();

                        if (retryBtn) {
                            retryBtn.hidden = false;
                        }
                    }
                },
            });

            uploader.start();
        };

        if (retryBtn) {
            retryBtn.addEventListener('click', () => {
                clearError();
                void startUpload();
            });
        }

        if (removeBtn) {
            removeBtn.addEventListener('click', async () => {
                const fileId = element.dataset.fileId;
                clearError();
                setProgressBarVisible(false);
                setProgressMessage('Deleting...');

                if (cancelBtn) {
                    cancelBtn.disabled = true;
                }

                if (removeBtn) {
                    removeBtn.disabled = true;
                    removeBtn.hidden = true;
                }

                if (retryBtn) {
                    retryBtn.disabled = true;
                }

                /**
                 * If the API file doesn't exist, just remove the file element in the UI and DO NOT touch tusd
                 */
                if (!fileId) {
                    finalizeUpload();
                    removeItem();

                    return;
                }

                ++pendingDeletes;
                setUploadsActive();

                try {
                    await fileDelete({fileId});
                    removeItem();
                } catch (e) {
                    const uiMessage = e?.uiMessage || 'Failed to save file. Please retry.';
                    const consoleMessage = e?.consoleMessage || uiMessage;

                    console.error(consoleMessage);
                    handleDeleteError(uiMessage);
                } finally {
                    pendingDeletes = Math.max(0, pendingDeletes - 1);
                    setUploadsActive();

                    if (removeBtn && element.isConnected) {
                        removeBtn.disabled = false;
                        removeBtn.hidden = false;
                    }
                }
            });
        }

        void startUpload();
    };

    window.addEventListener('uploads:cancelAll', () => {
        cancelHandlers.forEach((cancel) => cancel());
    });

    uploadCard.addEventListener('click', () => fileInput.click());

    uploadCard.addEventListener('dragover', (event) => {
        event.preventDefault();
        uploadCard.classList.add('is-dragover');
    });

    uploadCard.addEventListener('dragleave', () => {
        uploadCard.classList.remove('is-dragover');
    });

    uploadCard.addEventListener('drop', (event) => {
        event.preventDefault();
        uploadCard.classList.remove('is-dragover');
        const files = Array.from(event.dataTransfer?.files ?? []);
        files.forEach(handleFile);
    });

    fileInput.addEventListener('change', () => {
        const files = Array.from(fileInput.files ?? []);
        files.forEach(handleFile);
        fileInput.value = '';
    });

    return {
        getFileIds: () => Array.from(uploadedFileIds),
    };
};
