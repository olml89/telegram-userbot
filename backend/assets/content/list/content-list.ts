import { BusyAware, Component, HtmlElementWrapper } from '../../components/contracts';
import { Content } from '../content';
import { ContentComponent } from './content/content-component';
import { ContentQueryFields } from './content-library';
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

    public success(content: Content): void {
        const notification = document.createElement('div');
        notification.classList.add('alert', 'alert-success');
        notification.innerHTML = `Content <strong>${content.title}</strong> added successfully.`;
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

    public append(content: Content): ContentComponent {
        const contentComponent = this.createComponent(content);
        this.tableBody.appendChild(contentComponent.element());

        return contentComponent;
    }

    public clear(): void {
        this.contentComponents.clear();
        this.tableBody.innerHTML = '';
    }

    private createComponent(content: Content, isNew: boolean = false): ContentComponent {
        const contentComponent = new ContentComponent(content, isNew);
        this.contentComponents.set(content.publicId, contentComponent);

        return contentComponent;
    }

    public getValue(): ContentComponent[] {
        return Array.from(this.contentComponents.values());
    }

    public prepend(content: Content): ContentComponent {
        const contentComponent = this.createComponent(content, true);
        this.tableBody.lastChild?.remove();
        this.tableBody.prepend(contentComponent.element());

        return contentComponent;
    }

    public setBusy(isBusy: boolean) {
        this.table.classList.toggle('is-busy', isBusy);
    }

    public update(content: Content): void {
        const existingContentComponent = this.contentComponents.get(content.publicId);

        if (!existingContentComponent) {
            return;
        }

        const updatedContentComponent = this.createComponent(content, true);

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

    public add(content: Content, contentQueryFields: ContentQueryFields): void {
        this.contentNotifications.clear();
        this.contentNotifications.success(content);

        /**
         * A) Content does not match with the current filters
         * B) Content matches with the current filters, but pagination is not set on the first page
         *
         * - Reload to fetch the latest content
         */
        if (!contentQueryFields.matches(content) || !contentQueryFields.pagination.isFirstPage()) {
            contentQueryFields.pagination.restart();

            return;
        }

        /**
         * Optimistic addition
         *
         * - Add the content to the top of the current table and discard the last one
         * - Increase the total count
         */
        this.contentTable.prepend(content);
        contentQueryFields.pagination.increaseTotalCount();
    }

    public getValue(): Content[] {
        return this
            .contentTable
            .getValue()
            .map((contentComponent: ContentComponent): Content => contentComponent.getValue());
    }

    public error(error: BackendError): void {
        this.contentNotifications.clear();
        this.contentNotifications.error(error);
        this.contentTable.clear();
    }

    public replace(contents: Content[], searchTerm: string|null): void {
        this.contentNotifications.clear();
        this.contentTable.clear();

        contents.forEach((content: Content): void => {
            const contentComponent = this.contentTable.append(content);

            if (searchTerm) {
                contentComponent.highlight(searchTerm);
            }
        });
    }

    public setBusy(isBusy: boolean) {
        this.contentTable.setBusy(isBusy);
    }

    public update(content: Content): void {
        this.contentTable.update(content);
    }
}
