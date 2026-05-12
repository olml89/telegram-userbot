import {
    Audio as BackendAudio,
    Document as BackendDocument,
    File as BackendFile,
    FileType,
    Image as BackendImage,
    Size,
    Video as BackendVideo
} from '../file';
import { Thumbnail, ThumbnailImage } from './metadata/thumbnail';
import { Resolution, ResolutionElement } from './metadata/resolution';
import { Duration, DurationElement } from './metadata/duration';
import {
    AudioEmojiProvider,
    DocumentEmojiProvider,
    EmojiProvider,
    FileEmojiProvider,
    ImageEmojiProvider,
    VideoEmojiProvider
} from './metadata/emoji';

export class FileAdapterFactory {
    public static from(file: File|BackendFile): FileAdapter {
        if (file instanceof File) {
            const fileType = FileType.from(file.type);

            switch (fileType) {
                case FileType.Document: return new LocalDocumentAdapter(file);
                case FileType.Image: return new LocalImageAdapter(file);
                case FileType.Video: return new LocalVideoAdapter(file);
                case FileType.Audio: return new LocalAudioAdapter(file);
                default: return new LocalFileAdapter(file);
            }
        }

        if (file instanceof BackendDocument) {
            return new BackendDocumentAdapter(file);
        }

        if (file instanceof BackendImage) {
            return new BackendImageAdapter(file);
        }

        if (file instanceof BackendVideo) {
            return new BackendVideoAdapter(file);
        }

        if (file instanceof BackendAudio) {
            return new BackendAudioAdapter(file);
        }

        return new BackendFileAdapter(file);
    }
}

export interface FileAdapter extends EmojiProvider {
    name(): string;
    type(): string;
    size(): Size;
    url(): string;
    metadata(): FileMetadata;
}

class LocalFileAdapter implements FileAdapter {
    protected readonly emojiProvider: EmojiProvider = new FileEmojiProvider();
    protected readonly fileMetadataFactory: MetadataFactory = new FileMetadataFactory();

    public constructor(
        protected readonly file: File,
    ) {}

    public name(): string {
        return this.file.name;
    }

    public emoji(): string {
        return this.emojiProvider.emoji();
    }

    public type(): string {
        return this.file.type;
    }

    public size(): Size {
        return new Size(this.file.size);
    }

    public url(): string {
        return URL.createObjectURL(this.file);
    }

    metadata(): FileMetadata {
        return this.fileMetadataFactory.build(this);
    }
}

class BackendFileAdapter<T extends BackendFile> implements FileAdapter {
    protected readonly emojiProvider: EmojiProvider = new FileEmojiProvider();
    protected readonly fileMetadataFactory: MetadataFactory = new FileMetadataFactory();

    public constructor (
        protected readonly file: T,
    ) {}

    public name(): string {
        return this.file.fileName;
    }

    public emoji(): string {
        return this.emojiProvider.emoji();
    }

    public type(): string {
        return this.file.mimeType;
    }

    public size(): Size {
        return this.file.size;
    }

    public url(): string {
        return this.file.url();
    }

    public metadata(): FileMetadata {
        return this.fileMetadataFactory.build(this);
    }
}

interface DocumentAdapter extends FileAdapter {}

class LocalDocumentAdapter extends LocalFileAdapter implements DocumentAdapter {
    protected override readonly emojiProvider: EmojiProvider = new DocumentEmojiProvider();
    protected override readonly fileMetadataFactory: MetadataFactory = new DocumentMetadataFactory();
}

class BackendDocumentAdapter extends BackendFileAdapter<BackendDocument> implements DocumentAdapter {
    protected override readonly emojiProvider: EmojiProvider = new DocumentEmojiProvider();
    protected override readonly fileMetadataFactory: MetadataFactory = new DocumentMetadataFactory();
}

interface ImageAdapter extends FileAdapter {
    loadMetadata(thumbnailImage: ThumbnailImage, resolutionElement: ResolutionElement): void;
}

class LocalImageAdapter extends LocalFileAdapter implements ImageAdapter {
    protected override readonly emojiProvider: EmojiProvider = new ImageEmojiProvider();
    protected override readonly fileMetadataFactory: MetadataFactory = new ImageMetadataFactory();

