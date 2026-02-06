export const getEmoji = (mime) => {
    if (mime.startsWith('video/')) return '📹';
    if (mime.startsWith('image/')) return '🖼️';
    if (mime.startsWith('audio/')) return '🎵';
    return '📄';
};

export const formatSize = (bytes) => {
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

const formatEta = (seconds) => {
    if (!Number.isFinite(seconds)) {
        return '—';
    }

    if (seconds <= 0) {
        return '0s';
    }

    if (seconds < 60) {
        return `${Math.round(seconds)}s`;
    }

    const mins = Math.floor(seconds / 60);
    const secs = Math.round(seconds % 60);

    return `${mins}m ${secs}s`;
};

const formatSpeed = (bytesPerSecond) => {
    if (!Number.isFinite(bytesPerSecond) || bytesPerSecond <= 0) {
        bytesPerSecond = 0;
    }

    return `${formatSize(bytesPerSecond)}/s`;
};

export const formatProgress = (bytesSent = 0, bytesTotal = 0, speed = 0, remainingSeconds = null) => {
    return `Uploading: ${formatSize(bytesSent)} / ${formatSize(bytesTotal)} · ${formatSpeed(speed)} · ETA ${formatEta(remainingSeconds)}`;
};
