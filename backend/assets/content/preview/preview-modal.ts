import { Component } from '../../components/contracts';
import { LocalDate } from '../../components/local-date';
import { Content } from '../content';
import { TagList } from '../tag/tag-list';
import { FileList } from './file/file-list';
import { assertImported, querySelector, querySelectorAll } from '../../utils/importer';

class ContentFields implements Component<Content|null> {
    private readonly titles: NodeListOf<HTMLSpanElement>;
    private readonly category: HTMLSpanElement;
    private readonly type: HTMLSpanElement;
    private readonly price: HTMLSpanElement;
    private readonly intensity: HTMLSpanElement;
    private readonly language: HTMLSpanElement;
    private readonly status: HTMLSpanElement;
    private readonly created: HTMLSpanElement;
    private readonly sales: HTMLSpanElement;
    private readonly description: HTMLParagraphElement;
    private readonly tags: TagList;
    public readonly files: FileList;

    private content: Content|null = null;

    public constructor(
        titles: NodeListOf<HTMLSpanElement>,
        category: HTMLSpanElement,
        type: HTMLSpanElement,
        price: HTMLSpanElement,
        intensity: HTMLSpanElement,
        language: HTMLSpanElement,
        status: HTMLSpanElement,
        created: HTMLSpanElement,
        sales: HTMLSpanElement,
        description: HTMLParagraphElement,
        tags: TagList,
        files: FileList,
    ) {
        this.titles = titles;
        this.category = category;
        this.type = type;
        this.price = price;
        this.intensity = intensity;
        this.language = language;
        this.status = status;
        this.created = created;
        this.sales = sales;
        this.description = description;
        this.tags = tags;
        this.files = files;
    }

    public static from(previewModal: HTMLElement|null): ContentFields|null {
        const titles = querySelectorAll<HTMLSpanElement>(previewModal, '[data-content-title]');
        const category = querySelector<HTMLSpanElement>(previewModal, '[data-content-category]');
        const type = querySelector<HTMLSpanElement>(previewModal, '[data-content-mode]');
        const price = querySelector<HTMLSpanElement>(previewModal, '[data-content-price]');
        const intensity = querySelector<HTMLSpanElement>(previewModal, '[data-content-intensity]');
        const language = querySelector<HTMLSpanElement>(previewModal, '[data-content-language]');
        const status = querySelector<HTMLSpanElement>(previewModal, '[data-content-status]');
        const created = querySelector<HTMLSpanElement>(previewModal, '[data-content-created]');
        const sales = querySelector<HTMLSpanElement>(previewModal, '[data-content-sales]');
        const description = querySelector<HTMLParagraphElement>(previewModal, '[data-content-description]');
        const tags = new TagList(querySelector<HTMLDivElement>(previewModal, '[data-content-tags]'));

        const files = FileList.from(
            querySelector<HTMLFormElement>(previewModal, '[data-content-files]'),
            querySelector<HTMLSpanElement>(previewModal, '[data-content-file-count]'),
            querySelector<HTMLSpanElement>(previewModal, '[data-content-size]'),
        );

        const required = {
            previewModal,
            titles,
            category,
            type,
            price,
            intensity,
            language,
            status,
            created,
            sales,
            description,
            tags,
            files,
        };

        if (!assertImported('content-fields', required)) {
            return null;
        }

        return new ContentFields(
            required.titles,
            required.category,
            required.type,
            required.price,
            required.intensity,
            required.language,
            required.status,
            required.created,
            required.sales,
            required.description,
            required.tags,
            required.files,
        );
    }

    public destroy(): void {
        this.content = null;
        this.setValue(null);
    }

    public getValue(): Content | null {
        return this.content;
    }

    public setValue(content: Content|null): void {
        this.content = content;

        this.titles.forEach((title: HTMLSpanElement): void => {
            title.textContent = content?.title ?? '';
        });
        this.category.textContent = content?.category.name ?? '';
        this.type.textContent = content?.mode.name ?? '';
        this.price.textContent = content?.price.toString() ?? '';
        this.intensity.textContent = content?.intensity.toString() ?? '';
        this.language.textContent = content?.language.name ?? '';
        this.status.textContent = content?.status.name ?? '';
        this.created.textContent = LocalDate.from(content?.createdAt)?.format() ?? '';
        this.sales.textContent = content?.sales.toString() ?? '';
        this.description.textContent = content?.description ?? '';
        this.tags.setValue(content?.tags ?? []);
        this.files.setValue(content?.files.list ?? []);
    }
}

export class ContentPreviewModal implements Component<Content|null> {
    private readonly contentPreviewModalElement: HTMLDivElement;
    private readonly contentFields: ContentFields;
    private readonly closeBtns: NodeListOf<HTMLButtonElement>;

    public constructor(
        contentPreviewModalElement: HTMLDivElement,
        contentFields: ContentFields,
        closeBtns: NodeListOf<HTMLButtonElement>,
    ) {
        this.contentPreviewModalElement = contentPreviewModalElement;
        this.contentFields = contentFields;
        this.closeBtns = closeBtns;

        this.contentFields.files.onChange((): void => {
            this.closeBtns.forEach((closeBtn: HTMLButtonElement): void => {
                closeBtn.disabled = this.contentFields.files.isActive();
            });
        });

        this.closeBtns.forEach((closeBtn: HTMLButtonElement): void => {
            closeBtn.addEventListener('click', (): void => this.close());
        });

        /**
         * Automatically close modal if we click the backdrop
         */
        this.contentPreviewModalElement.addEventListener('click', (event: MouseEvent): void => {
            if ((event.target as HTMLElement) === this.contentPreviewModalElement && !this.contentFields.files.isActive()) {
                this.close();
            }
        });

        /**
         * Listen for content:preview events from any content MediaThumb component
         */
        document.addEventListener('content:preview', ((event: CustomEvent<Content>): void => {
            this.setValue(event.detail);
            this.open();
        }) as EventListener);
    }

    public static from(contentPreviewModalElement: HTMLDivElement|null): ContentPreviewModal|null {
        const contentFields = ContentFields.from(contentPreviewModalElement);
        const closeBtns = querySelectorAll<HTMLButtonElement>(contentPreviewModalElement, '[data-preview-content-close]');

        const required = {
            contentPreviewModalElement,
            contentFields,
            closeBtns,
        }

        if (!assertImported('content-preview-modal', required)) {
            return null;
        }

        return new ContentPreviewModal(
            required.contentPreviewModalElement,
            required.contentFields,
            required.closeBtns,
        );
    }

    public close(): void {
        this.contentPreviewModalElement.classList.remove('is-active');
        this.setValue(null);
    }

    public getValue(): Content|null {
        return this.contentFields.getValue();
    }

    public open(): void {
        this.contentPreviewModalElement.classList.add('is-active');
    }

    public setValue(content: Content|null): void {
        this.contentFields.setValue(content);
    }
}
