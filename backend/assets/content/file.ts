import { Entity, Payload } from '../models/entity';

export class Size {
    private bytes: number;

    public constructor(bytes: number = 0) {
        this.bytes = bytes;
    }

    public add(size: Size): this {
        this.bytes += size.get();

        return this;
    }

    public get(): number {
        return this.bytes;
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

type FilePayload = Payload & {
    fileName: string;
    originalName: string;
    mimeType: string;
    bytes: number;
    hasThumbnail: boolean;
    createdAt: string;
    updatedAt: string;
    width?: number;
    height?: number;
    duration?: number;
}

export class File extends Entity {
    public readonly fileName: string;
    public readonly originalName: string;
    public readonly mimeType: string;
    public readonly bytes: Size;
    public readonly hasThumbnail: boolean;
    public readonly createdAt: string;
    public readonly updatedAt: string;
    public readonly width?: number|undefined;
    public readonly height?: number|undefined;
    public readonly duration?: number|undefined;

    public constructor(
        publicId: string,
        fileName: string,
        originalName: string,
        mimeType: string,
        bytes: Size,
        hasThumbnail: boolean,
        createdAt: string,
        updatedAt: string,
        width?: number|undefined,
        height?: number|undefined,
        duration?: number|undefined,
    ) {
        super(publicId);

        this.fileName = fileName;
        this.originalName = originalName;
        this.mimeType = mimeType;
        this.bytes = bytes;
        this.hasThumbnail = hasThumbnail;
        this.createdAt = createdAt;
        this.updatedAt = updatedAt;
        this.width = width;
        this.height = height;
        this.duration = duration;
    }

    public static from(payload: FilePayload): File {
        return new File(
            payload.publicId,
            payload.fileName,
            payload.originalName,
            payload.mimeType,
            new Size(payload.bytes),
            payload.hasThumbnail,
            payload.createdAt,
            payload.updatedAt,
            payload?.width,
            payload?.height,
            payload?.duration,
        )
    }

    public override equals(other: File): boolean {
        return super.equals(other);
    }
}

