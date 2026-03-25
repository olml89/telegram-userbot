import { HtmlElementWrapper } from '../../../common/component/contracts';
import { Content } from '../../content';
import { BackendError } from '../../../common/backend-error';
import { assertImported } from '../../../common/importer';

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
