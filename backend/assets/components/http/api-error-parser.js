import { humanizeError } from '../formatter.js';

export const parseApiError = async (response, errorMessage) => {
    let uiMessage = errorMessage;
    let debugMessage;
    let errors = null;

    try {
        const data = await response.json();
        debugMessage = data.message;

        /**
         * Validation error: add the errors into the error body, concatenate them
         * to replace the generic validation error message.
         */
        if (response.status === 422 && data.errors) {
            errors = data.errors;

            const humanizedErrors = Object
                .entries(errors)
                .map(([field, msg]) => humanizeError(field, msg));

            debugMessage += `\n${humanizedErrors.join("\n")}`;
        }
    } catch (e) {
        /**
         * JSON parsing error: not a JSON response.
         * Check if the response is a 504 directly returned by nginx in HTML.
         */
        debugMessage = response.statusText;

        if (response.status === 504) {
            uiMessage += '. Please retry';
        }
    }

    const error = new Error(uiMessage);
    error.consoleMessage = `${errorMessage} (${response.status}): ${debugMessage}`;
    error.errors = errors;

    return error;
};
