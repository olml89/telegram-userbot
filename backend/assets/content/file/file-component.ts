import { BusyAware, ChangeAware, ErrorClearable, HtmlElementWrapper } from '../../common/component/contracts';
import { BaseComponent } from '../../common/component/base-component';
import { File as BackendFile, Size } from './file';
import { FileItem } from './file-item';
import { TusUploader, convertTusErrorToResponse } from './tus-uploader';
import { BackendError } from '../../common/backend-error';

export type UploadedFile = FileComponent & { backendFile: BackendFile };

export class FileComponent extends BaseComponent<BackendFile|null> implements BusyAware, ChangeAware, ErrorClearable, HtmlElementWrapper {
    private readonly file: File;
    private readonly fileItem: FileItem;
    private readonly cancelHandler: () => Promise<void>;
    private readonly uploader: TusUploader;
    private readonly eventTarget: EventTarget = new EventTarget();

    public backendFile: BackendFile|null = null;
    private isCanceled: boolean = false;
    private isFinalized: boolean = false;

    public constructor(file: File) {
        super();

        this.file = file;
        this.fileItem = new FileItem(this.file);

        this.cancelHandler = async(): Promise<void> => await this.cancelUpload();
        this.emit('file-item:cancel:register', this.cancelHandler);

        this.uploader = new TusUploader(this.file, {
            endpoint: `${window.location.origin}/tus/uploads`,
            chunkSize: 5 * 1024 * 1024,
            retryDelays: [],
            metadata: {
                filename: this.file.name,
                filetype: this.file.type || '',
            },
            onProgress: (bytesSent: number, bytesTotal: number): void => {
                if (this.isCanceled) {
                    return;
                }

                const uploadStartedAt = this.uploader.getUploadStartedAt();
                this.fileItem.updateProgress(bytesSent, bytesTotal, uploadStartedAt);
            },
            onError: async(error: unknown): Promise<void> => {
                if (this.isCanceled) {
                    return;
                }

                const response = await convertTusErrorToResponse(error);
                const parsedError = await BackendError.from(response, 'Failed to upload file');

                console.error(parsedError.consoleMessage);
                this.fileItem.setUploadErrorState(parsedError);

                this.emit('file-item:change');
                this.finalizeUpload();
            },
            onSuccess: async(): Promise<void> => {
                if (this.isCanceled) {
                    return;
                }

                this.fileItem.setSavingState();

                try {
                    this.backendFile = await this.saveBackendFile(this.uploader.getId());
                    this.fileItem.setUploadedState(this.backendFile);
                    this.emit('file-item:uploaded', this);
                } catch (e: any) {
                    const backendError = e as BackendError;
                    console.error(e.consoleMessage);
                    this.fileItem.setUploadErrorState(backendError);
                    this.emit('file-item:change');
                } finally {
                    this.finalizeUpload();
                }
            },
        });

        this.fileItem.onCancel(async(): Promise<void> => await this.cancelUpload());

        this.fileItem.onRetry((): void => {
            this.clearErrors();
            void this.startUpload();
        });

        this.fileItem.onRemove(async(): Promise<void> => {
            this.clearErrors();
            this.fileItem.setDeletingState();

            /**
             * If the API file doesn't exist, just remove the file element in the UI and DO NOT touch tusd
             */
            if (!this.isUploaded()) {
                this.finalizeUpload();
                this.removeItem();

                return;
            }

            this.emit('file-item:delete:begin');

            try {
                await this.removeBackendFile();
                this.removeItem();
            } catch (e: any) {
                const backendError = e as BackendError;
                console.error(backendError.consoleMessage);
                this.fileItem.setDeleteRetryState(backendError);
                this.finalizeUpload();
            } finally {
                this.emit('file-item:delete:end');
            }
        });
    }

    private async cancelUpload(): Promise<void> {
        if (this.isUploaded()) {
            return;
        }

        this.isCanceled = true;

        try {
            await this.uploader.remove();
        } catch (e: any) {
            const response = await convertTusErrorToResponse(e);
            const parsedError = await BackendError.from(
                response,
                'Failed to delete file remains after upload cancellation',
            );
            console.error(parsedError.consoleMessage);
        } finally {
            this.finalizeUpload();
            this.removeItem();
        }
    }

