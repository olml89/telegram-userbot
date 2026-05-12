import { BackendError } from '../../utils/backend';

type MessageBoxSyncButton<T> = {
    label: string;
    classNames: string[];
    value: T;
};

type MessageBoxAsyncButton<T> = MessageBoxSyncButton<T> & {
    action: () => Promise<T>;
    statusMessage: string;
};

type MessageBoxButton<T> = MessageBoxSyncButton<T>|MessageBoxAsyncButton<T>;

function isAsyncButton<T>(button: MessageBoxButton<T>): button is MessageBoxAsyncButton<T> {
    return 'action' in button && 'statusMessage' in button;
}

const MessageBoxButtons = {
    confirm: (): MessageBoxSyncButton<boolean> => ({
        label: 'OK',
        classNames: ['btn-primary'],
        value: true,
    }),
    confirmAsync: (action: () => Promise<boolean>, statusMessage: string): MessageBoxAsyncButton<boolean> => ({
        label: 'OK',
        classNames: ['btn-primary'],
        value: true,
        action: action,
        statusMessage: statusMessage,
    }),
    cancel: (): MessageBoxSyncButton<boolean> => ({
        label: 'Cancel',
        classNames: ['btn-secondary', 'btn-hover-outline'],
        value: false,
    }),
    ok: (): MessageBoxSyncButton<void> => ({
        label: 'OK',
        classNames: ['btn-primary'],
        value: undefined as void,
    }),
};

class MessageBoxStatus {
    private readonly statusElement: HTMLParagraphElement;

    public constructor() {
        this.statusElement = document.createElement('p');
        this.statusElement.classList.add('msgbox-status');
    }

    private clear(): void {
        this.statusElement.classList.remove('is-error', 'text-muted');
        this.statusElement.textContent = '';
    }

    public element(): HTMLParagraphElement {
        return this.statusElement;
    }

    public setLoading(message: string): void {
        this.clear();
        this.statusElement.classList.add('text-muted');
        this.statusElement.textContent = message;
    }

    public setError(message: string): void {
        this.clear();
        this.statusElement.classList.add('is-error');
        this.statusElement.textContent = message;
    }
}

class MessageBoxActions {
    private readonly actions: HTMLDivElement;
    private readonly buttons: HTMLButtonElement[] = [];

    public constructor() {
        this.actions = document.createElement('div');
        this.actions.classList.add('msgbox-actions');
    }

    public addButton(button: HTMLButtonElement): void {
        this.buttons.push(button);
        this.actions.appendChild(button);
    }

    public disable(disabled: boolean): void {
        this.buttons.forEach((button: HTMLButtonElement): void => {
            button.disabled = disabled;
        });
    }

    public element(): HTMLDivElement {
        return this.actions;
    }
}

class MessageBoxComponent<T> {
    private readonly overlay: HTMLDivElement;
    private readonly status: MessageBoxStatus;
    private readonly actions: MessageBoxActions;

    public constructor(
        title: string,
        message: string,
        buttons: MessageBoxButton<T>[],
        resolve: (value: T) => void
    ) {
        this.overlay = document.createElement('div');
        this.overlay.classList.add('msgbox-overlay');

        const msgBox = document.createElement('div');
        msgBox.classList.add('msgbox');

        const titleElement = document.createElement('h2');
        titleElement.classList.add('msgbox-title');
        titleElement.textContent = title;

        const messageElement = document.createElement('p');
        messageElement.classList.add('msgbox-message');
        messageElement.textContent = message;

        this.status = new MessageBoxStatus();
        this.actions = new MessageBoxActions();

        buttons.forEach((button: MessageBoxButton<T>): void => {
            const btn = document.createElement('button');
            btn.textContent = button.label;
            btn.classList.add('btn', ...button.classNames);

            btn.addEventListener('click', async (): Promise<void> => {
                try {
                    const result = await this.processAction(button);
                    this.close();
                    resolve(result as T);
                } catch (e: any) {
                    const error = e as BackendError;
                    this.status.setError(error.message);
                } finally {
                    this.actions.disable(false);
                }
            });

            this.actions.addButton(btn);
        });

        msgBox.append(titleElement, messageElement, this.status.element(), this.actions.element());
        this.overlay.appendChild(msgBox);
    }

    public close(): void {
        this.overlay.remove();
    }

    private async processAction(button: MessageBoxButton<T>): Promise<T> {
        if (isAsyncButton<T>(button)) {
            this.status.setLoading(button.statusMessage);
            this.actions.disable(true);

            return await button.action();
        }

        return button.value;
    }

    public show(): void {
        document.body.appendChild(this.overlay);
    }
}

export class MessageBox {
    private static create<T>(title: string, message: string, buttons: MessageBoxButton<T>[]): Promise<T> {
        return new Promise<T>(resolve => {
            new MessageBoxComponent<T>(title, message, buttons, resolve).show();
        });
    }

    public static confirm(title: string, message: string): Promise<boolean> {
        return this.create<boolean>(
            title,
            message,
            [
                MessageBoxButtons.cancel(),
                MessageBoxButtons.confirm()
            ],
        );
    }

    public static confirmAsync(title: string, message: string, statusMessage: string, action: () => Promise<any>): Promise<boolean> {
        return this.create<boolean>(
            title,
            message,
            [
                MessageBoxButtons.cancel(),
                MessageBoxButtons.confirmAsync(
                    async (): Promise<boolean> => {
                        await action();

                        return true;
                    },
                    statusMessage,
                ),
            ],
        );
    }

    public static error(error: BackendError): Promise<void> {
        return this.prompt('Error', error.message);
    }

    public static prompt(title: string, message: string): Promise<void> {
        return this.create<void>(
            title,
            message,
            [
                MessageBoxButtons.ok(),
            ],
        );
    }
}
