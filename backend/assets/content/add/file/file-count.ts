import { BusyAware, ChangeAware, ErrorClearable } from '../../../common/component/contracts';
import { Size } from '../../file';
import { BaseComponent } from '../../../common/component/base-component';
import { type UploadedFile } from './file-component';
import { assertImported } from '../../../common/importer';
import { pluralize } from '../../../common/strings';

export class FileCount extends BaseComponent<UploadedFile[]>implements BusyAware, ChangeAware, ErrorClearable {
    private readonly fileInput: HTMLInputElement;
    private readonly uploadCard: HTMLDivElement;
    private readonly uploadCount: HTMLDivElement;
    private readonly totalSize: HTMLDivElement;
    private readonly uploadedFiles: Map<string, UploadedFile> = new Map<string, UploadedFile>();
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

    public addUploadedFile(uploadedFile: UploadedFile): void {
        if (this.uploadedFiles.has(uploadedFile.backendFile.publicId)) {
            return;
        }

        this.uploadedFiles.set(uploadedFile.backendFile.publicId, uploadedFile);
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
        this.uploadedFiles.forEach((uploadedFile: UploadedFile): void => this.removeUploadedFile(uploadedFile));
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

    public getUploadedFile(publicId: string): UploadedFile|null {
        return this.uploadedFiles.get(publicId) ?? null;
    }

    public override getValue(): UploadedFile[] {
        return Array.from(this.uploadedFiles.values());
    }

    public removeUploadedFile(uploadedFile: UploadedFile): void {
        if (!this.uploadedFiles.has(uploadedFile.backendFile.publicId)) {
            return;
        }

        this.uploadedFiles.delete(uploadedFile.backendFile.publicId);
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
            .reduce((carry: Size, uploadedFile: UploadedFile) => carry.add(uploadedFile.backendFile.bytes), new Size());

        this.totalSize.textContent = `Total size: ${totalSize.format()}`;
        this.eventTarget.dispatchEvent(new Event('file-count:change'));
    }
}
