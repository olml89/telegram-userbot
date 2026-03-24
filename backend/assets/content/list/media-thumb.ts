import { FileContainer } from '../content';
import { File } from '../file';
import { CellElement } from './cell-element';

export class MediaThumb extends CellElement {
    public constructor(files: FileContainer) {
        super();

        const fileCount = files.list.length;

        const thumbnailFiles = files
            .list
            .filter((file: File) => file.hasThumbnail)
            .slice(0, 4);

        const nonThumbnailFiles = files
            .list
            .filter((file: File) => !file.hasThumbnail)
            .slice(0, 4 - thumbnailFiles.length);

        const mediaThumb = document.createElement('div');
        mediaThumb.classList.add('media-thumb', this.calculateMediaClass(fileCount));

        this.addThumbnails(mediaThumb, thumbnailFiles);
        this.addPlaceholders(mediaThumb, nonThumbnailFiles);

        if (fileCount > 4) {
            mediaThumb.appendChild(this.createMediaMore(fileCount));
        }

        this.cell.appendChild(mediaThumb);
    }

    private addPlaceholders(mediaThumb: HTMLDivElement, nonThumbnailFiles: File[]): void {
        nonThumbnailFiles.forEach((nonThumbnailFile: File): void => {
            const placeholder = document.createElement('span');
            placeholder.classList.add('placeholder');
            placeholder.textContent = this.calculatePlaceholder(nonThumbnailFile);
            mediaThumb.appendChild(placeholder);
        });
    }

    private addThumbnails(mediaThumb: HTMLDivElement, thumbnailFiles: File[]): void {
        thumbnailFiles.forEach((thumbnailFile: File): void => {
            const thumbnail = document.createElement('img');
            thumbnail.classList.add('media-img');
            thumbnail.src = `/api/files/${thumbnailFile.publicId}/thumbnail`;
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

    private calculatePlaceholder(nonThumbnailFile: File): string {
        if (nonThumbnailFile.mimeType.startsWith('audio/')) {
            return '🎵';
        }

        if (nonThumbnailFile.mimeType.startsWith('text/plain') || nonThumbnailFile.mimeType.startsWith('application/pdf')) {
            return '📄';
        }

        return '📦';
    }

    private createMediaMore(fileCount: number): HTMLSpanElement {
        const more = document.createElement('span');
        more.classList.add('media-more');
        more.textContent = `+${fileCount - 4}`;

        return more;
    }
}
