import { HtmlElementWrapper } from '../../../common/component/contracts';

export interface Highlightable {
    highlight(searchTerm: string): void;
}

export abstract class TextHighlightable implements Highlightable, HtmlElementWrapper {
    private readonly textElement: HTMLElement;

    protected constructor(textElement: HTMLElement) {
        this.textElement = textElement;
    }

    public element(): HTMLElement {
        return this.textElement;
    }

    public highlight(searchTerm: string): void {
        const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        this.element().innerHTML = this.element().textContent.replace(regex, '<mark>$1</mark>');
    }
}
