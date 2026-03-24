import { Component, HtmlElementWrapper } from '../common/component/contracts';
import { FileContainer, Content } from './content';
import { File } from './file/file';
import { Tag } from './tag/tag';
import { Category } from './category/category';
import { Mode } from './mode/mode';
import { Status } from './status/status';

abstract class CellElement implements HtmlElementWrapper {
    protected readonly cell: HTMLTableCellElement;

    protected constructor () {
        this.cell = document.createElement('td');
    }

    public element(): HTMLTableCellElement {
        return this.cell;
    }
}

class MediaThumb extends CellElement {
    public constructor(files: FileContainer) {
        super();

        const fileCount = files.list.length;

        const thumbnailFiles = files
            .list
            .filter((file: File) => file.hasThumbnail)
            .slice(0, 4);

        const nonThumbnailFiles = files
            .list
            .filter((file: File) => !file.hasThumbnail)
            .slice(0, 4 - thumbnailFiles.length);

        const mediaThumb = document.createElement('div');
        mediaThumb.classList.add('media-thumb', this.calculateMediaClass(fileCount));

        this.addThumbnails(mediaThumb, thumbnailFiles);
        this.addPlaceholders(mediaThumb, nonThumbnailFiles);

        if (fileCount > 4) {
            mediaThumb.appendChild(this.createMediaMore(fileCount));
        }

        this.cell.appendChild(mediaThumb);
    }

    private addPlaceholders(mediaThumb: HTMLDivElement, nonThumbnailFiles: File[]): void {
        nonThumbnailFiles.forEach((nonThumbnailFile: File): void => {
            const placeholder = document.createElement('span');
            placeholder.classList.add('placeholder');
            placeholder.textContent = this.calculatePlaceholder(nonThumbnailFile);
            mediaThumb.appendChild(placeholder);
        });
    }

    private addThumbnails(mediaThumb: HTMLDivElement, thumbnailFiles: File[]): void {
        thumbnailFiles.forEach((thumbnailFile: File): void => {
            const thumbnail = document.createElement('img');
            thumbnail.classList.add('media-img');
            thumbnail.src = `/api/files/${thumbnailFile.publicId}/thumbnail`;
            thumbnail.alt = 'Media thumbnail';
            mediaThumb.appendChild(thumbnail);
        });
    }

    /**
     * fileCount can never be 0 as content has always at least 1 file
     */
    private calculateMediaClass(fileCount: number): string {
        if (fileCount === 1) {
            return 'media-single';
        }

        if (fileCount === 2) {
            return 'media-pair';
        }

        return 'media-grid';
    }

    private calculatePlaceholder(nonThumbnailFile: File): string {
        if (nonThumbnailFile.mimeType.startsWith('audio/')) {
            return '🎵';
        }

        if (nonThumbnailFile.mimeType.startsWith('text/plain') || nonThumbnailFile.mimeType.startsWith('application/pdf')) {
            return '📄';
        }

        return '📦';
    }

    private createMediaMore(fileCount: number): HTMLSpanElement {
        const more = document.createElement('span');
        more.classList.add('media-more');
        more.textContent = `+${fileCount - 4}`;

        return more;
    }
}

class ContentInfo extends CellElement {
    public constructor(content: Content) {
        super();

        const contentInfo = document.createElement('div');
        contentInfo.classList.add('content-info');
        contentInfo.appendChild(this.createContentTitle(content.title));
        contentInfo.appendChild(this.createContentDescription(content.description));
        contentInfo.appendChild(this.createTagList(content.tags));

        this.cell.appendChild(contentInfo);
    }

    private createContentTitle(title: string): HTMLDivElement {
        const contentTitle = document.createElement('div');
        contentTitle.classList.add('content-title');
        contentTitle.textContent = title;

        return contentTitle;
    }

    private createContentDescription(description: string): HTMLDivElement {
        const contentDescription = document.createElement('div');
        contentDescription.classList.add('content-description');
        contentDescription.textContent = description;

        return contentDescription;
    }

