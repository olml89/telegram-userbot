import { Upload } from 'tus-js-client';
import { getReasonPhrase } from 'http-status-codes';

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

export const convertTusErrorToResponse = async (error) => {
    try {
        const originalResponse = await error?.originalResponse;

        if (originalResponse) {
            if (typeof Response !== 'undefined' && originalResponse instanceof Response) {
                return originalResponse;
            }

            const status = Number(originalResponse.getStatus());
            const statusText = getReasonPhrase(status);
            const body = await (originalResponse.getBody());

            return new Response(body, {
                status,
                statusText,
            });
        }
    } catch (e) {
        // Fallthrough to generic response
    }

    return new Response('Unknown tus error', {
        status: 0,
        statusText: '',
    });
};
