import {BusyAware, ChangeAware} from '../../common/component/contracts';
import { NumberInput } from '../../common/component/number-input';
import { ValidatableModeSelect } from './mode-select';
import { assertImported, querySelector } from '../../common/importer';

class HoldableButton implements BusyAware, ChangeAware {
    static readonly DELAY = 400;
    static readonly INTERVAL = 80;

    private readonly button: HTMLButtonElement;
    private readonly eventTarget: EventTarget = new EventTarget();

    private animationFrameId: number|null = null;
    private holdStartTime: number|null = null;
    private lastTriggerTime: number|null = null;

    public constructor(button: HTMLButtonElement) {
        this.button = button;

        this.button.addEventListener('pointerdown', (): void => this.onPointerDown());
        this.button.addEventListener('pointerup', (): void => this.stop());
        this.button.addEventListener('pointerleave', (): void => this.stop());
        this.button.addEventListener('pointercancel', (): void => this.stop());
    }

    public onChange(listener: () => void): void {
        this.eventTarget.addEventListener('price-input:change', listener);
    }

    private onPointerDown(): void {
        if (this.button.disabled) {
            return;
        }

        /**
         * First click
         */
        this.eventTarget.dispatchEvent(new Event('price-input:change'));

        /**
         * Subsequent simulated clicks while being hold
         */
        this.holdStartTime = null;
        this.lastTriggerTime = null;
        this.animationFrameId = requestAnimationFrame((t): void => this.loop(t));
    }

    private loop(timestamp: number): void {
        if (this.holdStartTime === null) {
            this.holdStartTime = timestamp;
        }

        const elapsed = timestamp - this.holdStartTime;

        if (elapsed > HoldableButton.DELAY) {
            if (this.lastTriggerTime === null || timestamp - this.lastTriggerTime > HoldableButton.INTERVAL) {
                this.eventTarget.dispatchEvent(new Event('price-input:change'));
                this.lastTriggerTime = timestamp;
            }
        }

        this.animationFrameId = requestAnimationFrame((t): void => this.loop(t));
    }

    public setBusy(isBusy: boolean): void {
        this.button.disabled = isBusy;
    }

    public setDisabled(isDisabled: boolean): void {
        this.button.disabled = isDisabled;
    }

    private stop(): void {
        if (this.animationFrameId !== null) {
            cancelAnimationFrame(this.animationFrameId);
            this.animationFrameId = null;
        }

        this.holdStartTime = null;
        this.lastTriggerTime = null;
    }
}

export class PriceInput extends NumberInput {
    private readonly mode: ValidatableModeSelect;
    private readonly stepUpButton: HoldableButton;
    private readonly stepDownButton: HoldableButton;

    private lastSellingValue: number|null = null;

    public constructor(
        mode: ValidatableModeSelect,
        label: HTMLSpanElement,
        input: HTMLInputElement,
        stepUpButton: HoldableButton,
        stepDownButton: HoldableButton,
    ) {
        super(label, input);

        this.mode = mode;
        this.stepUpButton = stepUpButton;
        this.stepDownButton = stepDownButton;

        this.stepUpButton.onChange((): void => this.setValue(1));
        this.stepDownButton.onChange((): void => this.setValue(-1));

        this.mode.onChange((): void => {
            const isFree = this.mode.getValue()?.isTeasing() ?? false;
            this.input.disabled = isFree;
            this.syncPrice(isFree);
        });

        this.input.addEventListener('input', (): void => this.syncButtons());
        this.syncButtons();
    }

    public static from(mode: ValidatableModeSelect|null, priceContainer: HTMLLabelElement|null): PriceInput|null {
        const label = querySelector<HTMLSpanElement>(priceContainer, '[data-error-for]');
        const input = querySelector<HTMLInputElement>(priceContainer, '[data-price-input]');
        const stepUpButton = querySelector<HTMLButtonElement>(priceContainer, '[data-price-step-up]');
        const stepDownButton = querySelector<HTMLButtonElement>(priceContainer, '[data-price-step-down]')

        const required = {
            mode,
            priceContainer,
            label,
            input,
            stepUpButton,
            stepDownButton,
        };

        if (!assertImported('price', required)) {
            return null;
        }

        return new PriceInput(
            required.mode,
            required.label,
            required.input,
            new HoldableButton(required.stepUpButton),
            new HoldableButton(required.stepDownButton),
        );
    }

    private normalizedMin(): number {
        return this.min() ?? 0;
    }

    private normalizedStep(): number {
        return this.step() ?? 1;
    }

    private normalizedValue(): number {
        return this.getValue() ?? 0;
    }

    public override setBusy(isBusy: boolean): void {
        super.setBusy(isBusy);

        this.stepDownButton.setBusy(isBusy);
        this.stepUpButton.setBusy(isBusy);
    }

    private syncPrice(isFree: boolean): void {
        const value = isFree ? 0 : this.lastSellingValue;

        super.setValue(value);
        this.input.dispatchEvent(new Event('input'));
    }

    protected override setValue(direction: 1|-1): void {
        if (this.disabled()) {
            return;
        }

        const nextValue = Math.max(
            this.normalizedMin(),
            this.normalizedValue() + (direction * this.normalizedStep()),
        );

        super.setValue(nextValue);
        this.input.dispatchEvent(new Event('input'));
    }

    private syncButtons(): void {
        this.stepUpButton.setDisabled(this.disabled());
        this.stepDownButton.setDisabled(this.disabled() || this.normalizedValue() <= this.normalizedMin());

        if (!this.disabled()) {
            this.lastSellingValue = this.normalizedValue();
        }
    }
}
