import { BusyAware, Component, HtmlElementWrapper } from '../../../common/component/contracts';
import { Tag } from '../../tag'
import { TagCount } from './tag-count';
import { TagInput } from './tag-input';
import { BackendError } from '../../../common/backend-error';
import { assertImported } from '../../../common/importer';

abstract class DropdownOption implements HtmlElementWrapper {
    protected readonly option: HTMLButtonElement;

    protected constructor() {
        this.option = document.createElement('button');
        this.option.type = 'button';
        this.option.className = 'tag-option';
    }

    public element(): HTMLButtonElement {
        return this.option;
    }
}

class LoadingDropdownOption extends DropdownOption {
    public constructor(message: string) {
        super();

        this.option.classList.add('is-loading');
        this.option.textContent = message;
    }
}

class ErrorDropdownOption extends DropdownOption {
    public constructor(message: string) {
        super();

        this.option.classList.add('is-error');
        this.option.textContent = message;
    }
}

export class TagDropdownOption extends DropdownOption implements Component<Tag> {
    private readonly tag: Tag;

    private selectedListener: ((option: TagDropdownOption) => void)|null = null;

    public constructor(tag: Tag) {
        super();

        this.tag = tag;
        this.option.textContent = this.tag.name;

        this.option.addEventListener('click', (): void => {
            if (this.selectedListener) {
                this.selectedListener(this);
            }
        });
    }

    public getValue(): Tag {
        return this.tag;
    }

    public onSelected(listener: (option: TagDropdownOption) => void): void {
        this.selectedListener = listener;
    }

    public setActive(isActive: boolean): void {
        this.option.classList.toggle('is-active', isActive);
    }
}

export class TagDropdown implements BusyAware, Component<Tag|null> {
    private readonly dropdown: HTMLDivElement;
    private readonly eventTarget: EventTarget = new EventTarget();
    private readonly tagDropdownOptions: TagDropdownOption[] = [];

    private activeIndex: number = -1;
    private preventRefocus: boolean = false;
    private isLoading: boolean = false;

    public constructor(dropdown: HTMLDivElement, tagInput: TagInput) {
        this.dropdown = dropdown;

        document.addEventListener('click', (event: PointerEvent): void => {
            if (this.isLoading) {
                return;
            }

            const target = event.target as Node;
            const isInsideDropdown = this.dropdown.contains(target);
            const isInsideInput = tagInput.element().contains(target);

            if (!isInsideDropdown && !isInsideInput) {
                this.hide();
            }
        });
    }

    public static from(dropdown: HTMLDivElement|null, tagInput: TagInput|null): TagDropdown|null {
        const required = {
            dropdown,
            tagInput,
        };

        if (!assertImported('tag-dropdown', required)) {
            return null;
        }

        return new TagDropdown(
            required.dropdown,
            required.tagInput,
        );
    }

    public clear(): void {
        this.dropdown.hidden = true;
        this.dropdown.style.display = 'none';
        this.dropdown.setAttribute('aria-hidden', 'true');
        this.dropdown.innerHTML = '';
        this.activeIndex = -1;
        this.isLoading = false;
    }

    public creatingTag(): void {
        this.showMessage('Creating tag...', 'loading');
    }

    public error(error: BackendError): void {
        this.showMessage(error.message, 'error');
    }

    public fetchingTags(): void {
        this.showMessage('Fetching tags...', 'loading');
    }

    public getActiveOption(): TagDropdownOption|null {
        return (this.tagDropdownOptions[this.activeIndex] ?? null) as TagDropdownOption|null;
    }

    private getOptions(): TagDropdownOption[] {
        return this.tagDropdownOptions;
    }

    public getValue(): Tag|null {
        return this.getActiveOption()?.getValue() ?? null;
    }

    public hide(refocus: boolean = false): boolean {
        this.clear();

        return refocus && !this.preventRefocus;
    }

    public isActive(): boolean {
        return this.activeIndex >= 0;
    }

    public isOpen(): boolean {
        return !this.dropdown.hidden && this.getOptions().length > 0;
    }

    public onSelectedTag(listener: (tag: Tag) => void): void {
        this.eventTarget.addEventListener('tag-dropdown:selected', (event: Event): void => {
            listener((event as CustomEvent<Tag>).detail);
        });
    }

    public setBusy(isBusy: boolean): void {
        if (isBusy) {
            this.hide();
        }
    }

    public setPreventRefocus(preventRefocus: boolean): void {
        this.preventRefocus = preventRefocus;
    }

    public show(tags: Tag[], tagCount: TagCount): void {
        this.dropdown.innerHTML = '';

        if (!tags.length) {
            this.hide(true);

            return;
        }

        tags.forEach((tag: Tag): void => {
            if (tagCount.hasTag(tag)) {
                return;
            }

            const tagDropdownOption = new TagDropdownOption(tag);

            tagDropdownOption.onSelected((): void => {
                this.eventTarget.dispatchEvent(new CustomEvent('tag-dropdown:selected', { detail: tag }));
            });

            this.tagDropdownOptions.push(tagDropdownOption);
            this.dropdown.appendChild(tagDropdownOption.element());
        });

        this.dropdown.hidden = false;
        this.dropdown.style.display = 'block';
        this.dropdown.setAttribute('aria-hidden', 'false');
        this.setActiveOption(0);
    }

    private showMessage(message: string, type: 'loading'|'error' = 'loading'): void {
        this.dropdown.innerHTML = '';
        this.dropdown.hidden = false;
        this.dropdown.style.display = 'block';
        this.dropdown.setAttribute('aria-hidden', 'false');
        this.isLoading = type === 'loading';

        const dropdownOption = this.isLoading
            ? new LoadingDropdownOption(message)
            : new ErrorDropdownOption(message);

        this.dropdown.appendChild(dropdownOption.element());
    }

    private setActiveOption(index: number): void {
        if (this.tagDropdownOptions.length === 0) {
            this.activeIndex = -1;

            return;
        }

        this.tagDropdownOptions.forEach((tagDropdownOption) => tagDropdownOption.setActive(false));

        this.activeIndex = Math.max(
             0,
            Math.min(index, this.tagDropdownOptions.length - 1),
        );

        this.getActiveOption()?.setActive(true);
    }

    public setNextOption(): void {
        this.setActiveOption(this.activeIndex + 1);
    }

    public setPreviousOption(): void {
        this.setActiveOption(this.activeIndex - 1);
    }
}
