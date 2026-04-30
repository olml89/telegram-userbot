import { FilePayload } from '../../../file';

export class Thumbnail {
    public constructor(public url: string, public alt: string) {}

    public static fromBackend(payload: FilePayload): Thumbnail {
        return new Thumbnail(
            `/api/files/${payload.publicId}/thumbnail`,
            payload.fileName,
        );
    }
}

export class ThumbnailImage {
    private element: HTMLImageElement;

    public constructor(fileThumb: HTMLDivElement) {
        this.element = fileThumb.querySelector<HTMLImageElement>('img') as HTMLImageElement;
    }

    public set(thumbnail: Thumbnail): void {
        this.element.src = thumbnail.url;
        this.element.alt = thumbnail.alt;
    }
}
