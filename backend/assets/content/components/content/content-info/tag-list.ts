import { Component, HtmlElementWrapper } from '../../../../components/contracts';
import { Tag } from '../../../tag';
import { TagPill } from './tag-pill';

export class TagList implements Component<TagPill[]>, HtmlElementWrapper {
    private readonly tagList: HTMLDivElement;
    private readonly tagComponents: TagPill[] = [];

    public constructor(tags: Tag[]) {
        this.tagList = document.createElement('div');
        this.tagList.classList.add('tag-list');

        tags.forEach((tag: Tag): void => {
            const tagComponent = new TagPill(tag);
            this.tagComponents.push(tagComponent);
            this.tagList.appendChild(tagComponent.element());
        })
    }

    public element(): HTMLDivElement {
        return this.tagList;
    }

    public getValue(): TagPill[] {
        return this.tagComponents;
    }
}
