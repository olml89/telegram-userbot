export const media = ({ mime, objectUrl, element }) => {
    const revoke = () => URL.revokeObjectURL(objectUrl);

    /**
     * Image
     */
    if (mime.startsWith('image/')) {
        const img = new Image();
        img.onload = () => {
            const resEl = element.querySelector('[data-resolution]');

            if (resEl) {
                resEl.textContent = `${img.width}×${img.height}`;
            }

            revoke();
        };

        img.src = objectUrl;
        return;
    }

    /**
     * Video
     */
    if (mime.startsWith('video/')) {
        const video = document.createElement('video');
        video.preload = 'metadata';
        video.muted = true;
        video.playsInline = true;

        video.onloadedmetadata = () => {
            const lengthEl = element.querySelector('[data-length]');
            const resEl = element.querySelector('[data-video-resolution]');

            if (lengthEl) {
                lengthEl.textContent = `${Math.round(video.duration)}s`;
            }

            if (resEl && video.videoWidth && video.videoHeight) {
                resEl.textContent = `${video.videoWidth}×${video.videoHeight}`;
            }

            video.currentTime = Math.min(0.1, video.duration || 0.1);
        };

        video.onseeked = () => {
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth || 320;
            canvas.height = video.videoHeight || 180;

            const ctx = canvas.getContext('2d');

            if (ctx) {
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                const img = document.createElement('img');
                img.src = canvas.toDataURL('image/jpeg');
                img.alt = 'Video thumbnail';

                const thumb = element.querySelector('.file-thumb');

                if (thumb) {
                    thumb.innerHTML = '';
                    thumb.appendChild(img);
                }
            }

            revoke();
        };

        video.src = objectUrl;

        return;
    }

    /**
     * Audio
     */
    if (mime.startsWith('audio/')) {
        const audio = document.createElement('audio');
        audio.preload = 'metadata';

        audio.onloadedmetadata = () => {
            const lengthEl = element.querySelector('[data-audio-length]');

            if (lengthEl) {
                lengthEl.textContent = `${Math.round(audio.duration)}s`;
            }

            revoke();
        };

        audio.src = objectUrl;

        return;
    }

    revoke();
};
