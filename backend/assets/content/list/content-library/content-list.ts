import { Component } from '../../../common/component/contracts';
import { ContentNotifications } from './content-notifications';
import { Content } from '../../content';
import { ContentComponent } from '../content-component';
import { ContentQueryFields } from './content-library';
import { BackendError } from '../../../common/backend-error';
import { assertImported } from '../../../common/importer';

export class ContentList implements Component<Content[]> {
    private readonly notifications: ContentNotifications;
    private readonly libraryTable: HTMLTableElement;
    private contents: Content[] = [];

    public constructor(notifications: ContentNotifications, libraryTable: HTMLTableElement) {
        this.notifications = notifications;
        this.libraryTable = libraryTable;
    }

    public static from(libraryNotifications: HTMLDivElement|null, libraryTable: HTMLTableElement|null): ContentList|null {
        const notifications = ContentNotifications.from(libraryNotifications);

        const required = {
            notifications,
            libraryTable,
        };

        if (!assertImported('content-list', required)) {
            return null;
        }

        return new ContentList(required.notifications, required.libraryTable)
    }

    public add(content: Content, contentQueryFields: ContentQueryFields): void {
        this.notifications.clear();
        this.notifications.success(content);

        if (contentQueryFields.matches(content)) {
            const contentComponent = this.createComponent(content, true);
            this.libraryTable.lastChild?.remove();
            this.libraryTable.prepend(contentComponent.element());
            contentQueryFields.pagination.increaseTotalCount();
        }
    }

    private createComponent(content: Content, isNew: boolean = false): ContentComponent {
        this.contents.push(content);

        return new ContentComponent(content, isNew);
    }

    public getValue(): Content[] {
        return this.contents;
    }

    private clear(): void {
        this.libraryTable.innerHTML = '';
    }

    public error(error: BackendError): void {
        this.clear();
        this.notifications.clear();
        this.notifications.error(error);
    }

    public replace(contents: Content[], searchTerm: string|null): void {
        this.clear();
        this.notifications.clear();

        contents.forEach((content: Content): void => {
            const contentComponent = this.createComponent(content);
            this.libraryTable.appendChild(contentComponent.element());

            if (searchTerm) {
                contentComponent.highlight(searchTerm);
            }
        });
    }
}
