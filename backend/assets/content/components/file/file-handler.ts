import { BusyAware, Component } from '../../../components/contracts';
import { FileComponent } from './file-component';
import { assertImported } from '../../../utils/importer';

export class FileHandler implements BusyAware, Component<FileComponent[]> {
    private readonly fileList: HTMLFormElement;
    private readonly handledFiles: Set<FileComponent> = new Set<FileComponent>();

    public constructor(fileList: HTMLFormElement) {
        this.fileList = fileList;
    }

    public static from(fileList: HTMLFormElement|null): FileHandler|null {
        const required = {
            fileList,
        };

        if (!assertImported('file-handler', required)) {
            return null;
        }

        return new FileHandler(required.fileList);
    }

    public add(fileComponent: FileComponent): void {
        this.handledFiles.add(fileComponent);
        this.fileList.appendChild(fileComponent.element());
        void fileComponent.startUpload();
    }

    public destroy(): void {
        this.handledFiles.clear();
        this.fileList.innerHTML = '';
    }

    public getValue(): FileComponent[] {
        return Array.from(this.handledFiles);
    }

    public remove(fileComponent: FileComponent): void {
        this.handledFiles.delete(fileComponent);
        this.fileList.removeChild(fileComponent.element());
    }

    public setBusy(isBusy: boolean): void {
        this.handledFiles.forEach((fileComponent: FileComponent): void => fileComponent.setBusy(isBusy));
    }
}
