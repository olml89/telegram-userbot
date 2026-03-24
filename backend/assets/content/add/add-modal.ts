import { BusyAware, ChangeAware, Component, ErrorAware } from '../../common/component/contracts';
import { ValidatableComponent } from '../../common/component/validatable-component';
import { TextInput } from '../../common/component/text-input';
import { ValidatableCategorySelect } from './category-select';
import { ValidatableLanguageSelect } from './language-select';
import { ValidatableStatusSelect } from './status-select';
import { ValidatableModeSelect } from './mode-select';
import { PriceInput } from './price-input';
import { IntensityInput } from './intensity-input';
import { Tag } from '../tag';
import { TagsComponent } from './tag/tags-component';
import { File as BackendFile } from '../file';
import { FilesComponent } from './file/files-component';
import { Content } from '../content';
import { BackendError } from '../../common/backend-error';
import { assertImported, querySelector, querySelectorAll } from '../../common/importer';

type ContentFieldKey = keyof ContentFields;
type ContentFieldComponent = ValidatableComponent&BusyAware;
type ContentFieldValue = ReturnType<ContentFieldComponent['getValue']>;

class ContentFields implements BusyAware, ChangeAware, Component<Record<string, ContentFieldValue>>, ErrorAware {
    private readonly title: TextInput;
    private readonly description: TextInput;
    private readonly category: ValidatableCategorySelect;
    private readonly language: ValidatableLanguageSelect;
    private readonly status: ValidatableStatusSelect;
    private readonly mode: ValidatableModeSelect;
    private readonly price: PriceInput;
    private readonly intensity: IntensityInput;
    private readonly tags: TagsComponent;
    public readonly files: FilesComponent;

    public constructor(
        title: TextInput,
        description: TextInput,
        category: ValidatableCategorySelect,
        language: ValidatableLanguageSelect,
        status: ValidatableStatusSelect,
        mode: ValidatableModeSelect,
        price: PriceInput,
        intensity: IntensityInput,
        tags: TagsComponent,
        files: FilesComponent,
    ) {
        this.title = title;
        this.description = description;
        this.category = category;
        this.language = language;
        this.status = status;
        this.mode = mode;
        this.price = price;
        this.intensity = intensity;
        this.tags = tags;
        this.files = files;
    }

    public static from(addModal: HTMLElement|null): ContentFields|null {
        const title = TextInput.from(
            querySelector<HTMLLabelElement>(addModal, '[data-content-title]'),
        );
        const description = TextInput.from(
            querySelector<HTMLLabelElement>(addModal, '[data-content-description]'),
        );
        const category = ValidatableCategorySelect.from(
            querySelector<HTMLLabelElement>(addModal, '[data-content-category]'),
        );
        const language = ValidatableLanguageSelect.from(
            querySelector<HTMLLabelElement>(addModal, '[data-content-language]'),
        );
        const status = ValidatableStatusSelect.from(
            querySelector<HTMLLabelElement>(addModal, '[data-content-status]'),
        );
        const mode = ValidatableModeSelect.from(
            querySelector<HTMLLabelElement>(addModal, '[data-content-mode]'),
        );
        const price = PriceInput.from(
            mode,
            querySelector<HTMLLabelElement>(addModal, '[data-content-price]'),
        );
        const intensity = IntensityInput.from(
            querySelector<HTMLLabelElement>(addModal, '[data-content-intensity]'),
        );
        const tags = TagsComponent.from(
            querySelector<HTMLDivElement>(addModal, '[data-content-tags]'),
        );
        const files = FilesComponent.from(
            querySelector<HTMLDivElement>(addModal, '[data-content-files]'),
        );

        const required = {
            addModal,
            title,
            description,
            price,
            intensity,
            category,
            language,
            status,
            mode,
            tags,
            files,
        };

        if (!assertImported('content-fields', required)) {
            return null;
        }

        return new ContentFields(
            required.title,
            required.description,
            required.category,
            required.language,
            required.status,
            required.mode,
            required.price,
            required.intensity,
            required.tags,
            required.files,
        );
    }

    private components(): ContentFieldComponent[] {
        return Object.values(this) as ContentFieldComponent[];
    }

    public destroy(): void {
        this.components().forEach((component: ContentFieldComponent): void => component.destroy());
    }

