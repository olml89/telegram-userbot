import { HtmlElementWrapper } from '../common/component/contracts';
import { ContentAddModal } from './add-modal';
import { CategorySelect } from './category/category-select';
import { ModeSelect } from './mode/mode-select';
import { Content } from './content';
import {ContentComponent} from './content-component';
import { assertImported, querySelector } from '../common/importer';

class Notifications implements HtmlElementWrapper {
    private readonly notifications: HTMLDivElement;

    public constructor(notifications: HTMLDivElement) {
        this.notifications = notifications;
    }

    public static from(notifications: HTMLDivElement|null): Notifications|null {
        const required = {
            notifications,
        };

        if (!assertImported('notifications', required)) {
            return null;
        }

        return new Notifications(required.notifications);
    }

    public clear(): void {
        this.notifications.innerHTML = '';
    }

    public element(): HTMLDivElement {
        return this.notifications;
    }

    public success(content: Content): void {
        const notification = document.createElement('div');
        notification.classList.add('alert', 'alert-success');
        notification.innerHTML = `Content <strong>${content.title}</strong> added successfully.`;
        this.notifications.appendChild(notification);
    }
}

export class ContentsComponent  {
    private readonly notifications: Notifications;
    private readonly contentsTable: HTMLTableElement;
    private readonly category: CategorySelect;
    private readonly mode: ModeSelect;
    private readonly openContentAddModalBtn: HTMLButtonElement;
    private readonly contentAddModal: ContentAddModal;

    public constructor(
        notifications: Notifications,
        contentsTable: HTMLTableElement,
        category: CategorySelect,
        mode: ModeSelect,
        openContentAddModalBtn: HTMLButtonElement,
        contentAddModal: ContentAddModal,
    ) {
        this.notifications = notifications;
        this.contentsTable = contentsTable;
        this.category = category;
        this.mode = mode;
        this.openContentAddModalBtn = openContentAddModalBtn;
        this.contentAddModal = contentAddModal;

        this.openContentAddModalBtn.addEventListener('click', (): void => this.contentAddModal.open());
        this.contentAddModal.onAddedContent((content: Content): void => this.addContent(content));
    }

    public static create(): ContentsComponent|null {
        const notifications = Notifications.from(querySelector<HTMLDivElement>(document, '[data-library-notifications]'));
        const contentsTable = querySelector<HTMLTableElement>(document, '[data-library-table-body]');
        const category = CategorySelect.from(querySelector<HTMLLabelElement>(document, '[data-library-category]'));
        const mode = ModeSelect.from(querySelector<HTMLLabelElement>(document, '[data-library-mode]'));
        const openContentAddModalBtn = querySelector<HTMLButtonElement>(document, '[data-content-open]');
        const contentAddModal = ContentAddModal.from(document.getElementById('contentAddModal') as HTMLDivElement|null);

        const required = {
            notifications,
            contentsTable,
            category,
            mode,
            openContentAddModalBtn,
            contentAddModal,
        };

        if (!assertImported('contents-component', required)) {
            return null;
        }

        return new ContentsComponent(
            required.notifications,
            required.contentsTable,
            required.category,
            required.mode,
            required.openContentAddModalBtn,
            required.contentAddModal,
        )
    }

    private addContent(content: Content): void {
        this.notifications.clear();
        this.notifications.success(content);
        const contentComponent = new ContentComponent(content);
        this.contentsTable.prepend(contentComponent.element());
    }
}
