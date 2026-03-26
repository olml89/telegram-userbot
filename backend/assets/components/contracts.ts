export interface BusyAware {
    setBusy(isBusy: boolean): void;
}

export interface ChangeAware {
    onChange(listener: () => void): void;
}

export interface Component<TValue = unknown> {
    getValue(): TValue;
}

export interface Errorable {
    setErrors(...errorMessages: string[]): void;
}

export interface BackendErrorable extends Errorable {
    setBackendErrors(...errorMessages: string[]): void;
}

export interface ErrorAware {
    hasErrors(): boolean;
}

export interface ErrorClearable {
    clearErrors(): void;
}

export interface HtmlElementWrapper {
    element(): HTMLElement;
}

export interface Validatable {
    validate(): boolean;
}





