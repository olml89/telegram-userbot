import { CollectionComponent } from '../../common/component/collection-component';
import { File as BackendFile } from './file';
import { FileCount } from './file-count';
import { FileHandler } from './file-handler';
import { FileComponent, UploadedFile } from './file-component';
import { assertImported, querySelector } from '../../common/importer';

export class FilesComponent extends CollectionComponent<BackendFile> {
    private readonly fileCount: FileCount;
    private readonly fileHandler: FileHandler;
    private readonly cancelHandlers: Set<() => void> = new Set<() => void>();

    private activeUploads: number = 0;
    private pendingDeletes: number = 0;

    public constructor(
        heading: HTMLHeadingElement,
        fileCount: FileCount,
        fileHandler: FileHandler,
        minCount: number|null,
        maxCount: number|null,
    ) {
        super('file', heading, minCount, maxCount);

        this.fileCount = fileCount;
        this.fileHandler = fileHandler;

        window.addEventListener('uploads:cancelAll', (): void => this.cancelAll());

        this.fileCount.onAddedFiles((files: File[]): void => files.forEach((file: File): void => this.handle(file)));
        this.fileCount.onChange((): void => this.notifyChange());
    }

    public static from(filesElement: HTMLDivElement|null): FilesComponent|null {
        const heading = querySelector<HTMLHeadingElement>(filesElement, '[data-error-for]');

        const fileCount = FileCount.from(
            querySelector<HTMLInputElement>(filesElement, '[data-file-input]'),
            querySelector<HTMLDivElement>(filesElement, '[data-upload-card]'),
            querySelector<HTMLDivElement>(filesElement, '[data-upload-count]'),
            querySelector<HTMLDivElement>(filesElement, '[data-total-size]'),
        );

        const fileHandler = FileHandler.from(
            querySelector<HTMLFormElement>(filesElement, '[data-file-list]'),
        );

        const required = {
            filesElement,
            heading,
            fileCount,
            fileHandler,
        }

        if (!assertImported('files-component', required)) {
            return null;
        }

        const minCountText = required.filesElement.getAttribute('data-min-count');
        const maxCountText = required.filesElement.getAttribute('data-max-count');

        return new FilesComponent(
            required.heading,
            required.fileCount,
            required.fileHandler,
            minCountText === null ? null : Number(minCountText),
            maxCountText === null ? null : Number(maxCountText),
        );
    }

    private beginDelete(): void {
        ++this.pendingDeletes;
        this.notifyChange();
    }

    private beginUpload(): void {
        ++this.activeUploads;
        this.notifyChange();
    }

    private cancelAll(): void {
        this.cancelHandlers.forEach((cancel: () => void): void => cancel());
    }

    public override clearErrors(): void {
        super.clearErrors();

        this.fileCount.clearErrors();
    }

    private createFileComponent(file: File): FileComponent {
        const fileComponent = new FileComponent(file);
        fileComponent.onCancelRegister((handler: () => void): void => this.registerCancelHandler(handler));
        fileComponent.onCancelUnregister((handler: () => void): void => this.unregisterCancelHandler(handler));
        fileComponent.onUploadBegin((): void => this.beginUpload());
        fileComponent.onUploadEnd((): void => this.endUpload());
        fileComponent.onDeleteBegin((): void => this.beginDelete());
        fileComponent.onDeleteEnd((): void => this.endDelete());
        fileComponent.onUploaded((uploadedFile: UploadedFile): void => this.fileCount.addUploadedFile(uploadedFile));
        fileComponent.onRemoved((fileComponent: FileComponent): void => this.removeFile(fileComponent));
        fileComponent.onChange((): void => this.notifyChange());

        return fileComponent;
    }

    public override destroy() {
        super.destroy();

        this.fileCount.destroy();
    }

    private endDelete(): void {
        this.pendingDeletes = Math.max(0, this.pendingDeletes - 1);
        this.notifyChange();
    }

    private endUpload(): void {
        this.activeUploads = Math.max(0, this.activeUploads - 1);
        this.notifyChange();
    }

    public override getValue(): BackendFile[] {
        return this.fileCount.getValue().map((uploadedFile: UploadedFile): BackendFile => uploadedFile.backendFile);
    }

    private handle(file: File): void {
        const fileComponent = this.createFileComponent(file);
        this.fileHandler.add(fileComponent);
    }

    public isActive(): boolean {
        return this.activeUploads > 0 || this.pendingDeletes > 0;
    }

    private registerCancelHandler(handler: () => void): void {
        this.cancelHandlers.add(handler);
    }

    private removeFile(fileComponent: FileComponent): void {
        if (fileComponent.isUploaded()) {
            this.fileCount.removeUploadedFile(fileComponent);
        }

        this.fileHandler.remove(fileComponent);
    }

    public override setBusy(isBusy: boolean): void {
        this.fileCount.setBusy(isBusy);
        this.fileHandler.setBusy(isBusy);
    }

    public override setErrors(...errorMessages: string[]) {
        super.setErrors(...errorMessages);

        this.fileCount.setErrors();
    }

    public setItemError(publicId: string, message: string): void {
        this.fileCount.getUploadedFile(publicId)?.setErrors(message);
    }

    private unregisterCancelHandler(handler: () => void): void {
        this.cancelHandlers.delete(handler);
    }
}
