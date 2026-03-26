import { CellElement } from './cell-element';
import { capitalize } from '../../../utils/strings';

export class ActionsMenu extends CellElement {
    public constructor() {
        super();

        const actionsMenu = document.createElement('div');
        actionsMenu.classList.add('actions-menu');
        actionsMenu.appendChild(this.createActionsMenuTrigger());
        actionsMenu.appendChild(this.createActionsMenuDropdown());

        this.cell.appendChild(actionsMenu);
    }

    private createActionsMenuTrigger(): HTMLButtonElement {
        const actionsMenuTrigger = document.createElement('button');
        actionsMenuTrigger.type = 'button';
        actionsMenuTrigger.ariaLabel = 'More actions';
        actionsMenuTrigger.classList.add('actions-menu-trigger');
        actionsMenuTrigger.textContent = '...';

        return actionsMenuTrigger;
    }

    private createActionsMenuDropdown(): HTMLDivElement {
        const actionsMenuDropdown = document.createElement('div');
        actionsMenuDropdown.classList.add('actions-menu-dropdown');
        actionsMenuDropdown.appendChild(this.createActionItem('preview', '👁️'));
        actionsMenuDropdown.appendChild(this.createActionItem('edit', '📝'));
        actionsMenuDropdown.appendChild(this.createActionItem('duplicate', '🗐'));
        actionsMenuDropdown.appendChild(this.createActionItem('delete', '🗑'));

        return actionsMenuDropdown;
    }

    private createActionItem(action: string, icon: string): HTMLButtonElement {
        const actionItem = document.createElement('button');
        actionItem.type = 'button';
        actionItem.classList.add('action-item');
        actionItem.setAttribute(`data-${action}-open`, '');

        const actionIcon = document.createElement('span');
        actionIcon.classList.add('action-icon');
        actionIcon.textContent = icon;

        actionItem.appendChild(actionIcon);
        actionItem.appendChild(document.createTextNode(` ${capitalize(action)}`));

        return actionItem;
    }
}