    private createTagList(tags: Tag[]): HTMLDivElement {
        const tagList = document.createElement('div');
        tagList.classList.add('tag-list');

        tags.forEach((tag: Tag) => {
            const tagElement = document.createElement('span');
            tagElement.classList.add('tag');
            tagElement.textContent = tag.name;
            tagList.appendChild(tagElement);
        })

        return tagList;
    }
}

class CategoryPill extends CellElement {
    public constructor(category: Category) {
        super();

        this.cell.appendChild(this.createCategoryPill(category));
    }


    private createCategoryPill(category: Category): HTMLSpanElement {
        const categoryPill = document.createElement('span');
        categoryPill.classList.add('pill');
        categoryPill.textContent = category.name;

        return categoryPill;
    }
}

class Bundle extends CellElement {
    public constructor(files: FileContainer) {
        super();

        this.cell.appendChild(this.createBundle(files));
    }

    private createBundle(files: FileContainer): HTMLSpanElement {
        const bundle = document.createElement('span');
        bundle.classList.add('bundle');
        bundle.textContent = `
            Images ${files.count.images} ·
            Videos ${files.count.videos} ·
            Audio ${files.count.audios} ·
            Docs ${files.count.documents}
        `;

        return bundle;
    }
}

class PriceInfo extends CellElement {
    public constructor(content: Content) {
        super();

        const priceInfo = document.createElement('div');
        priceInfo.appendChild(this.createPriceValue(content.price));
        priceInfo.appendChild(this.createModePill(content.mode));

        this.cell.appendChild(priceInfo);
    }

    private createPriceValue(price: number): HTMLDivElement {
        const priceValue = document.createElement('div');
        priceValue.classList.add('price-value');
        priceValue.textContent = `$${price.toString()}`;

        return priceValue;
    }

    private createModePill(mode: Mode): HTMLSpanElement {
        const modePill = document.createElement('span');
        modePill.classList.add('pill', 'pill-muted');
        modePill.textContent = mode.name;

        return modePill;
    }
}

class SalesInfo extends CellElement {
    public constructor(sales: number) {
        super();

        this.cell.textContent = sales.toString();
    }
}

class StatusPill extends CellElement {
    public constructor(status: Status) {
        super();

        this.cell.appendChild(this.createStatusPill(status));
    }

    private createStatusPill(status: Status): HTMLSpanElement {
        const statusPill = document.createElement('span');
        statusPill.classList.add('pill', `pill-${status.value}`);
        statusPill.textContent = status.name;

        return statusPill;
    }
}

class CreatedAt extends CellElement {
    public constructor(iso8601CreatedAt: string) {
        super();

        this.cell.textContent = this.formatDate(iso8601CreatedAt);
    }

    private formatDate(iso8601CreatedAt: string): string {
        const date = new Date(iso8601CreatedAt);

        return date.toLocaleString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
        });
    }
}

export class ContentComponent implements Component<Content>, HtmlElementWrapper {
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
        const mediaThumb = new MediaThumb(content.files);
        this.row.appendChild(mediaThumb.element());

        /**
         * Content info
         */
        const contentInfo = new ContentInfo(content);
        this.row.appendChild(contentInfo.element());

        /**
         * Category pill
         */
        const categoryPill = new CategoryPill(content.category);
        this.row.appendChild(categoryPill.element());

        /**
         * Bundle
         */
        const bundle = new Bundle(content.files);
        this.row.appendChild(bundle.element());

        /**
         * Price info
         */
        const priceInfo = new PriceInfo(content);
        this.row.appendChild(priceInfo.element());

        /**
         * Sales info
         */
        const salesInfo = new SalesInfo(content.sales);
        this.row.appendChild(salesInfo.element());

        /**
         * Status pill
         */
        const statusPill = new StatusPill(content.status);
        this.row.appendChild(statusPill.element());

        /**
         * Created at
         */
        const createdAt = new CreatedAt(content.createdAt);
        this.row.appendChild(createdAt.element());
    }

    public element(): HTMLTableRowElement {
        return this.row;
    }

    public getValue(): Content {
        return this.content;
    }
}
