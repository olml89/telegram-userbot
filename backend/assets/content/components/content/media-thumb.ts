import { CellElement } from './cell-element';
import { Content } from '../../content';
import { File, Image, Video  } from '../../file';
import { FileAdapterFactory } from '../file/file-metadata';

export class MediaThumb extends CellElement {
    private readonly content: Content;

    public constructor(content: Content) {
        super();

        this.content = content;
        const files = this.content.files;
        const fileCount = files.list.length;
        const isThumbnailFile = (file: File): file is Image|Video => file instanceof Image || file instanceof Video;

        const thumbnailFiles = files
            .list
            .filter(isThumbnailFile)
            .slice(0, 4);

        const nonThumbnailFiles = files
            .list
            .filter(file => !isThumbnailFile(file))
            .slice(0, 4 - thumbnailFiles.length);

        const mediaThumb = document.createElement('div');
        mediaThumb.classList.add('media-thumb', this.calculateMediaClass(fileCount));

        this.addThumbnails(mediaThumb, thumbnailFiles);
        this.addPlaceholders(mediaThumb, nonThumbnailFiles);

        if (fileCount > 4) {
            mediaThumb.appendChild(this.createMediaMore(fileCount));
        }

        this.cell.appendChild(mediaThumb);

        mediaThumb.addEventListener('click', (): void => {
            const previewContent = new CustomEvent<Content>('content:preview', {
                detail: this.content,
                bubbles: true,
            });

            mediaThumb.dispatchEvent(previewContent);
        });
    }

    private addPlaceholders(mediaThumb: HTMLDivElement, nonThumbnailFiles: File[]): void {
        nonThumbnailFiles.forEach((nonThumbnailFile: File): void => {
            const placeholder = document.createElement('span');
            placeholder.classList.add('placeholder');
            placeholder.textContent = FileAdapterFactory.from(nonThumbnailFile).emoji();
            mediaThumb.appendChild(placeholder);
        });
    }

    private addThumbnails(mediaThumb: HTMLDivElement, thumbnailFiles: (Image|Video)[]): void {
        thumbnailFiles.forEach((thumbnailFile: Image|Video): void => {
            const thumbnail = document.createElement('img');
            thumbnail.classList.add('media-img');
            thumbnail.src = thumbnailFile.thumbnail.url;
            thumbnail.alt = 'Media thumbnail';
            mediaThumb.appendChild(thumbnail);
        });
    }

    /**
     * fileCount can never be 0 as content always has at least 1 file
     */
    private calculateMediaClass(fileCount: number): string {
        if (fileCount === 1) {
            return 'media-single';
        }

        if (fileCount === 2) {
            return 'media-pair';
        }

        return 'media-grid';
    }

    private createMediaMore(fileCount: number): HTMLSpanElement {
        const more = document.createElement('span');
        more.classList.add('media-more');
        more.textContent = `+${fileCount - 4}`;

        return more;
    }
}
