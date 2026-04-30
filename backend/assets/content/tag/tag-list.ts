import { Component, HtmlElementWrapper } from '../../components/contracts';
import { Tag } from '../tag';
import { TagPill } from './tag-pill';

export class TagList implements Component<TagPill[]>, HtmlElementWrapper {
    private readonly tagList: HTMLDivElement;
    private tagPills: TagPill[] = [];

    public constructor(tagList: HTMLDivElement|null = null) {
        if (!tagList) {
            tagList = document.createElement('div');
            tagList.classList.add('tag-list');
        }

        this.tagList = tagList;
    }

    public element(): HTMLDivElement {
        return this.tagList;
    }

    public getValue(): TagPill[] {
        return this.tagPills;
    }

    public highLight(searchTerm: string): void {
        this.tagPills.forEach((tagPill: TagPill): void => {
            tagPill.highlight(searchTerm);
        });
    }

    public setValue(tags: Tag[]): void {
        this.tagPills = [];
        this.tagList.innerHTML = '';

        tags.forEach((tag: Tag): void => {
            const tagPill = new TagPill(tag);
            this.tagPills.push(tagPill);
            this.tagList.appendChild(tagPill.element());
        });
    }
}
