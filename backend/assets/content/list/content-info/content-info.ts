import { Content } from '../../content';
import { CellElement } from '../cell-element';
import { Highlightable } from './hightlightable';
import {ContentTitle} from './content-title';
import {ContentDescription} from './content-description';
import { TagPill } from './tag-pill';
import { TagList } from './tag-list';

export class ContentInfo extends CellElement implements Highlightable {
    private readonly contentTitle: ContentTitle;
    private readonly contentDescription: ContentDescription;
    private readonly tagList: TagList;

    public constructor(content: Content) {
        super();

        const contentInfo = document.createElement('div');
        contentInfo.classList.add('content-info');

        this.contentTitle = new ContentTitle(content.title);
        contentInfo.appendChild(this.contentTitle.element());

        this.contentDescription = new ContentDescription(content.description);
        contentInfo.appendChild(this.contentDescription.element());

        this.tagList = new TagList(content.tags);
        contentInfo.appendChild(this.tagList.element());

        this.cell.appendChild(contentInfo);
    }

    public highlight(searchTerm: string): void {
        this.contentTitle.highlight(searchTerm);
        this.contentDescription.highlight(searchTerm);
        this.tagList.getValue().forEach((tag: TagPill): void => tag.highlight(searchTerm));
    }
}