    public loadMetadata(thumbnailImage: ThumbnailImage, resolutionElement: ResolutionElement) {
        const url = this.url();
        const thumbnail = new Thumbnail(url, this.name());
        thumbnailImage.set(thumbnail);

        const image = new Image();

        image.onload = (): void => {
            const resolution = new Resolution(image.width, image.height);
            resolutionElement.set(resolution);
            URL.revokeObjectURL(url);
        };

        /**
         * Load the Image object to fire the onload event so we can read its resolution.
         */
        image.src = url;
    }
}

class BackendImageAdapter extends BackendFileAdapter<BackendImage> implements ImageAdapter {
    protected override readonly emojiProvider: EmojiProvider = new ImageEmojiProvider();
    protected override readonly fileMetadataFactory: MetadataFactory = new ImageMetadataFactory();

    public loadMetadata(thumbnailImage: ThumbnailImage, resolutionElement: ResolutionElement) {
        thumbnailImage.set(this.file.thumbnail);
        resolutionElement.set(this.file.resolution);
    }
}

interface VideoAdapter extends FileAdapter {
    loadMetadata(thumbnailImage: ThumbnailImage, resolutionElement: ResolutionElement, durationElement: DurationElement): void;
}

class LocalVideoAdapter extends LocalFileAdapter implements VideoAdapter {
    protected override readonly emojiProvider: EmojiProvider = new VideoEmojiProvider();
    protected override readonly fileMetadataFactory: MetadataFactory = new VideoMetadataFactory();

    public loadMetadata(thumbnailImage: ThumbnailImage, resolutionElement: ResolutionElement, durationElement: DurationElement): void {
        const url = this.url();
        const video = document.createElement('video');
        video.preload = 'metadata';
        video.muted = true;
        video.playsInline = true;

        video.onloadedmetadata = (): void => {
            const resolution = new Resolution(video.videoWidth, video.videoHeight);
            resolutionElement.set(resolution);

            const duration = new Duration(video.duration);
            durationElement.set(duration);

            video.currentTime = Math.min(0.1, video.duration || 0.1);
        };

        video.onseeked = (): void => {
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth || 320;
            canvas.height = video.videoHeight || 180;

            const ctx = canvas.getContext('2d');

            if (ctx) {
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                const thumbnail = new Thumbnail(canvas.toDataURL('image/jpeg'), 'Video thumbnail');
                thumbnailImage.set(thumbnail);
            }

            URL.revokeObjectURL(url);
        };

        /**
         * Load the Video object to fire the onloadedmetadata event so we can read its resolution and length
         * and the onseeked event so we can draw the thumbnail.
         */
        video.src = url;
    }
}

class BackendVideoAdapter extends BackendFileAdapter<BackendVideo> implements VideoAdapter {
    protected override readonly emojiProvider: EmojiProvider = new VideoEmojiProvider();
    protected override readonly fileMetadataFactory: MetadataFactory = new VideoMetadataFactory();

    public loadMetadata(thumbnailImage: ThumbnailImage, resolutionElement: ResolutionElement, durationElement: DurationElement): void {
        thumbnailImage.set(this.file.thumbnail);
        resolutionElement.set(this.file.resolution);
        durationElement.set(this.file.duration);
    }
}

interface AudioAdapter extends FileAdapter {
    loadMetadata(durationElement: DurationElement): void;
}

class LocalAudioAdapter extends LocalFileAdapter implements AudioAdapter {
    protected override readonly emojiProvider: EmojiProvider = new AudioEmojiProvider();
    protected override readonly fileMetadataFactory: MetadataFactory = new AudioMetadataFactory();

    public loadMetadata(durationElement: DurationElement): void {
        const url = this.url();
        const audio = document.createElement('audio');
        audio.preload = 'metadata';

        audio.onloadedmetadata = (): void => {
            const duration = new Duration(audio.duration);
            durationElement.set(duration);
            URL.revokeObjectURL(url);
        };

        /**
         * Load the Audio object to fire the onloadedmetadata event so we can read its length.
         */
        audio.src = url;
    }
}

class BackendAudioAdapter extends BackendFileAdapter<BackendAudio> implements AudioAdapter {
    protected override readonly emojiProvider: EmojiProvider = new AudioEmojiProvider();
    protected override readonly fileMetadataFactory: MetadataFactory = new AudioMetadataFactory();

    public loadMetadata(durationElement: DurationElement): void {
        durationElement.set(this.file.duration);
    }
}

interface MetadataFactory {
    build(fileAdapter: FileAdapter): FileMetadata;
}

