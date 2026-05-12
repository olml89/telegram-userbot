import { ChangeAware, Component } from '../../../components/contracts';
import { File as BackendFile, Size } from '../../file';
import { FileComponent } from './file-component';
import { assertImported } from '../../../utils/importer';

export class FileList implements ChangeAware, Component<BackendFile[]> {
    private readonly fileList: HTMLFormElement;
    private readonly fileCount: HTMLSpanElement;
    private readonly totalSize: HTMLSpanElement;
    private readonly changeListeners: Set<() => void> = new Set();
    private readonly removeFileComponentListeners: Set<(fileComponent: FileComponent) => void> = new Set();
    private readonly removedFileListeners: Set<(file: BackendFile) => void> = new Set();

    private fileComponents: Set<FileComponent> = new Set<FileComponent>();
    private pendingDeletes: number = 0;

    public constructor(fileList: HTMLFormElement, fileCount: HTMLSpanElement, totalSize: HTMLSpanElement) {
        this.fileList = fileList;
        this.fileCount = fileCount;
        this.totalSize = totalSize;

        this.onRemovedFile((): void => this.printFilesSummary());
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
        fileComponent.onRemove((fileComponent: FileComponent): void => this.notifyRemoveFileComponent(fileComponent));
        fileComponent.onRemoved((fileComponent: FileComponent): void => this.removeFileComponent(fileComponent));

        return fileComponent;
    }

    private endDelete(): void {
        this.pendingDeletes = Math.max(0, this.pendingDeletes - 1);
        this.notifyChange();
    }

    public getValue(): BackendFile[] {
        const fileComponents = Array.from(this.fileComponents);

        return fileComponents.map((fileComponent: FileComponent): BackendFile => fileComponent.getValue());
    }

    public isActive(): boolean {
        return this.pendingDeletes > 0;
    }

    public length(): number {
        return this.fileComponents.size;
    }

    private notifyChange(): void {
        this.changeListeners.forEach((listener: () => void): void => listener());
    }

    private notifyRemoveFileComponent(fileComponent: FileComponent): void {
        this.removeFileComponentListeners.forEach((listener: (fileComponent: FileComponent) => void): void => listener(fileComponent));
    }

    private notifyRemovedFile(file: BackendFile): void {
        this.removedFileListeners.forEach((listener: (file: BackendFile) => void): void => listener(file));
    }

    public onChange(listener: () => void): void {
        this.changeListeners.add(listener);
    }

    public onRemoveFileComponent(listener: (fileComponent: FileComponent) => void): void {
        this.removeFileComponentListeners.add(listener);
    }

    public onRemovedFile(listener: (file: BackendFile) => void): void {
        this.removedFileListeners.add(listener);
    }

    private printFilesSummary(): void {
        this.fileCount.textContent = String(this.fileComponents.size);

        const totalSize = Array
            .from(this.fileComponents)
            .reduce((carry: Size, fileComponent: FileComponent) => carry.add(fileComponent.getValue().size), new Size());

        this.totalSize.textContent = totalSize.format();
    }

    private removeFileComponent(fileComponent: FileComponent): void {
        this.fileComponents.delete(fileComponent);
        this.fileList.removeChild(fileComponent.element());
        this.notifyRemovedFile(fileComponent.getValue());
    }

    public setValue(files: BackendFile[]): void {
        this.fileComponents.clear();
        this.fileList.innerHTML = '';

        files.forEach((file: BackendFile): void => {
            const fileComponent = this.createFileComponent(file);
            this.fileComponents.add(fileComponent);
            this.fileList.appendChild(fileComponent.element());
        });

        this.printFilesSummary();
    }
}
