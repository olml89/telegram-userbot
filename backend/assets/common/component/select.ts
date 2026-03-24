import { BusyAware, ChangeAware, Component, Errorable, ErrorClearable, HtmlElementWrapper } from './contracts';
import { Enum } from '../models/enum';
import { RequirableComponent } from './requirable-component';
import { assertImported, querySelector, querySelectorAll } from '../importer';
import { capitalize } from '../strings';

let isOutsideClickBound = false;
const selectInstances = new Set<Select>();

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
    protected readonly defaultValue: HTMLSpanElement;
    protected readonly selectedValue: HTMLSpanElement;
    protected readonly selectOptions: SelectOption[] = [];
    protected readonly changeListeners: Set<() => void> = new Set<() => void>();

    protected enum: Enum|null = null;

    protected constructor(
        select: HTMLDivElement,
        trigger: HTMLButtonElement,
        defaultValue: HTMLSpanElement,
        selectedValue: HTMLSpanElement,
        selectOptions: SelectOption[],
    ) {
        this.select = select;
        this.trigger = trigger;
        this.defaultValue = defaultValue;
        this.selectedValue = selectedValue;
        this.selectOptions = selectOptions;
        selectInstances.add(this as Select);

        /**
         * Dropdown behaviour
         */
        this.trigger.addEventListener('click', (): void => {
            const isOpened = this.isOpened();

            const otherOpenedSelects = Array
                .from(selectInstances)
                .filter((select: Select): boolean => select !== this && select.isOpened());

            otherOpenedSelects.forEach((otherOpenedSelect: Select): void => otherOpenedSelect.close());

            if (!this.length()) {
                return;
            }

            if (!isOpened) {
                this.open();
            }
        });

        /**
         * Selection behaviour
         */
        this.selectOptions.forEach((selectOption: SelectOption): void => {
            selectOption.onSelected((): void => {
                this.selectOption(selectOption);
                this.close();
            });
        });

        /**
         * Outside click handler
         */
        if (!isOutsideClickBound) {
            isOutsideClickBound = true;

            document.addEventListener('click', (event: PointerEvent): void => {
                /**
                 * If we are not clicking on a Select component
                 */
                if (!(event.target as HTMLElement).closest('.select')) {
                    const openedSelects = Array
                        .from(selectInstances)
                        .filter((select: Select): boolean => select.isOpened());

                    openedSelects.forEach((openedSelect: Select): void => {
                        if (!openedSelect.required()) {
                            openedSelect.reset();
                        }

                        openedSelect.close();
                    });
                }
            });
        }
    }

    protected static createFrom<T extends Select>(
        this: new (
            select: HTMLDivElement,
            trigger: HTMLButtonElement,
            defaultValue: HTMLSpanElement,
            selectedValue: HTMLSpanElement,
            selectOptions: SelectOption[]
        ) => T,
        itemName: string,
        selectContainer: HTMLLabelElement|null,
    ): T | null {
        const select = querySelector<HTMLDivElement>(selectContainer, '[data-select]');
        const trigger = querySelector<HTMLButtonElement>(selectContainer, '[data-select-trigger]');
        const defaultValue = querySelector<HTMLSpanElement>(selectContainer, '[data-default-value]');
        const selectedValue = querySelector<HTMLSpanElement>(selectContainer, '[data-select-value]');
        const selectOptions = Array.from(querySelectorAll<HTMLButtonElement>(selectContainer, '[data-select-option]'));

        const required = {
            selectContainer,
            select,
            trigger,
            defaultValue,
            selectedValue,
            selectOptions,
        }

        if (!assertImported(`${itemName}-select`, required)) {
            return null;
        }

        return new this(
            required.select,
            required.trigger,
            required.defaultValue,
            required.selectedValue,
            required.selectOptions.map((el: HTMLButtonElement): SelectOption => new SelectOption(el))
        );
    }

    public clearErrors(): void {
        this.trigger.classList.remove('is-error');
    }

    private close(): void {
        this.select.classList.remove('is-opened');
    }

    public destroy(): void {
        this.reset();
        this.close();
        this.changeListeners.clear();
    }

    public getValue(): TValue {
        return this.enum as TValue;
    }

    private isOpened(): boolean {
        return this.select.classList.contains('is-opened');
    }

    private length(): number {
        return this.selectOptions.length;
    }

    public onChange(listener: () => void) {
        this.changeListeners.add(listener);
    }

    private open(): void {
        this.select.classList.add('is-opened');
    }

    private notifyChange(): void {
        this.changeListeners.forEach((listener: () => void): void => listener());
    }

    private reset(): void {
        this.enum = null;
        this.selectedValue.textContent = '';
        this.selectedValue.classList.add('is-hidden');
        this.defaultValue.classList.remove('is-hidden');
        this.notifyChange();
    }

    public required(): boolean {
        return this.select.hasAttribute('data-required');
    }

    private selectOption(selectOption: SelectOption): void {
        this.enum = selectOption.getValue();
        this.selectedValue.textContent = selectOption.getValue().name;
        this.selectedValue.classList.remove('is-hidden');
        this.defaultValue.classList.add('is-hidden');
        this.notifyChange();
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
