import { TextHighlightable } from './hightlightable';

export class ContentDescription extends TextHighlightable {
    public constructor(description: string) {
        const contentDescription = document.createElement('div');
        contentDescription.classList.add('content-description');
        contentDescription.textContent = description;
        super(contentDescription);
    }
}
