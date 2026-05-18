import { Entity, Payload } from '../models/entity';
import { Thumbnail } from './file-item/metadata/thumbnail';
import { Resolution} from './file-item/metadata/resolution';
import { Duration } from './file-item/metadata/duration';

export class Size {
    public bytes: number;

    public constructor(bytes: number = 0) {
        this.bytes = bytes;
    }

    public add(size: Size): this {
        this.bytes += size.bytes;

        return this;
    }

    public format(): string {
        const kb = this.bytes / 1024;
        const mb = kb / 1024;
        const gb = mb / 1024;

        if (mb < 1) {
            return `${Math.round(kb)} KB`;
        }

        if (mb < 1000) {
            return `${mb.toFixed(2)} MB`;
        }

        return `${gb.toFixed(2)} GB`;
    }

    public set(bytes: number): void {
        this.bytes = bytes;
    }
}

export type FilePayload = Payload & {
    fileName: string;
    originalName: string;
    mimeType: string;
    bytes: number;
    createdAt: string;
    updatedAt: string;
    width?: number;
    height?: number;
    duration?: number;
}

export enum FileType {
    File = 'file',
    Document = 'document',
    Image = 'image',
    Video = 'video',
    Audio = 'audio',
}

export namespace FileType {
    export function from(value: string): FileType {
        if (value.startsWith('text/plain') || value.startsWith('application/pdf')) {
            return FileType.Document;
        }

        if (value.startsWith('image/')) {
            return FileType.Image;
        }

        if (value.startsWith('video/')) {
            return FileType.Video;
        }

        if (value.startsWith('audio/')) {
            return FileType.Audio;
        }

        return FileType.File;
    }
}

export class File extends Entity {
    public readonly fileName: string;
    public readonly originalName: string;
    public readonly mimeType: string;
    public readonly size: Size;
    public readonly createdAt: string;
    public readonly updatedAt: string;

    public constructor(payload: FilePayload) {
        super(payload.publicId);

        this.fileName = payload.fileName;
        this.originalName = payload.originalName;
        this.mimeType = payload.mimeType;
        this.size = new Size(payload.bytes);
        this.createdAt = payload.createdAt;
        this.updatedAt = payload.updatedAt;
    }

    public static from(payload: FilePayload): File {
        const fileType = FileType.from(payload.mimeType);

        if (fileType === FileType.Document) {
            return new Document(payload);
        }

        if (fileType === FileType.Image) {
            if (payload.width === undefined || payload.height === undefined) {
                throw new Error('Image width and height are required');
            }

            return new Image(
                payload,
                Thumbnail.fromBackend(payload),
                new Resolution(payload.width, payload.height),
            );
        }

        if (fileType === FileType.Video) {
            if (payload.width === undefined || payload.height === undefined || payload.duration === undefined) {
                throw new Error('Audio width, height and duration are required');
            }

            return new Video(
                payload,
                Thumbnail.fromBackend(payload),
                new Resolution(payload.width, payload.height),
                new Duration(payload.duration),
            );
        }

        if (fileType === FileType.Audio) {
            if (payload.duration === undefined) {
                throw new Error('Audio duration is required');
            }

            return new Audio(
                payload,
                new Duration(payload.duration),
            );
        }

        return new File(payload);
    }

    public override equals(other: File): boolean {
        return super.equals(other);
    }

    public url(): string {
        return `/api/files/${this.publicId}`;
    }
}

export class Document extends File {}

export class Image extends File {
    public readonly thumbnail: Thumbnail;
    public readonly resolution: Resolution;

    public constructor(payload: FilePayload, thumbnail: Thumbnail, resolution: Resolution) {
        super(payload);

        this.thumbnail = thumbnail;
        this.resolution = resolution;
    }
}

export class Video extends File {
    public readonly thumbnail: Thumbnail;
    public readonly resolution: Resolution;
    public readonly duration: Duration;

    public constructor(payload: FilePayload, thumbnail: Thumbnail, resolution: Resolution, duration: Duration) {
        super(payload);

        this.thumbnail = thumbnail;
        this.resolution = resolution;
        this.duration = duration;
    }
}

export class Audio extends File {
    public readonly duration: Duration;

    public constructor(payload: FilePayload, duration: Duration) {
        super(payload);

        this.duration = duration;
    }
}

