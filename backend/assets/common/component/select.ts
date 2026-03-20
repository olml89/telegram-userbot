import {BusyAware, ChangeAware, Component, Errorable, ErrorClearable, HtmlElementWrapper} from './contracts';
import { Enum } from '../models/enum';
import { RequirableComponent } from './requirable-component';
import { assertImported, querySelector, querySelectorAll } from '../importer';
import { capitalize } from '../strings';

let isOutsideClickBound = false;

class SelectOption implements Component<Enum>, HtmlElementWrapper {
    private readonly option: HTMLButtonElement;
    private readonly enum: Enum;
    private readonly selectedListeners: Set<() => void> = new Set<() => void>();

    public constructor(option: HTMLButtonElement) {
        this.option = option;

        this.enum = {
            name: option.textContent.trim(),
            value: option.value.trim(),
        }

        this.option.addEventListener('click', (): void => {
            this.selectedListeners.forEach((listener: () => void): void => listener());
        });
    }

    public getValue(): Enum {
        return this.enum;
    }

    public element(): HTMLElement {
        return this.option;
    }

    public onSelected(listener: () => void): void {
        this.selectedListeners.add(listener);
    }
}

export abstract class Select<TValue = Enum|null> implements BusyAware, ChangeAware, Component<TValue>, Errorable, ErrorClearable {
    protected readonly select: HTMLDivElement;
    protected readonly trigger: HTMLButtonElement;
    protected readonly valueLabel: HTMLSpanElement;
    protected readonly selectOptions: SelectOption[] = [];
    protected readonly changeListeners: Set<() => void> = new Set<() => void>();

    protected enum: Enum|null = null;

    protected constructor(
        select: HTMLDivElement,
        trigger: HTMLButtonElement,
        valueLabel: HTMLSpanElement,
        selectOptions: SelectOption[],
    ) {
        this.select = select;
        this.trigger = trigger;
        this.valueLabel = valueLabel;
        this.selectOptions = selectOptions;

        /**
         * Dropdown behaviour
         */
        this.trigger.addEventListener('click', (): void => {
            const isOpen = this.select.classList.contains('open');

            /**
             * Close all other open .select elements
             */
            const openedCustomSelectElements = document.querySelectorAll<HTMLDivElement>('.select.open');
            openedCustomSelectElements.forEach((openedCustomSelectElement: HTMLDivElement): void => openedCustomSelectElement.classList.remove('open'));

            if (!this.selectOptions.length) {
                return;
            }

            if (!isOpen) {
                this.select.classList.add('open');
            }
        });

        /**
         * Selection behaviour
         */
        this.selectOptions.forEach((selectOption: SelectOption): void => {
            selectOption.onSelected((): void => {
                this.enum = selectOption.getValue();
                this.valueLabel.textContent = selectOption.getValue().name;
                this.valueLabel.classList.remove('unselected');
                this.select.classList.remove('open');
                this.notifyChange();
            });
        });

        /**
         * Outside click handler
         */
        if (!isOutsideClickBound) {
            isOutsideClickBound = true;

            document.addEventListener('click', (event: PointerEvent): void => {
                if (!(event.target as HTMLElement).closest('.select')) {
                    const openedCustomSelectElements = document.querySelectorAll<HTMLDivElement>('.select.open');

                    openedCustomSelectElements.forEach((openedCustomSelectElement: HTMLDivElement): void => {
                        openedCustomSelectElement.classList.remove('open');
                    });
                }
            });
        }
    }

    protected static createFrom<T extends Select>(
        this: new (
            select: HTMLDivElement,
            trigger: HTMLButtonElement,
            valueLabel: HTMLSpanElement,
            selectOptions: SelectOption[]
        ) => T,
        itemName: string,
        selectContainer: HTMLLabelElement|null,
    ): T | null {
        const select = querySelector<HTMLDivElement>(selectContainer, '[data-select]');
        const trigger = querySelector<HTMLButtonElement>(selectContainer, '[data-select-trigger]');
        const valueLabel = querySelector<HTMLSpanElement>(selectContainer, '[data-select-value]');
        const selectOptions = Array.from(querySelectorAll<HTMLButtonElement>(selectContainer, '[data-select-option]'));

        const required = {
            selectContainer,
            select,
            trigger,
            valueLabel,
            selectOptions,
        }

        if (!assertImported(`${itemName}-select`, required)) {
            return null;
        }

        return new this(
            required.select,
            required.trigger,
            required.valueLabel,
            required.selectOptions.map((el: HTMLButtonElement): SelectOption => new SelectOption(el))
        );
    }

    public clearErrors(): void {
        this.trigger.classList.remove('is-error');
    }

    public destroy(): void {
        this.select.classList.remove('open');
        this.changeListeners.clear();
    }

    public getValue(): TValue {
        return this.enum as TValue;
    }

    public onChange(listener: () => void) {
        this.changeListeners.add(listener);
    }

    private notifyChange(): void {
        this.changeListeners.forEach((listener: () => void): void => listener());
    }

    public required(): boolean {
        return this.select.hasAttribute('data-required');
    }

    public setBusy(isBusy: boolean) {
        this.trigger.disabled = isBusy;
        this.trigger.setAttribute('aria-disabled', String(isBusy));
    }

    public setErrors(): void {
        this.trigger.classList.add('is-error');
    }
}

export abstract class ValidatableSelect<TValue = Enum|null> extends RequirableComponent<TValue> implements BusyAware {
    protected readonly itemName: string;
    private readonly select: Select<TValue>;

    protected constructor(
        itemName: string,
        label: HTMLSpanElement,
        select: Select<TValue>,
    ) {
        super(label, select.required());

        this.itemName = itemName;
        this.select = select;

        /**
         * Forward change events from Select to RequirableComponent listeners
         */
        this.select.onChange((): void => {
            this.changeListeners.forEach((listener: () => void): void => listener());
        })
    }

    protected static createFrom<T extends ValidatableSelect<TValue>, TValue = Enum|null>(
        this: new (
            itemName: string,
            label: HTMLSpanElement,
            select: Select<TValue>,
        ) => T,
        itemName: string,
        selectContainer: HTMLLabelElement|null,
        select: Select<TValue>|null,
    ): T | null {
        const label = querySelector<HTMLSpanElement>(selectContainer, '[data-error-for]');

        const required = {
            selectContainer,
            label,
            select,
        };

        if (!assertImported(`${itemName}-validatable-select`, required)) {
            return null;
        }

        return new this(
            itemName,
            required.label,
            required.select,
        );
    }

    public override clearErrors(): void {
        super.clearErrors();

        this.select.clearErrors();
    }

    public override destroy(): void {
        super.destroy();

        this.select.destroy();
    }

    public override getValue(): TValue {
        return this.select.getValue();
    }

    protected override isEmpty(): boolean {
        return this.getValue() === null;
    }

    protected override name(): string {
        return this.itemName;
    }

    protected override requiredMessage(): string {
        return `${capitalize(this.name())} is required`;
    }

    public setBusy(isBusy: boolean) {
        this.label.classList.toggle('is-disabled', isBusy);
        this.select.setBusy(isBusy);
    }

    public override setErrors(...errorMessages: string[]): void {
        super.setErrors(...errorMessages);

        this.select.setErrors();
    }
}
