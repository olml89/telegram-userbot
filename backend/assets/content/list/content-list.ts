import { BusyAware, Component, HtmlElementWrapper } from '../../components/contracts';
import { ContentComponent } from './content/content-component';
import { Content } from '../content';
import { BackendError } from '../../utils/backend';
import { assertImported, querySelector } from '../../utils/importer';

class ContentNotifications implements HtmlElementWrapper {
    private readonly notifications: HTMLDivElement;

    public constructor(notifications: HTMLDivElement) {
        this.notifications = notifications;
    }

    public static from(notifications: HTMLDivElement|null): ContentNotifications|null {
        const required = {
            notifications,
        };

        if (!assertImported('notifications', required)) {
            return null;
        }

        return new ContentNotifications(required.notifications);
    }

    public clear(): void {
        this.notifications.innerHTML = '';
    }

    public element(): HTMLDivElement {
        return this.notifications;
    }

    public error(error: BackendError): void {
        const notification = document.createElement('div');
        notification.classList.add('alert', 'alert-error');
        notification.innerHTML = error.formatErrors().join('<br>');
        this.notifications.appendChild(notification);
    }

    public successfullyAdded(content: Content): void {
        const notification = document.createElement('div');
        notification.classList.add('alert', 'alert-success');
        notification.innerHTML = `Content <strong>${content.title}</strong> added successfully.`;
        this.notifications.appendChild(notification);
    }

    public successfullyDeleted(content: Content): void {
        const notification = document.createElement('div');
        notification.classList.add('alert', 'alert-success');
        notification.innerHTML = `Content <strong>${content.title}</strong> deleted successfully.`;
        this.notifications.appendChild(notification);
    }
}

class ContentTable implements BusyAware, Component<ContentComponent[]> {
    private readonly table: HTMLTableElement;
    private readonly tableBody: HTMLTableSectionElement;

    private contentComponents: Map<string, ContentComponent> = new Map<string, ContentComponent>();

    public constructor(table: HTMLTableElement, tableBody: HTMLTableSectionElement) {
        this.table = table;
        this.tableBody = tableBody;
    }

    public static from(table: HTMLTableElement|null): ContentTable|null {
        const tableBody = querySelector<HTMLTableSectionElement>(table, 'tbody');

        const required = {
            table,
            tableBody,
        };

        if (!assertImported('library-table', required)) {
            return null;
        }

        return new ContentTable(required.table, required.tableBody);
    }

    public append(content: Content, delay: number = 0): ContentComponent {
        const contentComponent = this.createComponent(content).delay(delay);
        this.tableBody.appendChild(contentComponent.element());

        return contentComponent;
    }

    public clear(): void {
        this.contentComponents.clear();
        this.tableBody.innerHTML = '';
    }

    private createComponent(content: Content): ContentComponent {
        const contentComponent = new ContentComponent(content);

        contentComponent.onRemove((contentComponent: ContentComponent): void => {
            this.tableBody.removeChild(contentComponent.element())
        });

        this.contentComponents.set(content.publicId, contentComponent);

        return contentComponent;
    }

    public getValue(): ContentComponent[] {
        return Array.from(this.contentComponents.values());
    }

    public prepend(content: Content): ContentComponent {
        const contentComponent = this.createComponent(content).new();
        this.tableBody.lastChild?.remove();
        this.tableBody.prepend(contentComponent.element());

        return contentComponent;
    }

    public remove(content: Content): void {
        const existingContentComponent = this.contentComponents.get(content.publicId);

        if (!existingContentComponent) {
            return;
        }

        this.contentComponents.delete(content.publicId);
        this.tableBody.removeChild(existingContentComponent.element());
    }

    public setBusy(isBusy: boolean) {
        this.table.classList.toggle('is-busy', isBusy);
    }

    public update(content: Content): void {
        const existingContentComponent = this.contentComponents.get(content.publicId);

        if (!existingContentComponent) {
            return;
        }

        const updatedContentComponent = this.createComponent(content).new();

        this.tableBody.replaceChild(
            updatedContentComponent.element(),
            existingContentComponent.element(),
        );
    }
}

export class ContentList implements BusyAware, Component<Content[]> {
    private readonly contentNotifications: ContentNotifications;
    private readonly contentTable: ContentTable

    public constructor(contentNotifications: ContentNotifications, contentTable: ContentTable) {
        this.contentNotifications = contentNotifications;
        this.contentTable = contentTable;
    }

    public static from(libraryNotifications: HTMLDivElement|null, libraryTable: HTMLTableElement|null): ContentList|null {
        const contentNotifications = ContentNotifications.from(libraryNotifications);
        const contentTable = ContentTable.from(libraryTable);

        const required = {
            contentNotifications,
            contentTable,
        };

        if (!assertImported('content-list', required)) {
            return null;
        }

        return new ContentList(required.contentNotifications, required.contentTable);
    }

    public delete(content: Content, nextPageFirstContent?: Content|null): void {
        this.contentNotifications.clear();
        this.contentNotifications.successfullyDeleted(content);
        this.contentTable.remove(content);

        if (nextPageFirstContent) {
            this.contentTable.append(nextPageFirstContent);
        }
    }

    public getValue(): Content[] {
        return this
            .contentTable
            .getValue()
            .map((contentComponent: ContentComponent): Content => contentComponent.getValue());
    }

    public error(error: BackendError): void {
        this.contentNotifications.error(error);
    }

    public load(contents: Content[] = [], highlight: string|null = null): void {
        this.contentNotifications.clear();
        this.contentTable.clear();

        contents.forEach((content: Content, index: number): void => {
            const delay = index * 30;
            const contentComponent = this.contentTable.append(content, delay);

            if (highlight) {
                contentComponent.highlight(highlight);
            }
        });
    }

    public prepend(content: Content): void {
        this.contentNotifications.clear();
        this.contentNotifications.successfullyAdded(content);
        this.contentTable.prepend(content);
    }

    public setBusy(isBusy: boolean) {
        this.contentTable.setBusy(isBusy);
    }

    public update(content: Content): void {
        this.contentTable.update(content);
    }
}
