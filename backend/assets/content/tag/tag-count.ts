import {BusyAware, ChangeAware, ErrorClearable } from '../../common/component/contracts';
import { BaseComponent } from '../../common/component/base-component';
import { Tag } from './tag';
import { TagComponent } from './tag-component';
import { assertImported } from '../../common/importer';

export class TagCount extends BaseComponent<TagComponent[]> implements BusyAware, ChangeAware, ErrorClearable {
    private readonly label: HTMLDivElement;
    private readonly countValue: HTMLSpanElement;
    private readonly selectedTags: HTMLDivElement;
    private readonly tagComponents: Map<string, TagComponent> = new Map<string, TagComponent>();
    private readonly eventTarget: EventTarget = new EventTarget();

    public constructor(
        label: HTMLDivElement,
        countValue: HTMLSpanElement,
        selectedTags: HTMLDivElement,
    ) {
        super();

        this.label = label;
        this.countValue = countValue;
        this.selectedTags = selectedTags;

        this.onChange((): void => {
            this.countValue.textContent = String(this.getValue().length);
        });
    }

    public static from(
        label: HTMLDivElement|null,
        countValue: HTMLSpanElement|null,
        selectedTags: HTMLDivElement|null,
    ): TagCount|null {
        const required = {
            label,
            countValue,
            selectedTags,
        };

        if (!assertImported('tag-count', required)) {
            return null;
        }

        return new TagCount(
            required.label,
            required.countValue,
            required.selectedTags,
        );
    }

    public addTag(tag: Tag): boolean {
        if (this.tagComponents.has(tag.publicId)) {
            return false;
        }

        const tagComponent = new TagComponent(tag);
        tagComponent.onError(() => this.eventTarget.dispatchEvent(new CustomEvent('tag-count:change')));
        tagComponent.onRemove((tagComponent: TagComponent): void => this.removeTagComponent(tagComponent));

        this.tagComponents.set(tagComponent.getValue().publicId, tagComponent);
        this.selectedTags.appendChild(tagComponent.element());
        this.eventTarget.dispatchEvent(new CustomEvent('tag-count:change'));

        return true;
    }

    /**
     * There's no way to clear the errors of a tagComponent once it is errored,
     * the only way is to let the user delete them.
     */
    public clearErrors(): void {
        this.label.classList.remove('is-error');
    }

    public override destroy(): void {
        this.tagComponents.forEach((tagComponent: TagComponent): void => this.removeTagComponent(tagComponent));
    }

    public getTagComponent(publicId: string): TagComponent|null {
        return this.tagComponents.get(publicId) ?? null;
    }

    public override getValue(): TagComponent[] {
        return Array
            .from(this.tagComponents.values())
            .filter((tagComponent: TagComponent): boolean => !tagComponent.hasErrors());
    }

    public hasTag(tag: Tag): boolean {
        return this.tagComponents.has(tag.publicId);
    }

    public onChange(listener: () => void): void {
        this.eventTarget.addEventListener('tag-count:change', listener);
    }

    private removeTagComponent(tagComponent: TagComponent): void {
        if (!this.tagComponents.has(tagComponent.getValue().publicId)) {
            return;
        }

        this.tagComponents.delete(tagComponent.getValue().publicId);
        this.selectedTags.removeChild(tagComponent.element());
        this.eventTarget.dispatchEvent(new CustomEvent('tag-count:change'));
    }

    public setBusy(isBusy: boolean): void {
        this.label.classList.toggle('is-disabled', isBusy);
        this.tagComponents.forEach((tagComponent: TagComponent): void => tagComponent.setBusy(isBusy));
    }

    /**
     * This is set from the outside by TagsComponent, who has the responsibility of validation using
     * the filtered getValue() that excludes TagComponents that have errors.
     */
    public override setErrors(): void {
        this.label.classList.add('is-error');
    }
}
