import { Component, HtmlElementWrapper } from '../../../components/contracts';
import { Highlightable } from './content-info/hightlightable';
import { MediaThumb } from './media-thumb';
import { ContentInfo } from './content-info/content-info';
import { CategoryPill } from './category-pill';
import { Bundle } from './bundle';
import { PriceInfo } from './price-info';
import { SalesInfo } from './sales-info';
import { StatusPill } from './status-pill';
import { CreatedAt } from './created-at';
import { ActionsMenu } from './actions-menu';
import { Content } from '../../content';

export class ContentComponent implements Component<Content>, Highlightable, HtmlElementWrapper {
    private readonly mediaThumb: MediaThumb;
    private readonly contentInfo: ContentInfo;
    private readonly categoryComponent: CategoryPill;
    private readonly bundle: Bundle;
    private readonly priceInfo: PriceInfo;
    private readonly salesInfo: SalesInfo;
    private readonly statusPill: StatusPill;
    private readonly createdAt: CreatedAt;
    private readonly actionsMenu: ActionsMenu;
    private readonly row: HTMLTableRowElement;
    private readonly content: Content;

    private readonly removeListeners: Set<(contentComponent: ContentComponent) => void> = new Set();

    public constructor(content: Content) {
        this.content = content;
        this.row = document.createElement('tr');
        this.row.classList.add('content-row');
        this.row.setAttribute('data-content-row', '');

        /**
         * Thumbnail
         */
        this.mediaThumb = new MediaThumb(content);
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

        /**
         * ActionsMenu
         */
        this.actionsMenu = new ActionsMenu();
        this.row.appendChild(this.actionsMenu.element());
    }

    public delay(delay: number): this {
        this.row.style.animationDelay = `${delay}ms`;

        return this;
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

    public new(): this {
        this.row.classList.add('is-new');

        return this;
    }

    public onRemove(listener: (contentComponent: ContentComponent) => void): void {
        this.removeListeners.add(listener);
    }

    public remove(): void {
        this.row.classList.add('is-removing');

        this.row.addEventListener('animationend', (): void => {
            this.removeListeners.forEach((listener: (contentComponent: ContentComponent) => void): void => listener(this));
        }, { once: true });
    }
}