    private get<K extends ContentFieldKey>(name: K): ContentFieldComponent {
        return this[name] as ContentFieldComponent;
    }

    public getValue(): Record<string, ContentFieldValue> {
        return {
            title: this.title.getValue(),
            description: this.description.getValue(),
            categoryId: this.category.getValue()?.publicId,
            language: this.language.getValue()?.value,
            status: this.status.getValue()?.value,
            mode: this.mode.getValue()?.value,
            price: this.price.getValue(),
            intensity: this.intensity.getValue(),
            tagIds: this.tags.getValue().map((tag: Tag): string => tag.publicId),
            fileIds: this.files.getValue().map((file: BackendFile): string => file.publicId),
        };
    }

    public hasErrors(): boolean {
        return this.components().some((component: ContentFieldComponent): boolean => component.hasErrors());
    }

    public onChange(listener: () => void): void {
        this.components().forEach((component: ContentFieldComponent): void => component.onChange(listener));
    }

    public setBusy(isBusy: boolean): void {
        this.components().forEach((component: ContentFieldComponent): void => component.setBusy(isBusy));
    }

    private setValidationError(fieldKey: string, message: string): void {
        const isContentFieldKey = (key: string): key is keyof ContentFields => key in this;

        if (!isContentFieldKey(fieldKey)) {
            return;
        }

        const component = this.get(fieldKey);
        component.setBackendErrors(message);
    }

    public setValidationErrors(backendErrors: Map<string, string[]>): void {
        this.components().forEach((component: ContentFieldComponent): void => component.clearErrors());

        backendErrors.forEach((errorMessages: string[], field: string): void => {
            const normalized = field.replace(/\[\d+]/g, '');

            Array.from(errorMessages).forEach((errorMessage: string) => {
                if (normalized === 'categoryId') {
                    const match = errorMessage.match(/^Category\s+([0-9a-f-]{36})\b/i);

                    if (match) {
                        const categoryId = match[1] as string;
                        const cleaned = errorMessage
                            .replace(categoryId, '')
                            .replace(/\s+/g, ' ')
                            .trim();
                        this.category.setBackendErrors(cleaned);

                        return;
                    }

                    this.category.setBackendErrors(errorMessage);

                    return;
                }

                if (normalized === 'tagIds') {
                    const match = errorMessage.match(/^Tag\s+([0-9a-f-]{36})\b/i);

                    if (match) {
                        const tagId = match[1] as string;
                        const cleaned = errorMessage
                            .replace(tagId, '')
                            .replace(/\s+/g, ' ')
                            .trim();
                        this.tags.setItemError(tagId, cleaned);

                        return;
                    }

                    this.tags.setBackendErrors(errorMessage);

                    return;
                }

                if (normalized === 'fileIds') {
                    const match = errorMessage.match(/^File\s+([0-9a-f-]{36})\b/i);

                    if (match) {
                        const fileId = match[1] as string;
                        const cleaned = errorMessage
                            .replace(fileId, '')
                            .replace(/\s+/g, ' ')
                            .trim();
                        this.files.setItemError(fileId, cleaned);

                        return;
                    }

                    this.files.setBackendErrors(errorMessage);

                    return;
                }

                this.setValidationError(normalized, errorMessage);
            });
        });
    }

    public validate(): boolean {
        return this
            .components()
            .map((component: ContentFieldComponent): boolean => component.validate())
            .every(Boolean);
    }
}

export class ContentAddModal implements Component<Content|null> {
    private readonly contentAddModalElement: HTMLDivElement;
    private readonly contentFields: ContentFields;
    private readonly formError: HTMLElement;
    private readonly addBtn: HTMLButtonElement;
    private readonly closeBtns: NodeListOf<HTMLButtonElement>;
    private readonly eventTarget: EventTarget = new EventTarget();

    private content: Content|null = null;

