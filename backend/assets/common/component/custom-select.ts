import { BusyAware, Component, HtmlElementWrapper } from './contracts';
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

export abstract class CustomSelect<TValue = Enum|null> extends RequirableComponent<TValue> implements BusyAware {
    protected readonly itemName: string;
    protected readonly select: HTMLDivElement;
    protected readonly trigger: HTMLButtonElement;
    protected readonly valueLabel: HTMLSpanElement;
    protected readonly selectOptions: SelectOption[] = [];

    protected enum: Enum|null = null;

    protected constructor(
        itemName: string,
        label: HTMLSpanElement,
        select: HTMLDivElement,
        trigger: HTMLButtonElement,
        valueLabel: HTMLSpanElement,
        selectOptions: SelectOption[],
    ) {
        super(label, select.hasAttribute('data-required'));

        this.itemName = itemName;
        this.select = select;
        this.trigger = trigger;
        this.valueLabel = valueLabel;
        this.selectOptions = selectOptions;

        this.trigger.addEventListener('click', (): void => {
            const isOpen = this.select.classList.contains('open');

            /**
             * Close all other open .custom-select elements
             */
            const openedCustomSelectElements = document.querySelectorAll<HTMLDivElement>('.custom-select.open');
            openedCustomSelectElements.forEach((openedCustomSelectElement: HTMLDivElement): void => openedCustomSelectElement.classList.remove('open'));

            if (!this.selectOptions.length) {
                return;
            }

            if (!isOpen) {
                this.select.classList.add('open');
            }
        });

        this.selectOptions.forEach((selectOption: SelectOption): void => {
            selectOption.onSelected((): void => {
                this.enum = selectOption.getValue();
                this.valueLabel.textContent = selectOption.getValue().name;
                this.valueLabel.classList.remove('unselected');
                this.select.classList.remove('open');
                this.changeListeners.forEach((listener: () => void): void => listener());
            });
        });

        if (!isOutsideClickBound) {
            isOutsideClickBound = true;

            document.addEventListener('click', (event: PointerEvent): void => {
                if (!(event.target as HTMLElement).closest('.custom-select')) {
                    const openedCustomSelectElements = document.querySelectorAll<HTMLDivElement>('.custom-select.open');
                    openedCustomSelectElements.forEach((openedCustomSelectElement: HTMLDivElement): void => openedCustomSelectElement.classList.remove('open'));
                }
            });
        }
    }

    protected static createFrom<T extends CustomSelect>(
        this: new (
            itemName: string,
            label: HTMLSpanElement,
            select: HTMLDivElement,
            trigger: HTMLButtonElement,
            valueLabel: HTMLSpanElement,
            selectOptions: SelectOption[]
        ) => T,
        itemName: string,
        selectContainer: HTMLLabelElement|null,
    ): T | null {
        const label = querySelector<HTMLSpanElement>(selectContainer, '[data-error-for]');
        const select = querySelector<HTMLDivElement>(selectContainer, '[data-select]');
        const trigger = querySelector<HTMLButtonElement>(selectContainer, '[data-select-trigger]');
        const valueLabel = querySelector<HTMLSpanElement>(selectContainer, '[data-select-value]');
        const selectOptions = Array.from(querySelectorAll<HTMLButtonElement>(selectContainer, '[data-select-option]'));

        const required = {
            selectContainer,
            label,
            select,
            trigger,
            valueLabel,
            selectOptions,
        }

        if (!assertImported(itemName, required)) {
            return null;
        }

        return new this(
            itemName,
            required.label,
            required.select,
            required.trigger,
            required.valueLabel,
            required.selectOptions.map((el: HTMLButtonElement): SelectOption => new SelectOption(el))
        );
    }

    public override clearErrors(): void {
        super.clearErrors();

        this.trigger.classList.remove('is-error');
    }

    public override destroy(): void {
        super.destroy();

        this.select.classList.remove('open');
    }

    public getOptions(): SelectOption[] {
        return this.selectOptions;
    }

    public override getValue(): TValue {
        return this.enum as TValue;
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

        this.trigger.disabled = isBusy;
        this.trigger.setAttribute('aria-disabled', String(isBusy));
    }

    public override setErrors(...errorMessages: string[]): void {
        super.setErrors(...errorMessages);

        this.trigger.classList.add('is-error');
    }
}
