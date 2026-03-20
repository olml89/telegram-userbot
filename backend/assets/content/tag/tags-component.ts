import { CollectionComponent } from '../../common/component/collection-component';
import { Tag } from './tag';
import { TagCount } from './tag-count';
import { TagInput } from './tag-input';
import { TagDropdown } from './tag-dropdown';
import { TagComponent } from './tag-component';
import { BackendError } from '../../common/backend-error';
import { assertImported, querySelector } from '../../common/importer';

export class TagsComponent extends CollectionComponent<Tag> {
    private readonly tagInput: TagInput;
    private readonly tagDropdown: TagDropdown;
    private readonly tagCount: TagCount;

    public constructor(
        heading: HTMLHeadingElement,
        tagInput: TagInput,
        tagDropdown: TagDropdown,
        tagCount: TagCount,
        minCount: number|null,
        maxCount: number|null,
    ) {
        super('tag', heading, minCount, maxCount);

        this.tagInput = tagInput;
        this.tagDropdown = tagDropdown;
        this.tagCount = tagCount;

        /**
         * TagCount events
         */
        this.tagCount.onChange((): void => {
            this.notifyChange();
        });

        /**
         * TagDropdown events
         */
        this.tagDropdown.onSelectedTag((tag: Tag): void => {
            if (!this.tagCount.addTag(tag)) {
                return;
            }

            this.tagInput.clear();

            if (this.tagDropdown.hide(true)) {
                this.tagInput.refocus();
            }
        });

        /**
         * TagInput events
         */
        this.tagInput.onSelectedTag((tag: Tag): void => {
            this.tagCount.addTag(tag);
            this.tagInput.clear();
        });

        this.tagInput.onFocusEvent((): void => {
            this.tagDropdown.setPreventRefocus(false);
        });

        this.tagInput.onBlurEvent((): void => {
            this.tagDropdown.setPreventRefocus(true);

            setTimeout((): void => {
                this.tagDropdown.hide();
            }, 150);
        });

        this.tagInput.onNavigate((direction: 'next'|'previous'): void => {
            if (!this.tagDropdown.isOpen()) {
                return;
            }

            direction === 'next'
                ? this.tagDropdown.setNextOption()
                : this.tagDropdown.setPreviousOption();
        });

        this.tagInput.onEnter((value: string): Promise<void> => this.handleEnter(value));
        this.tagInput.onQuery((query: string): Promise<void> => this.handleQuery(query));
        this.tagInput.onQueryClear((): boolean => this.tagDropdown.hide());
    }

    public static from(tagsElement: HTMLDivElement|null): TagsComponent|null {
        const heading = querySelector<HTMLHeadingElement>(tagsElement, '[data-error-for]');
        const tagInput = TagInput.from(querySelector<HTMLInputElement>(tagsElement, '[data-tag-input]'));

        const tagDropdown = TagDropdown.from(
            querySelector<HTMLDivElement>(tagsElement, '[data-tag-dropdown]'),
            tagInput,
        );

        const tagCount = TagCount.from(
            querySelector<HTMLDivElement>(tagsElement, '[data-tag-count]'),
            querySelector<HTMLSpanElement>(tagsElement, '[data-tag-count-value]'),
            querySelector<HTMLDivElement>(tagsElement, '[data-tag-selected]'),
        );

        const required = {
            tagsElement,
            heading,
            tagInput,
            tagDropdown,
            tagCount,
        }

        if (!assertImported('tags-component', required)) {
            return null;
        }

        const minCountText = required.tagsElement.dataset['minCount'];
        const maxCountText = required.tagsElement.dataset['maxCount'];

        return new TagsComponent(
            required.heading,
            required.tagInput,
            required.tagDropdown,
            required.tagCount,
            minCountText === undefined ? null : Number(minCountText),
            maxCountText === undefined ? null : Number(maxCountText),
        );
    }

    public override clearErrors(): void {
        super.clearErrors();

        this.tagCount.clearErrors();
    }

    private async createTag(name: string): Promise<Tag> {
        const response = await fetch('/api/tags', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                name,
            }),
        });

        if (!response.ok) {
            throw await BackendError.from(
                response,
                'Failed to create tag',
            );
        }

        return await response.json() as Tag;
    }

    public override destroy(): void {
        super.destroy();

        this.tagDropdown.clear();
        this.tagCount.destroy();
    }

    private async fetchTags(query: string): Promise<Tag[]> {
        const response = await fetch(`/api/tags?query=${encodeURIComponent(query)}`);

        if (!response.ok) {
            throw await BackendError.from(
                response,
                'Failed to fetch tags',
            );
        }

        const payload = await response.json();

        return payload.map(
            (payloadItem: { publicId: string; name: string }) => payloadItem as Tag,
        );
    }

    public override getValue(): Tag[] {
        return this.tagCount.getValue().map((tagComponent: TagComponent): Tag => tagComponent.getValue());
    }

    private async handleEnter(value: string): Promise<void> {
        if (this.tagDropdown.isOpen() && this.tagDropdown.isActive()) {
            const tag = this.tagDropdown.getValue();

            if (tag === null || !this.tagCount.addTag(tag)) {
                return;
            }

            this.tagInput.clear();

            if (this.tagDropdown.hide(true)) {
                this.tagInput.refocus();
            }

            return;
        }

        if (value.length === 0 || !this.tagInput.validate()) {
            return;
        }

        try {
            this.tagInput.setBusy(true);
            this.tagDropdown.creatingTag();
            const created = await this.createTag(value);

            if (this.tagCount.addTag(created)) {
                this.tagInput.clear();
                this.tagDropdown.hide();
            }
        } catch (e: any) {
            const backendError = e as BackendError;
            console.error(backendError.consoleMessage);
            this.tagDropdown.error(backendError);
        } finally {
            this.tagInput.setBusy(false);
        }
    }

    private async handleQuery(query: string): Promise<void> {
        try {
            this.tagInput.setBusy(true);
            this.tagDropdown.fetchingTags();
            const tags = await this.fetchTags(query);

            this.tagDropdown.show(
                tags,
                this.tagCount,
            );
        } catch (e: any) {
            const backendError = e as BackendError;

            console.error(backendError.consoleMessage);
            this.tagDropdown.error(backendError);
        } finally {
            this.tagInput.setBusy(false);
        }
    }

    public override setBusy(isBusy: boolean): void {
        this.tagCount.setBusy(isBusy);
        this.tagDropdown.setBusy(isBusy);
        this.tagInput.setBusy(isBusy);
    }

    public override setErrors(...errorMessages: string[]) {
        super.setErrors(...errorMessages);

        this.tagCount.setErrors();
    }

    public setItemError(publicId: string, message: string): void {
        this.tagCount.getTagComponent(publicId)?.setErrors(message);
    }
}
