import { Component } from '../../../common/component/contracts';
import { TextHighlightable } from './hightlightable';
import { Tag } from '../../tag';

export class TagPill extends TextHighlightable implements Component<Tag> {
    private readonly tag: Tag;

    public constructor(tag: Tag) {
        const tagElement = document.createElement('span');
        tagElement.classList.add('tag');
        tagElement.textContent = tag.name;
        super(tagElement);

        this.tag = tag;
    }

    public override element(): HTMLSpanElement {
        return super.element();
    }

    public getValue(): Tag {
        return this.tag;
    }
}
