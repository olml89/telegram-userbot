import {BusyAware, ErrorClearable, HtmlElementWrapper} from '../../../components/contracts';
import { BaseComponent } from '../../../components/base-component';
import { File as BackendFile } from '../../file';
import { FileStatus } from './file-status';
import { FileSize } from './file-size';
import { BackendError } from '../../../models/backend-error';

class FileMetadata {
    protected readonly file: File;
    protected readonly thumbnail: HTMLDivElement;
    protected readonly metaLeft: HTMLDivElement;
    protected readonly metaRight: HTMLDivElement;
    private readonly sizeComponent: FileSize;

    public constructor(file: File) {
        this.file = file;
        this.sizeComponent = new FileSize(this.file);

        this.thumbnail = document.createElement('div');
        this.thumbnail.className = 'file-thumb';
        this.thumbnail.appendChild(this.buildThumbnailContent());

        this.metaLeft = document.createElement('div');
        this.metaLeft.className = 'file-meta';
        this.metaLeft.innerHTML = `
            <div class="file-title" title="${this.file.name}">${this.file.name}</div>
            <div class="file-row">
                <span class="file-label">Type:</span>
                <span class="file-value file-muted">${this.getEmoji()} ${this.file.type}</span>
            </div>
        `;

        this.metaRight = document.createElement('div');
        this.metaRight.className = 'file-meta';
        this.metaRight.appendChild(this.sizeComponent.element());
    }

    public static from(file: File): FileMetadata {
        if (file.type.startsWith('image/')) {
            return new ImageMetadata(file);
        }

        if (file.type.startsWith('video/')) {
            return new VideoMetadata(file);
        }

        if (file.type.startsWith('audio/')) {
            return new AudioMetadata(file);
        }

        if (file.type.startsWith('text/plain') || file.type.startsWith('application/pdf')) {
            return new ReadableMetadata(file);
        }

        return new FileMetadata(file);
    }

    public appendTo(parent: HTMLDivElement) {
        parent.appendChild(this.thumbnail);
        parent.appendChild(this.metaLeft);
        parent.appendChild(this.metaRight);
    }

    protected buildThumbnailContent(): HTMLDivElement {
        const placeholder = document.createElement('div');
        placeholder.className = 'file-placeholder';
        placeholder.textContent = this.getEmoji();

        return placeholder;
    }

    protected getEmoji(): string {
        return '📦';
    }

    public getSizeComponent(): FileSize {
        return this.sizeComponent;
    }
}

class ReadableMetadata extends FileMetadata {
    public constructor(file: File) {
        super(file);

        this.thumbnail.classList.add('is-clickable');

        this.thumbnail.addEventListener('click', (): void => {
            const objectUrl = URL.createObjectURL(this.file);
            window.open(objectUrl, '_blank');
            URL.revokeObjectURL(objectUrl);
        })
    }

    protected override getEmoji(): string {
        return '📄';
    }
}

abstract class ObjectMetadata extends ReadableMetadata {
    protected loadFileMetadata(): void {
        const objectUrl = URL.createObjectURL(this.file);
        this.loadObjectMetadata(objectUrl);
    }

    protected abstract loadObjectMetadata(objectUrl: string): void;
}

class ThumbnailImage {
    private thumbnailImage: HTMLImageElement;

    public constructor(thumbnail: HTMLDivElement) {
        this.thumbnailImage = thumbnail.querySelector<HTMLImageElement>('img') as HTMLImageElement;
    }

    public set (src: string, alt: string): void {
        this.thumbnailImage.src = src;
        this.thumbnailImage.alt = alt;
    }
}

class Resolution {
    private resolution: HTMLDivElement;

    public constructor(metaLeft: HTMLDivElement) {
        const resolutionRow = document.createElement('div');
        resolutionRow.className = 'file-row';
        resolutionRow.innerHTML = `
                <span class="file-label">Resolution:</span>
                <span class="file-value file-muted" data-resolution>Loading…</span>
            `;
        metaLeft.appendChild(resolutionRow);
        this.resolution = resolutionRow.querySelector<HTMLDivElement>('[data-resolution]') as HTMLDivElement;
    }

    public set(width: number, height: number): void {
        this.resolution.textContent = `${width}×${height}`;
    }
}

class Length {
    private length: HTMLDivElement;

