import { Entity } from '../common/models/entity';

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

export type File = Entity & {
    fileName: string;
    originalName: string;
    mimeType: string;
    bytes: Size;
    hasThumbnail: boolean;
    width?: number;
    height?: number;
    duration?: number;
    createdAt: string;
    updatedAt: string;
};
