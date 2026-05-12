import { Content, FindContentParams } from '../content/content';
import { ContentFieldValue } from '../content/add/add-modal';
import { Tag, TagPayload } from '../content/tag';
import { File as BackendFile } from '../content/file';
import { Paginated, Pagination } from '../models/pagination';

const humanizeError = (field: string, messages: string[]) => {
    const humanizeField = (field: string) => field
        .replace(/([a-z0-9])([A-Z])/g, '$1 $2')
        .replace(/[_-]+/g, ' ')
        .replace(/\s+/g, ' ')
        .trim()
        .toLowerCase()
        .replace(/^\w/, (c: string) => c.toUpperCase());

    const lowerFirst = (text: string) => text
        ? text.replace(/^\w/, (c: string) => c.toLowerCase())
        : text;

    return `${humanizeField(field)}: ${messages.map((message: string) => lowerFirst(message)).join(', ')}`;
};

export class BackendError extends Error {
    public consoleMessage: string;
    public validationErrors: Map<string, string[]>;

    public constructor(message: string, consoleMessage: string, validationErrors: Map<string, string[]>) {
        super(message);

        this.name = 'BackendError';
        this.consoleMessage = consoleMessage;
        this.validationErrors = validationErrors;
    }

    public formatErrors(): string[] {
        if (!this.isValidationError()) {
            return [this.message];
        }

        return Array.from(this.validationErrors).map(([field, messages]) => humanizeError(field, messages));
    }

    public static async from(response: Response, errorMessage: string): Promise<BackendError> {
        let uiMessage = errorMessage;
        let debugMessage;
        let validationErrors: Map<string, string[]> = new Map<string, string[]>();

        try {
            const data = await response.json();
            debugMessage = data.message;

            /**
             * Validation error: add the errors into the error body, concatenate them
             * to replace the generic validation error message.
             */
            if (response.status === 422 && data.errors) {
                validationErrors = new Map<string, string[]>(Object.entries(data.errors as Record<string, string[]>));

                const humanizedErrors = Array
                    .from(validationErrors)
                    .map(([field, messages]) => humanizeError(field, messages));

                debugMessage += `\n${humanizedErrors.join("\n")}`;
            }
        } catch {
            /**
             * JSON parsing error: not a JSON response.
             * Check if the response is a 504 directly returned by nginx in HTML.
             */
            debugMessage = response.statusText;

            if (response.status === 504) {
                uiMessage += '. Please retry';
            }
        }

        const consoleMessage = `${errorMessage} (${response.status}): ${debugMessage}`;

        return new BackendError(uiMessage, consoleMessage, validationErrors);
    }

    public isValidationError(): boolean {
        return this.validationErrors.size > 0;
    }
}

export class BackendApi {
    private async fetch(params: {
        method: string,
        endpoint: string,
        headers?: Record<string, string>,
        body?: unknown,
    }): Promise<Response> {
        const options: RequestInit = {
            method: params.method,
            headers: {
                ...params.headers,
            },
        }

        if (params.body && params.method !== 'GET') {
            options.body = JSON.stringify(params.body);
            options.headers = {
                ...options.headers,
                'Content-Type': 'application/json',
            };
        }

        return fetch(`/api/${params.endpoint}`, options);
    }

    public async addContent(contentData: Record<string, ContentFieldValue>): Promise<Content> {
        const response = await this.fetch({
            method: 'POST',
            endpoint: 'content',
            body: contentData,
        });

        if (!response.ok) {
            throw await BackendError.from(
                response,
                'Failed to add content',
            );
        }

        return Content.from(await response.json());
    }

    public async createTag(name: string): Promise<Tag> {
        const response = await this.fetch({
            method: 'POST',
            endpoint: 'tags',
            body: { name },
        });

        if (!response.ok) {
            throw await BackendError.from(
                response,
                'Failed to create tag',
            );
        }

        return Tag.from(await response.json());
    }

    public async deleteContent(content: Content): Promise<void> {
        const response = await this.fetch({
            method: 'DELETE',
            endpoint: `content/${content.publicId}`,
        });

        if (!response.ok) {
            throw await BackendError.from(
                response,
                'Failed to delete content',
            );
        }
    }

    public async deleteContentFile(content: Content, file: BackendFile): Promise<void> {
        const response = await this.fetch({
            method: 'DELETE',
            endpoint: `content/${content.publicId}/files/${file.publicId}`,
        });

        if (!response.ok) {
            throw await BackendError.from(
                response,
                'Failed to delete content file',
            );
        }
    }

    public async deleteFile(file: BackendFile): Promise<void> {
        const response = await this.fetch({
            method: 'DELETE',
            endpoint: `files/${file.publicId}`,
        });

        if (!response.ok) {
            throw await BackendError.from(
                response,
                'Failed to delete file',
            );
        }
    }

    public async findContent(params: FindContentParams, pagination: Pagination): Promise<Paginated<Content>> {
        const response = await this.fetch({
            method: 'GET',
            endpoint: `content${params.build(pagination)}`,
        });

        if (!response.ok) {
            throw await BackendError.from(
                response,
                'Failed to fetch content list',
            );
        }

        const payload = await response.json();

        return Paginated.from<Content>(payload, Content);
    }

    public async saveFile(uploadId: string): Promise<BackendFile> {
        const response = await this.fetch({
            method: 'POST',
            endpoint: `files`,
            body: { uploadId },
        });

        if (!response.ok) {
            throw await BackendError.from(
                response,
                'Failed to save file',
            );
        }

        return BackendFile.from(await response.json());
    }

    public async searchTags(query: string): Promise<Tag[]> {
        const response = await this.fetch({
            method: 'GET',
            endpoint: `tags?query=${encodeURIComponent(query)}`,
        });

        if (!response.ok) {
            throw await BackendError.from(
                response,
                'Failed to fetch tags',
            );
        }

        const payload = await response.json();

        return payload.map((tagPayload: TagPayload): Tag => Tag.from(tagPayload));
    }

    public async validateFile(file: File): Promise<void> {
        const response = await this.fetch({
            method: 'POST',
            endpoint: `files/validation`,
            body: {
                originalName: file.name,
                mimeType: file.type || null,
                size: file.size,
            },
        });

        if (!response.ok) {
            throw await BackendError.from(
                response,
                'Failed to validate file',
            );
        }
    }
}