    public constructor(metaRight: HTMLDivElement) {
        const lengthRow = document.createElement('div');
        lengthRow.className = 'file-row';
        lengthRow.innerHTML = `
                <span class="file-label">Length:</span>
                <span class="file-value" data-length>Loading…</span>
            `;
        metaRight.appendChild(lengthRow);
        this.length = lengthRow.querySelector<HTMLDivElement>('[data-length]') as HTMLDivElement;
    }

    private format(seconds: number): string {
        if (seconds < 60) {
            return `${Math.round(seconds)}s`;
        }

        if (seconds < 3600) {
            const minutes = Math.floor(seconds / 60);
            seconds = Math.floor(seconds % 60);

            return `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }

        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        seconds = Math.floor(seconds % 60);

        return `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }

    public set(seconds: number): void {
        this.length.textContent = this.format(seconds);
    }
}

class ImageMetadata extends ObjectMetadata {
    private thumbnailImage: ThumbnailImage;
    private resolution: Resolution;

    public constructor(file: File) {
        super(file);

        this.thumbnailImage = new ThumbnailImage(this.thumbnail);
        this.resolution = new Resolution(this.metaLeft);
        this.loadFileMetadata();
    }

    protected override buildThumbnailContent(): HTMLImageElement {
        return document.createElement('img');
    }

    protected override getEmoji(): string {
        return '🖼️';
    }

    protected loadObjectMetadata(objectUrl: string): void {
        /**
         * Dynamically assign the src from the URL blob and the name from the file name
         * once we have objectUrl calculated.
         */
        this.thumbnailImage.set(objectUrl, this.file.name);
        const image = new Image();

        image.onload = () => {
            this.resolution.set(image.width, image.height);
            URL.revokeObjectURL(objectUrl);
        };

        /**
         * Load the Image object to fire the onload event so we can read its resolution.
         */
        image.src = objectUrl;
    }
}

class VideoMetadata extends ObjectMetadata {
    private thumbnailImage: ThumbnailImage;
    private resolution: Resolution;
    private length: Length;

    public constructor(file: File) {
        super(file);

        this.thumbnailImage = new ThumbnailImage(this.thumbnail);
        this.resolution = new Resolution(this.metaLeft);
        this.length = new Length(this.metaRight);
        this.loadFileMetadata();
    }

    protected override buildThumbnailContent(): HTMLImageElement {
        return document.createElement('img');
    }

    protected override getEmoji(): string {
        return '📹';
    }

    protected loadObjectMetadata(objectUrl: string): void {
        const video = document.createElement('video');
        video.preload = 'metadata';
        video.muted = true;
        video.playsInline = true;

        video.onloadedmetadata = () => {
            this.resolution.set(video.videoWidth, video.videoHeight);
            this.length.set(video.duration);
            video.currentTime = Math.min(0.1, video.duration || 0.1);
        };

        video.onseeked = () => {
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth || 320;
            canvas.height = video.videoHeight || 180;

            const ctx = canvas.getContext('2d');

            if (ctx) {
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                this.thumbnailImage.set(canvas.toDataURL('image/jpeg'), 'Video thumbnail');
            }

            URL.revokeObjectURL(objectUrl);
        };

        /**
         * Load the Video object to fire the onloadedmetadata event so we can read its resolution and length
         * and the onseeked event so we can draw the thumbnail.
         */
        video.src = objectUrl;
    }
}

class AudioMetadata extends ObjectMetadata {
    private length: Length;

    public constructor(file: File) {
        super(file);

        this.length = new Length(this.metaRight);
        this.loadFileMetadata();
    }

    protected override getEmoji(): string {
        return '🎵';
    }

    protected loadObjectMetadata(objectUrl: string): void {
        const audio = document.createElement('audio');
        audio.preload = 'metadata';

        audio.onloadedmetadata = () => {
            this.length.set(audio.duration);
            URL.revokeObjectURL(objectUrl);
        };

        /**
         * Load the Audio object to fire the onloadedmetadata event so we can read its length.
         */
        audio.src = objectUrl;
    }
}

export class FileItem extends BaseComponent<File> implements BusyAware, ErrorClearable, HtmlElementWrapper {
    private readonly file: File;
    private readonly fileElement: HTMLDivElement;
    private readonly fileSize: FileSize;
    private readonly fileStatus: FileStatus;

    private readonly cancelBtn: HTMLButtonElement;
    private readonly retryBtn: HTMLButtonElement;
    private readonly removeBtn: HTMLButtonElement;

