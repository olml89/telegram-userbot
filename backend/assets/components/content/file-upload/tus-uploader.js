import { Upload } from 'tus-js-client';

export const createTusUploader = ({
    file,
    endpoint,
    chunkSize,
    metadata,
    retryDelays = [],
    onProgress,
    onSuccess,
    onError,
    onAfterResponse,
}) => {
    const upload = new Upload(file, {
        endpoint,
        chunkSize,
        retryDelays,
        metadata,
        onProgress,
        onSuccess,
        onError,
        onAfterResponse,
    });

    return {
        start: () => upload.start(),
        abort: (shouldTerminate) => upload.abort(shouldTerminate),
        getId: () => new URL(upload.url).pathname.split('/').pop(),
    };
};
