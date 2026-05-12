import { BusyAware, ChangeAware, ErrorClearable } from '../../../components/contracts';
import { Size } from '../../file';
import { BaseComponent } from '../../../components/base-component';
import { type UploadedFileComponent } from './file-component';
import { assertImported } from '../../../utils/importer';
import { pluralize } from '../../../utils/strings';

export class FileCount extends BaseComponent<UploadedFileComponent[]>implements BusyAware, ChangeAware, ErrorClearable {
    private readonly fileInput: HTMLInputElement;
    private readonly uploadCard: HTMLDivElement;
    private readonly uploadCount: HTMLDivElement;
    private readonly totalSize: HTMLDivElement;
    private readonly uploadedFileComponents: Map<string, UploadedFileComponent> = new Map();
    private readonly eventTarget: EventTarget = new EventTarget();

    public constructor(
        fileInput: HTMLInputElement,
        uploadCard: HTMLDivElement,
        uploadCount: HTMLDivElement,
        totalSize: HTMLDivElement,
    ) {
        super();

        this.fileInput = fileInput;
        this.uploadCard = uploadCard;
        this.uploadCount = uploadCount;
        this.totalSize = totalSize;

        this.uploadCard.addEventListener('dragover', (event: DragEvent): void => {
            event.preventDefault();
            this.uploadCard.classList.add('is-dragover');
        });

        this.uploadCard.addEventListener('dragleave', (): void => this.uploadCard.classList.remove('is-dragover'));
        this.uploadCard.addEventListener('click', (): void => this.fileInput.click());

        this.uploadCard.addEventListener('drop', (event: DragEvent): void => {
            event.preventDefault();
            this.uploadCard.classList.remove('is-dragover');
            const files = Array.from(event.dataTransfer?.files ?? []);
            this.eventTarget.dispatchEvent(new CustomEvent('file:added', { detail: files }));
        });

        this.fileInput.addEventListener('change', (): void => {
            const files = Array.from(this.fileInput.files ?? []);
            this.eventTarget.dispatchEvent(new CustomEvent('file:added', { detail: files }));
            this.fileInput.value = '';
        });
    }

    public static from(
        fileInput: HTMLInputElement|null,
        uploadCard: HTMLDivElement|null,
        uploadCount: HTMLDivElement|null,
        totalSize: HTMLDivElement|null,
    ): FileCount|null {
        const required = {
            fileInput,
            uploadCard,
            uploadCount,
            totalSize,
        };

        if (!assertImported('file-count', required)) {
            return null;
        }

        return new FileCount(
            required.fileInput,
            required.uploadCard,
            required.uploadCount,
            required.totalSize,
        );
    }

    public add(uploadedFileComponent: UploadedFileComponent): void {
        if (this.uploadedFileComponents.has(uploadedFileComponent.backendFile.publicId)) {
            return;
        }

        this.uploadedFileComponents.set(uploadedFileComponent.backendFile.publicId, uploadedFileComponent);
        this.update();
    }

    /**
     * There's no way to clear the errors of a fileComponent once it is errored,
     * the only way is to let the user retry or delete them.
     */
    public clearErrors(): void {
        this.uploadCard.classList.remove('is-error');
    }

    public override destroy() {
        this
            .uploadedFileComponents
            .forEach((uploadedFileComponent: UploadedFileComponent): void => this.remove(uploadedFileComponent));
    }

    public onAddedFiles(listener: (files: File[]) => void): void {
        this.eventTarget.addEventListener('file:added', (event: Event): void => {
            listener((event as CustomEvent<File[]>).detail);
        });
    }

    public onChange(listener: () => void): void {
        this.eventTarget.addEventListener('file-count:change', listener);
    }

    public override setErrors(): void {
        this.uploadCard.classList.add('is-error');
    }

    public get(publicId: string): UploadedFileComponent|null {
        return this.uploadedFileComponents.get(publicId) ?? null;
    }

    public override getValue(): UploadedFileComponent[] {
        return Array.from(this.uploadedFileComponents.values());
    }

    public remove(uploadedFileComponent: UploadedFileComponent): void {
        if (!this.uploadedFileComponents.has(uploadedFileComponent.backendFile.publicId)) {
            return;
        }

        this.uploadedFileComponents.delete(uploadedFileComponent.backendFile.publicId);
        this.update();
    }

    public setBusy(isBusy: boolean): void {
        this.uploadCard.classList.toggle('is-disabled', isBusy);
    }

    private update(): void {
        const uploadedFiles = this.getValue();
        const count = uploadedFiles.length;
        this.uploadCount.textContent = `${count} ${pluralize('file', count)} uploaded`;

        const totalSize = Array
            .from(uploadedFiles)
            .reduce((carry: Size, uploadedFileComponent: UploadedFileComponent) => carry.add(uploadedFileComponent.backendFile.size), new Size());

        this.totalSize.textContent = `Total size: ${totalSize.format()}`;
        this.eventTarget.dispatchEvent(new Event('file-count:change'));
    }
}