class FileMetadataFactory implements MetadataFactory {
    public build(fileAdapter: FileAdapter): FileMetadata {
        return new FileMetadata(
            this.fileThumb(fileAdapter),
            this.metaLeft(fileAdapter),
            this.metaRight(fileAdapter),
        );
    }

    protected fileThumb(fileAdapter: FileAdapter): HTMLDivElement {
        const fileThumb = document.createElement('div');
        fileThumb.className = 'file-thumb';
        fileThumb.appendChild(this.thumbnailContent(fileAdapter));

        return fileThumb;
    }

    protected metaLeft(fileAdapter: FileAdapter): HTMLDivElement {
        const metaLeft = document.createElement('div');
        metaLeft.className = 'file-meta';
        metaLeft.innerHTML = `
            <div class="file-title" title="${fileAdapter.name()}">${fileAdapter.name()}</div>
            <div class="file-row">
                <span class="file-label">Type:</span>
                <span class="file-value file-muted">${fileAdapter.emoji()} ${fileAdapter.type()}</span>
            </div>
        `;

        return metaLeft;
    }

    protected metaRight(fileAdapter: FileAdapter): HTMLDivElement {
        const metaRight = document.createElement('div');
        metaRight.className = 'file-meta';
        metaRight.innerHTML = `
            <div class="file-row">
                <span class="file-label">Size:</span>
                <span class="file-value" data-size>${fileAdapter.size().format()}</span>
            </div>
        `;

        return metaRight;
    }

    protected thumbnailContent(fileAdapter: FileAdapter): HTMLDivElement {
        const placeholder = document.createElement('div');
        placeholder.className = 'file-placeholder';
        placeholder.textContent = fileAdapter.emoji();

        return placeholder;
    }
}

class DocumentMetadataFactory extends FileMetadataFactory {
    public override build(fileAdapter: FileAdapter): FileMetadata {
        const fileMetadata = super.build(fileAdapter);

        fileMetadata.fileThumb.classList.add('is-clickable');

        fileMetadata.fileThumb.addEventListener('click', (): void => {
            const objectUrl = fileAdapter.url();
            window.open(objectUrl, '_blank');
        });

        return fileMetadata;
    }
}

class ImageMetadataFactory extends DocumentMetadataFactory {
    public override build(fileAdapter: ImageAdapter): FileMetadata {
        const fileMetadata = super.build(fileAdapter);

        fileAdapter.loadMetadata(
            new ThumbnailImage(fileMetadata.fileThumb),
            new ResolutionElement(fileMetadata.metaLeft),
        );

        return fileMetadata;
    }

    protected override thumbnailContent(): HTMLImageElement {
        return document.createElement('img');
    }
}

class VideoMetadataFactory extends DocumentMetadataFactory {
    public override build(fileAdapter: VideoAdapter): FileMetadata {
        const fileMetadata = super.build(fileAdapter);

        fileAdapter.loadMetadata(
            new ThumbnailImage(fileMetadata.fileThumb),
            new ResolutionElement(fileMetadata.metaLeft),
            new DurationElement(fileMetadata.metaRight),
        );

        return fileMetadata;
    }

    protected override thumbnailContent(): HTMLImageElement {
        return document.createElement('img');
    }
}

class AudioMetadataFactory extends DocumentMetadataFactory {
    public override build(fileAdapter: AudioAdapter): FileMetadata {
        const fileMetadata = super.build(fileAdapter);
        fileAdapter.loadMetadata(new DurationElement(fileMetadata.metaRight));

        return fileMetadata;
    }
}

export class FileMetadata {
    public readonly fileThumb: HTMLDivElement;
    public readonly metaLeft: HTMLDivElement;
    public readonly metaRight: HTMLDivElement;
    private readonly sizeElement: HTMLSpanElement;

    public constructor(fileThumb: HTMLDivElement, metaLeft: HTMLDivElement, metaRight: HTMLDivElement) {
        this.fileThumb = fileThumb;
        this.metaLeft = metaLeft;
        this.metaRight = metaRight;
        this.sizeElement = this.metaRight.querySelector('[data-size]') as HTMLSpanElement;
    }

    public appendTo(fileItemElement: HTMLDivElement) {
        fileItemElement.appendChild(this.fileThumb);
        fileItemElement.appendChild(this.metaLeft);
        fileItemElement.appendChild(this.metaRight);
    }

    public updateSize(size: Size) {
        this.sizeElement.textContent = size.format();
    }
}
