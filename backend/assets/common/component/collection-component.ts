import { BusyAware } from './contracts';
import { ValidatableComponent } from './validatable-component';
import { capitalize, pluralize } from '../strings';

export abstract class CollectionComponent<TItem> extends ValidatableComponent<TItem[]> implements BusyAware {
    protected readonly itemName: string;
    protected readonly minCount: number|null;
    protected readonly maxCount: number|null;

    protected constructor(
        itemName: string,
        label: HTMLHeadingElement,
        minCount: number|null,
        maxCount: number|null,
    ) {
        super(label);

        this.itemName = itemName;
        this.minCount = minCount;
        this.maxCount = maxCount;
    }

    protected override frontendErrors(): string[] {
        let frontendErrors = [];

        if (this.minCount !== null && this.getValue().length < this.minCount) {
            const tooShortMessage = `${capitalize(pluralize(this.name()))} must have at least ${this.minCount} ${pluralize(this.name(), this.minCount)}`;
            frontendErrors.push(tooShortMessage);
        }

        if (this.maxCount !== null && this.getValue().length > this.maxCount) {
            const tooLongMessage = `${capitalize(pluralize(this.name()))} cannot have more than ${this.maxCount} ${pluralize(this.name(), this.maxCount)}`;
            frontendErrors.push(tooLongMessage);
        }

        return frontendErrors;
    }

    public abstract override getValue(): TItem[];

    protected override name(): string {
        return this.itemName;
    }

    protected notifyChange(): void {
        this.changeListeners.forEach((listener: () => void): void => listener());
    }

    public abstract setBusy(isBusy: boolean): void;
}