    public constructor(
        contentAddModalElement: HTMLDivElement,
        contentFields: ContentFields,
        formError: HTMLElement,
        addBtn: HTMLButtonElement,
        closeBtns: NodeListOf<HTMLButtonElement>,
    ) {
        this.contentAddModalElement = contentAddModalElement;
        this.contentFields = contentFields;
        this.formError = formError;
        this.addBtn = addBtn;
        this.closeBtns = closeBtns;

        this.setAddButtonDisabled(!this.contentFields.validate());

        this.contentFields.files.onChange((): void => {
            this.setAddButtonDisabled(this.contentFields.hasErrors());

            this.closeBtns.forEach((closeBtn: HTMLButtonElement): void => {
                closeBtn.disabled = this.contentFields.files.isActive();
            });
        });

        this.closeBtns.forEach((closeBtn: HTMLButtonElement): void => {
            closeBtn.addEventListener('click', (): void => this.close());
        });

        this.addBtn.addEventListener('click', async(): Promise<void> => this.submit());

        /**
         * Automatically close modal if we click the backdrop
         */
        this.contentAddModalElement.addEventListener('click', (event: MouseEvent): void => {
            if ((event.target as HTMLElement) === this.contentAddModalElement) {
               this.contentAddModalElement.classList.remove('active');
            }
        });
    }

    public static from(contentAddModalElement: HTMLDivElement|null): ContentAddModal|null {
        const formError = querySelector<HTMLDivElement>(contentAddModalElement, '[data-error-for="form"]');
        const contentFields = ContentFields.from(contentAddModalElement);
        const addBtn = querySelector<HTMLButtonElement>(contentAddModalElement, '[data-content-add]');
        const closeBtns = querySelectorAll<HTMLButtonElement>(contentAddModalElement, '[data-content-close]');

        const required = {
            contentAddModalElement,
            formError,
            contentFields,
            addBtn,
            closeBtns,
        }

        if (!assertImported('content-add-modal', required)) {
            return null;
        }

        return new ContentAddModal(
            required.contentAddModalElement,
            required.contentFields,
            required.formError,
            required.addBtn,
            required.closeBtns,
        );
    }

    private async add(contentFields: ContentFields): Promise<Content> {
        const response = await fetch('/api/content', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(contentFields.getValue()),
        });

        if (!response.ok) {
            throw await BackendError.from(
                response,
                'Failed to add content',
            );
        }

        return await response.json() as Content;
    }

    private clearFormError(): void {
        this.formError.textContent = '';
        this.formError.hidden = true;
    };

    public close(): void {
        this.contentAddModalElement.classList.remove('is-active');
    }

    public onAddedContent(listener: (content: Content) => void) {
        this.eventTarget.addEventListener('contents-component:add', (event: Event): void => {
            listener((event as CustomEvent<Content>).detail);
        });
    }

    public getValue(): Content|null {
        return this.content;
    }

    public open(): void {
        this.contentAddModalElement.classList.add('is-active');
    }

    private setAddButtonDisabled(hasErrors: boolean): void {
        this.addBtn.disabled = this.contentFields.files.isActive() || hasErrors;
    }

    private setValidationErrors(validationErrors: Map<string, string[]>): void {
        this.clearFormError();
        this.contentFields.setValidationErrors(validationErrors);
    };

    private setFormError(errorMessage: string): void {
        this.formError.textContent = errorMessage;
        this.formError.hidden = false;
    }

    private setSubmitLoading(isLoading: boolean, hasValidationErrors: boolean = false): void {
        this.setAddButtonDisabled(isLoading || hasValidationErrors);
        this.addBtn.classList.toggle('is-loading', isLoading);

        this.closeBtns.forEach((button: HTMLButtonElement): void => {
            button.disabled = isLoading;
        })

        this.contentAddModalElement.classList.toggle('is-busy', isLoading);
        this.contentFields.setBusy(isLoading);
    }

    private async submit(): Promise<void> {
        if (this.contentFields.hasErrors()) {
            return;
        }

        let hasValidationErrors = false;
        this.setSubmitLoading(true);

        try {
            const content = await this.add(this.contentFields);
            this.eventTarget.dispatchEvent(new CustomEvent('contents-component:add', { detail: content }));
            this.contentFields.destroy();
            this.setAddButtonDisabled(!this.contentFields.validate());
            this.close();
        } catch (e: any) {
            const backendError = e as BackendError;
            console.error(e.consoleMessage);
            const validationErrors = e.validationErrors;
            hasValidationErrors = validationErrors !== null;

            validationErrors !== null
                ? this.setValidationErrors(validationErrors)
                : this.setFormError(backendError.message);
        } finally {
            this.setSubmitLoading(false, hasValidationErrors);
        }
    }
}

