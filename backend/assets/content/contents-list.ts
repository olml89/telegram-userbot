import {Component, HtmlElementWrapper} from '../common/component/contracts';
import { Content } from './content';
import { ContentComponent } from './content-component';
import { BackendError } from '../common/backend-error';
import { assertImported } from '../common/importer';

export class ContentNotifications implements HtmlElementWrapper {
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

export class ContentsList implements Component<Content[]> {
    private readonly notifications: ContentNotifications;
    private readonly contentsTable: HTMLTableElement;
    private contents: Content[] = [];

    public constructor(notifications: ContentNotifications, contentsTable: HTMLTableElement) {
        this.notifications = notifications;
        this.contentsTable = contentsTable;
    }

    public static from(notifications: ContentNotifications|null, contentsTable: HTMLTableElement|null): ContentsList|null {
        const required = {
            notifications,
            contentsTable,
        };

        if (!assertImported('contents-list', required)) {
            return null;
        }

        return new ContentsList(required.notifications, required.contentsTable)
    }

    public add(content: Content): void {
        this.insert(content, true);
        this.notifications.clear();
        this.notifications.success(content);
    }

    public getValue(): Content[] {
        return this.contents;
    }

    private insert(content: Content, isNew: boolean = false): void {
        this.contents.push(content);
        const contentComponent = new ContentComponent(content, isNew);
        this.contentsTable.appendChild(contentComponent.element());
    }

    private clear(): void {
        this.contentsTable.innerHTML = '';
    }

    public error(error: BackendError): void {
        this.clear();
        this.notifications.clear();
        this.notifications.error(error);
    }

    public show(contents: Content[]): void {
        this.clear();
        this.notifications.clear();
        contents.forEach((content: Content): void => this.insert(content));
    }
}
