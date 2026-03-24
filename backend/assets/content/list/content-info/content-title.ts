import { TextHighlightable } from './hightlightable';

export class ContentTitle extends TextHighlightable {
    public constructor(title: string) {
        const contentTitle = document.createElement('div');
        contentTitle.classList.add('content-title');
        contentTitle.textContent = title;
        super(contentTitle);
    }
}
