import { ChangeAware, Component } from '../../../components/contracts';
import { File as BackendFile, Size } from '../../file';
import { FileComponent } from './file-component';
import { assertImported } from '../../../utils/importer';

export class FileList implements ChangeAware, Component<FileComponent[]> {
    private readonly fileList: HTMLFormElement;
    private readonly fileCount: HTMLSpanElement;
    private readonly totalSize: HTMLSpanElement;
    private readonly changeListeners: Set<() => void> = new Set<() => void>();

    private fileComponents: Set<FileComponent> = new Set<FileComponent>();
    private pendingDeletes: number = 0;

    public constructor(fileList: HTMLFormElement, fileCount: HTMLSpanElement, totalSize: HTMLSpanElement) {
        this.fileList = fileList;
        this.fileCount = fileCount;
        this.totalSize = totalSize;

        this.onChange((): void => {
            this.fileCount.textContent = String(this.fileComponents.size);

            const totalSize = Array
                .from(this.fileComponents)
                .reduce((carry: Size, fileComponent: FileComponent) => carry.add(fileComponent.getValue().size), new Size());

            this.totalSize.textContent = totalSize.format();
        });
    }

    public static from(
        fileList: HTMLFormElement|null,
        fileCount: HTMLSpanElement|null,
        totalSize: HTMLSpanElement|null,
    ): FileList|null {
        const required = {
            fileList,
            fileCount,
            totalSize,
        };

        if (!assertImported('content:preview:tag-list', required)) {
            return null;
        }

        return new FileList(
            required.fileList,
            required.fileCount,
            required.totalSize,
        );
    }

    private beginDelete(): void {
        ++this.pendingDeletes;
        this.notifyChange();
    }

    private createFileComponent(file: BackendFile): FileComponent {
        const fileComponent = new FileComponent(file);
        fileComponent.onDeleteBegin((): void => this.beginDelete());
        fileComponent.onDeleteEnd((): void => this.endDelete());
        fileComponent.onRemoved((fileComponent: FileComponent): void => this.removeFileComponent(fileComponent));

        return fileComponent;
    }

    private endDelete(): void {
        this.pendingDeletes = Math.max(0, this.pendingDeletes - 1);
        this.notifyChange();
    }

    public getValue(): FileComponent[] {
        return Array.from(this.fileComponents);
    }

    public isActive(): boolean {
        return this.pendingDeletes > 0;
    }

    protected notifyChange(): void {
        this.changeListeners.forEach((listener: () => void): void => listener());
    }

    public onChange(listener: () => void): void {
        this.changeListeners.add(listener);
    }

    private removeFileComponent(fileComponent: FileComponent): void {
        this.fileComponents.delete(fileComponent);
        this.fileList.removeChild(fileComponent.element());
    }

    public setValue(files: BackendFile[]): void {
        this.fileComponents.clear();
        this.fileList.innerHTML = '';

        files.forEach((file: BackendFile): void => {
            const fileComponent = this.createFileComponent(file);
            this.fileComponents.add(fileComponent);
            this.fileList.appendChild(fileComponent.element());
        });

        this.notifyChange();
    }
}
