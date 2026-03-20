import { Upload, type UploadOptions } from 'tus-js-client';
import { getReasonPhrase } from 'http-status-codes';

export class TusUploader {
    private readonly upload: Upload;

    private uploadStartedAt: number|null = null;

    public constructor(file: File, uploadOptions: UploadOptions) {
        this.upload = new Upload(file, uploadOptions);
    }

    public getId(): string {
        const uploadUrl = this.upload.url ?? null;

        if (!uploadUrl) {
            throw new Error('Upload URL is not available.');
        }

        const uploadId = new URL(uploadUrl).pathname.split('/').pop();

        if (!uploadId) {
            throw new Error('Upload ID is not available.');
        }

        return uploadId;
    }

    public getUploadStartedAt(): number {
        if (this.uploadStartedAt === null) {
            throw new Error('Upload has not been started yet.');
        }

        return this.uploadStartedAt;
    }

    public async remove(): Promise<void> {
        await this.upload.abort(true);
        const uploadUrl = this.upload.url ?? null;

        if (uploadUrl === null) {
            return;
        }

        await fetch(uploadUrl, {
            method: 'DELETE',
            headers: {
                'Tus-Resumable': '1.0.0',
            },
        });
    }

    public start(): void {
        this.uploadStartedAt = Date.now();
        this.upload.start();
    }
}

export const convertTusErrorToResponse = async (error: any): Promise<Response> => {
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
