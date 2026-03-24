import { Component, HtmlElementWrapper } from '../../common/component/contracts';
import { Highlightable } from './content-info/hightlightable';
import { MediaThumb } from './media-thumb';
import { ContentInfo } from './content-info/content-info';
import { CategoryPill } from './category-pill';
import { Bundle } from './bundle';
import { PriceInfo } from './price-info';
import { SalesInfo } from './sales-info';
import { StatusPill } from './status-component';
import { CreatedAt } from './created-at';
import { Content } from '../content';

export class ContentComponent implements Component<Content>, Highlightable, HtmlElementWrapper {
    private readonly mediaThumb: MediaThumb;
    private readonly contentInfo: ContentInfo;
    private readonly categoryComponent: CategoryPill;
    private readonly bundle: Bundle;
    private readonly priceInfo: PriceInfo;
    private readonly salesInfo: SalesInfo;
    private readonly statusPill: StatusPill;
    private readonly createdAt: CreatedAt;
    private readonly row: HTMLTableRowElement;
    private readonly content: Content;

    public constructor(content: Content, isNew: boolean) {
        this.content = content;
        this.row = document.createElement('tr');
        this.row.classList.add('content-row');
        this.row.setAttribute('data-content-row', '');

        if (isNew) {
            this.row.classList.add('is-new');

            setTimeout((): void => {
                this.row.classList.remove('is-new');
            }, 600);
        }

        /**
         * Thumbnail
         */
        this.mediaThumb = new MediaThumb(content.files);
        this.row.appendChild(this.mediaThumb.element());

        /**
         * Content info
         */
        this.contentInfo = new ContentInfo(content);
        this.row.appendChild(this.contentInfo.element());

        /**
         * Category pill
         */
        this.categoryComponent = new CategoryPill(content.category);
        this.row.appendChild(this.categoryComponent.element());

        /**
         * Bundle
         */
        this.bundle = new Bundle(content.files);
        this.row.appendChild(this.bundle.element());

        /**
         * Price info
         */
        this.priceInfo = new PriceInfo(content);
        this.row.appendChild(this.priceInfo.element());

        /**
         * Sales info
         */
        this.salesInfo = new SalesInfo(content.sales);
        this.row.appendChild(this.salesInfo.element());

        /**
         * Status pill
         */
        this.statusPill = new StatusPill(content.status);
        this.row.appendChild(this.statusPill.element());

        /**
         * Created at
         */
        this.createdAt = new CreatedAt(content.createdAt);
        this.row.appendChild(this.createdAt.element());
    }

    public element(): HTMLTableRowElement {
        return this.row;
    }

    public getValue(): Content {
        return this.content;
    }

    public highlight(searchTerm: string): void {
        this.contentInfo.highlight(searchTerm);
    }
}
