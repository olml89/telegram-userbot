import { Component } from '../../components/contracts';
import { LocalDate } from '../../components/local-date';
import { MessageBox } from '../../components/message-box/message-box';
import { Content } from '../content';
import { File as BackendFile } from '../file';
import { TagList } from '../tag/tag-list';
import { FileList } from './file/file-list';
import { FileComponent } from './file/file-component';
import { BackendApi } from '../../utils/backend';
import { assertImported, querySelector, querySelectorAll } from '../../utils/importer';

class ContentPreviewFields implements Component<BackendFile[]> {
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

    public static from(previewModal: HTMLElement|null): ContentPreviewFields|null {
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

        return new ContentPreviewFields(
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

    public getValue(): BackendFile[] {
        return this.files.getValue();
    }

    public setValue(content: Content|null): void {
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
        this.files.setValue(content?.files.all() ?? []);
    }
}

export class ContentPreviewModal implements Component<Content|null> {
    private readonly contentPreviewModalElement: HTMLDivElement;
    private readonly contentPreviewFields: ContentPreviewFields;
    private readonly closeBtns: NodeListOf<HTMLButtonElement>;
    private readonly eventTarget: EventTarget = new EventTarget();
    private readonly backend: BackendApi = new BackendApi();

    private content: Content|null = null;

    public constructor(
        contentPreviewModalElement: HTMLDivElement,
        contentPreviewFields: ContentPreviewFields,
        closeBtns: NodeListOf<HTMLButtonElement>,
    ) {
        this.contentPreviewModalElement = contentPreviewModalElement;
        this.contentPreviewFields = contentPreviewFields;
        this.closeBtns = closeBtns;

        this.contentPreviewFields.files.onChange((): void => {
            this.closeBtns.forEach((closeBtn: HTMLButtonElement): void => {
                closeBtn.disabled = this.contentPreviewFields.files.isActive();
            });
        });

        this.contentPreviewFields.files.onRemoveFileComponent(async (fileComponent: FileComponent): Promise<void> => {
            if (!this.content) {
                return;
            }

            if (this.contentPreviewFields.files.length() > 1) {
                await fileComponent.removeFrom(this.content);

                return;
            }

            /**
             * an async function needs re-assignment to make sure content is still not null when processing
             */
            const content = this.content;

            const deletedContent = await MessageBox.confirmAsync(
                'The content has only one file',
                'Deleting the last file will also delete the content. Do you wish to delete this content?',
                'Deleting...',
                (): Promise<void> => this.backend.deleteContent(content),
            );

            if (deletedContent) {
                const deletedContent = new CustomEvent('content:preview:deleted-content', { detail: content });
                this.eventTarget.dispatchEvent(deletedContent);

                this.setValue(null);
                this.close();
            }
        });

        this.contentPreviewFields.files.onRemovedFile((file: BackendFile): void => {
            if (!this.content) {
                return;
            }

            this.content.files.delete(file);
            const deletedFile = new CustomEvent('content:preview:deleted-file', { detail: this.content });
            this.eventTarget.dispatchEvent(deletedFile);
        });

        this.closeBtns.forEach((closeBtn: HTMLButtonElement): void => {
            closeBtn.addEventListener('click', (): void => {
                this.close()
                this.setValue(null);
            });
        });

        /**
         * Automatically close modal if we click the backdrop
         */
        this.contentPreviewModalElement.addEventListener('click', (event: MouseEvent): void => {
            if ((event.target as HTMLElement) === this.contentPreviewModalElement && !this.contentPreviewFields.files.isActive()) {
                this.close();
                this.setValue(null);
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
        const contentPreviewFields = ContentPreviewFields.from(contentPreviewModalElement);
        const closeBtns = querySelectorAll<HTMLButtonElement>(contentPreviewModalElement, '[data-preview-content-close]');

        const required = {
            contentPreviewModalElement,
            contentPreviewFields,
            closeBtns,
        }

        if (!assertImported('content-preview-modal', required)) {
            return null;
        }

        return new ContentPreviewModal(
            required.contentPreviewModalElement,
            required.contentPreviewFields,
            required.closeBtns,
        );
    }

    public close(): void {
        this.contentPreviewModalElement.classList.remove('is-active');
    }

    public getValue(): Content|null {
        return this.content;
    }

    public onDeletedContent(listener: (content: Content) => void) {
        this.eventTarget.addEventListener('content:preview:deleted-content', (event: Event): void => {
            listener((event as CustomEvent<Content>).detail);
        });
    }

    public onDeletedFile(listener: (content: Content) => void) {
        this.eventTarget.addEventListener('content:preview:deleted-file', (event: Event): void => {
            listener((event as CustomEvent<Content>).detail);
        });
    }

    public open(): void {
        this.contentPreviewModalElement.classList.add('is-active');
    }

    public setValue(content: Content|null): void {
        this.content = content;
        this.contentPreviewFields.setValue(content);
    }
}
