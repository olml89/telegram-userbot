import { Component, Errorable } from './contracts';
import { ErrorHandler } from './error-handler';

export abstract class BaseComponent<TValue = unknown> implements Component<TValue>, Errorable {
    protected readonly errorHandler: ErrorHandler<TValue> = new ErrorHandler<TValue>();
    public destroy(): void {
        /**
         * Override this method to add custom logic when the component is destroyed.
         */
    }

    public abstract getValue(): TValue;

    public setErrors(...errorMessages: string[]) {
        this.errorHandler.setErrors(...errorMessages);
    }
}
