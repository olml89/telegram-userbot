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
