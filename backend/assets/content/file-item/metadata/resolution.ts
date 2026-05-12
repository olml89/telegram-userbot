export class Resolution {
    public constructor(public width: number, public height: number) {}

    public format(): string {
        return `${this.width}×${this.height}`;
    }
}

export class ResolutionElement {
    private element: HTMLDivElement;

    public constructor(metaLeft: HTMLDivElement) {
        const resolutionRow = document.createElement('div');
        resolutionRow.className = 'file-row';
        resolutionRow.innerHTML = `
                <span class="file-label">Resolution:</span>
                <span class="file-value file-muted" data-resolution>Loading…</span>
            `;
        metaLeft.appendChild(resolutionRow);
        this.element = resolutionRow.querySelector<HTMLDivElement>('[data-resolution]') as HTMLDivElement;
    }

    public set(resolution: Resolution): void {
        this.element.textContent = resolution.format();
    }
}
