import { BaseComponent } from '../../../components/base-component';
import { Tag } from '../../tag';
import { assertImported } from '../../../utils/importer';

export class TagList extends BaseComponent<Tag[]> {
    private readonly tagList: HTMLDivElement;

    private tags: Tag[] = [];

    public constructor(tagList: HTMLDivElement) {
        super();

        this.tagList = tagList;
    }

    public static from(tagList: HTMLDivElement|null): TagList|null {
        const required = {
            tagList,
        };

        if (!assertImported('content:preview:tag-list', required)) {
            return null;
        }

        return new TagList(required.tagList);
    }

    private createTagPill(tag: Tag): HTMLSpanElement {
        const tagPill = document.createElement('span');
        tagPill.classList.add('tag');
        tagPill.textContent = tag.name;

        return tagPill;
    }

    public override getValue(): Tag[] {
        return this.tags;
    }

    public setValue(tags: Tag[]): void {
        this.tags = tags;

        this.tagList.innerHTML = '';
        this.tags.forEach(tag => this.tagList.appendChild(this.createTagPill(tag)));
    }
}