    public clearErrors(): void {
        this.errorHandler.clearErrors();
        this.fileItem.clearErrors();
    }

    public override getValue(): BackendFile|null {
        return this.backendFile;
    }

    public element(): HTMLElement {
        return this.fileItem.element();
    }

    private emit<T>(name: string, detail?: T): void {
        this.eventTarget.dispatchEvent(new CustomEvent(name, { detail }));
    }

    private finalizeUpload(): void {
        if (this.isFinalized) {
            return;
        }

        this.isFinalized = true;
        this.emit('file-item:upload:end');
    }

    public isUploaded(): this is UploadedFile {
        return this.backendFile !== null;
    }

    public onChange(listener: () => void): void {
        this.eventTarget.addEventListener('file-item:change', (): void => listener());
    }

    public onCancelRegister(listener: (handler: () => void) => void): void {
        this.eventTarget.addEventListener('file-item:cancel:register', (event: Event): void => {
            listener((event as CustomEvent<() => void>).detail);
        });
    }

    public onCancelUnregister(listener: (handler: () => void) => void): void {
        this.eventTarget.addEventListener('file-item:cancel:unregister', (event: Event): void => {
            listener((event as CustomEvent<() => void>).detail);
        });
    }

    public onDeleteBegin(listener: () => void): void {
        this.eventTarget.addEventListener('file-item:delete:begin', (): void => listener());
    }

    public onDeleteEnd(listener: () => void): void {
        this.eventTarget.addEventListener('file-item:delete:end', (): void => listener());
    }

    public onRemoved(listener: (fileComponent: FileComponent) => void): void {
        this.eventTarget.addEventListener('file-item:removed', (event: Event): void => {
            listener((event as CustomEvent<FileComponent>).detail);
        });
    }

    public onUploadBegin(listener: () => void): void {
        this.eventTarget.addEventListener('file-item:upload:begin', (): void => listener());
    }

    public onUploadEnd(listener: () => void): void {
        this.eventTarget.addEventListener('file-item:upload:end', (): void => listener());
    }

    public onUploaded(listener: (uploadedFile: UploadedFile) => void): void {
        this.eventTarget.addEventListener('file-item:uploaded', (event: Event): void => {
            if (!this.isUploaded()) {
                return;
            }

            listener((event as CustomEvent<UploadedFile>).detail);
        });
    }

    private async removeBackendFile(): Promise<void> {
        if (this.backendFile === null) {
            return;
        }

        const response = await fetch(`/api/files/${this.backendFile.publicId}`, {
            method: 'DELETE',
        });

        if (!response.ok) {
            throw await BackendError.from(
                response,
                'Failed to delete file',
            );
        }
    }

    private removeItem(): void {
        this.emit('file-item:removed', this);
        this.emit('file-item:cancel:unregister', this.cancelHandler);
    }

    private async saveBackendFile(uploadId: string): Promise<BackendFile> {
        const response = await fetch('/api/files', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                uploadId,
            }),
        });

        if (!response.ok) {
            throw await BackendError.from(
                response,
                'Failed to save file',
            );
        }

        const payload = await response.json();

        return {
            ...payload,
            bytes: new Size(payload.bytes),
        } as BackendFile;
    }

    public setBusy(isBusy: boolean): void {
        this.fileItem.setBusy(isBusy);
    }

    public override setErrors(...errorMessages: string[]): void {
        super.setErrors(...errorMessages);

        this.fileItem.setErrors(...errorMessages);
    }

    public async startUpload(): Promise<void> {
        this.isCanceled = false;
        this.isFinalized = false;

        this.fileItem.setValidatingState();
        this.emit('file-item:upload:begin');

        try {
            await this.validate();
            this.fileItem.setUploadingState();
            this.uploader.start();
        } catch (e: any) {
            const backendError = e as BackendError;
            console.error(backendError.consoleMessage);
            this.fileItem.setUploadErrorState(backendError);
            this.emit('file-item:change');
            this.finalizeUpload();
        }
    }

    private async validate(): Promise<void> {
        const response = await fetch('/api/files/validation', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                originalName: this.file.name,
                mimeType: this.file.type || null,
                size: this.file.size,
            }),
        });

        if (!response.ok) {
            throw await BackendError.from(
                response,
                'Failed to validate file',
            );
        }
    }
}
