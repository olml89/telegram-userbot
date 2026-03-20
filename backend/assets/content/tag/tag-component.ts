import { BusyAware, HtmlElementWrapper } from '../../common/component/contracts';
import { HtmlLabelComponent } from '../../common/component/html-label-component';
import { Tag } from './tag';

export class TagComponent extends HtmlLabelComponent<Tag> implements BusyAware, HtmlElementWrapper {
    private readonly tag: Tag;
    private readonly removeBtn: HTMLButtonElement;
    private readonly eventTarget: EventTarget = new EventTarget();

    public constructor (tag: Tag) {
        super(TagComponent.createElement(tag.name));

        this.tag = tag;

        this.removeBtn = this.label.querySelector<HTMLButtonElement>('.tag-remove') as HTMLButtonElement;
        this.removeBtn.addEventListener('click', (event: Event): void => {
            event.stopPropagation();

            const tagRemoved = new CustomEvent('tag:removed', { detail: this });
            this.eventTarget.dispatchEvent(tagRemoved);
        });
    }

    private static createElement(name: string): HTMLSpanElement {
        const element = document.createElement('span');
        element.className = 'tag tag-selected';

        element.innerHTML = `
            <span class="tag-label">${name}</span>
            <button type="button" class="tag-remove" aria-label="Remove tag">✕</button>
        `;

        return element;
    }

    public element(): HTMLSpanElement {
        return this.label;
    }

    public override getValue(): Tag {
        return this.tag;
    }

    public onError(listener: () => void): void {
        this.eventTarget.addEventListener('tag:error', listener);
    }

    public onRemove(listener: (tagComponent: TagComponent) => void): void {
        this.eventTarget.addEventListener('tag:removed', (event: Event): void => {
            listener((event as CustomEvent<TagComponent>).detail);
        });
    }

    public override setErrors(...errorMessages: string[]): void {
        super.setErrors(...errorMessages);

        this.eventTarget.dispatchEvent(new CustomEvent('tag:error'));
    }

    public setBusy(isBusy: boolean): void {
        this.label.classList.toggle('is-disabled', isBusy);
        this.removeBtn.disabled = isBusy;
    }
}
