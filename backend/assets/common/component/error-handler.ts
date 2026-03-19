export class ErrorHandler<TValue = unknown> {
    private backendErrorValue: TValue|null = null;
    private backendErrorMessages: string[] = [];
    private errorMessages: string[] = [];

    public backendErrorMessagesFor(value: TValue): string[] {
        if (this.backendErrorMessages.length === 0 || this.backendErrorValue !== value) {
            return [];
        }

        return this.backendErrorMessages;
    }

    public clearErrors(): void {
        this.errorMessages = [];
    }

    public hasErrors(): boolean {
        return this.errorMessages.length > 0;
    }

    public setErrors(...errorMessages: string[]) {
        this.errorMessages = errorMessages;
    }

    public setBackendErrors(value: TValue, ...errorMessages: string[]): void {
        this.setErrors(...errorMessages);

        this.backendErrorMessages = errorMessages;
        this.backendErrorValue = value;
    }
}