    public constructor(file: File) {
        super();

        this.file = file;
        this.fileElement = document.createElement('div');
        this.fileElement.className = 'file-item';

        const metadata = FileMetadata.from(this.file);
        metadata.appendTo(this.fileElement);
        this.fileSize = metadata.getSizeComponent();

        const actions = document.createElement('div');
        actions.className = 'file-actions';
        actions.innerHTML = `
            <button type="button" class="btn btn-secondary btn-hover-outline btn-sm" data-cancel-file hidden>✖ Cancel</button>
            <button type="button" class="btn btn-secondary btn-hover-outline btn-sm" data-retry-file hidden>🔄 Retry</button>
            <button type="button" class="btn btn-secondary btn-hover-outline btn-sm" data-remove-file hidden>🗑️ Remove</button>    
        `;

        this.fileElement.appendChild(actions);

        this.cancelBtn = this.fileElement.querySelector<HTMLButtonElement>('[data-cancel-file]') as HTMLButtonElement;
        this.retryBtn = this.fileElement.querySelector<HTMLButtonElement>('[data-retry-file]') as HTMLButtonElement;
        this.removeBtn = this.fileElement.querySelector<HTMLButtonElement>('[data-remove-file]') as HTMLButtonElement;
        this.fileStatus = new FileStatus(this.fileElement);
    }

    public clearErrors(): void {
        this.fileElement.classList.remove('is-error', 'is-warning');
        this.fileStatus.clearErrors();
    }

    public element(): HTMLDivElement {
        return this.fileElement;
    }

    public override getValue(): File {
        return this.file;
    }

    public onCancel(handler: () => void): void {
        this.cancelBtn.addEventListener('click', handler);
    }

    public onRemove(handler: () => void): void {
        this.removeBtn.addEventListener('click', handler);
    }

    public onRetry(handler: () => void): void {
        this.retryBtn.addEventListener('click', handler);
    }

    private resetActions(): void {
        this.cancelBtn.hidden = true;
        this.retryBtn.hidden = true;
        this.removeBtn.hidden = true;

        this.cancelBtn.disabled = false;
        this.retryBtn.disabled = false;
        this.removeBtn.disabled = false;

        this.removeBtn.textContent = '🗑️ Remove';
    }

    public setBusy(isBusy: boolean) {
        this.fileElement.classList.toggle('is-disabled', isBusy);
        this.cancelBtn.disabled = isBusy;
        this.retryBtn.disabled = isBusy;
        this.removeBtn.disabled = isBusy;
    }

    public setDeletingState(): void {
        this.resetActions();
        this.fileStatus.showMessage('Deleting...');
    }

    public setDeleteRetryState(deleteError: BackendError): void {
        this.fileElement.classList.remove('is-error');
        this.fileElement.classList.add('is-warning');
        this.resetActions();
        this.removeBtn.hidden = false;
        this.removeBtn.textContent =  '🗑️ Remove';
        this.fileStatus.setWarnings(...deleteError.formatErrors());
    }

    public override setErrors(...errorMessages: string[]): void {
        this.fileElement.classList.remove('is-warning');
        this.fileElement.classList.add('is-error');
        this.fileStatus.setErrors(...errorMessages);
    }

    public setSavingState(): void {
        this.resetActions();
        this.fileStatus.showMessage('Saving file...');
    }

    public setUploadedState(backendFile: BackendFile): void {
        this.fileElement.classList.remove('is-error', 'is-warning');
        this.fileSize.update(backendFile.bytes);
        this.fileStatus.hide();
        this.showUploadedActions(true);
    }

    public setUploadingState(): void {
        this.resetActions();
        this.cancelBtn.hidden = false;
        this.fileStatus.showMessage('Uploading file...', true);
    }

    public setUploadErrorState(backendError: BackendError): void {
        this.fileElement.classList.remove('is-warning');
        this.fileElement.classList.add('is-error');
        this.resetActions();

        this.removeBtn.hidden = false;
        this.removeBtn.textContent = '🗑️ Remove';

        if (!backendError.isValidationError()) {
            this.retryBtn.hidden = false;
        }

        this.fileStatus.setErrors(...backendError.formatErrors());
    }

    public setValidatingState(): void {
        this.resetActions();
        this.cancelBtn.hidden = false;
        this.fileStatus.showMessage('Validating file...');
    }

    private showUploadedActions(showRemove: boolean): void {
        this.resetActions();

        if (showRemove) {
            this.removeBtn.hidden = false;
        }
    }

    public updateProgress(bytesSent: number, bytesTotal: number, uploadStartedAt: number): void {
        this.fileStatus.showProgress(bytesSent, bytesTotal, uploadStartedAt);
    }
}
