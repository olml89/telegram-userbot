export class Duration {
    public constructor(public seconds: number) {}

    public format(): string {
        if (this.seconds < 60) {
            return `${Math.round(this.seconds)}s`;
        }

        if (this.seconds < 3600) {
            const minutes = Math.floor(this.seconds / 60);
            const seconds = Math.floor(this.seconds % 60);

            return `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }

        const hours = Math.floor(this.seconds / 3600);
        const minutes = Math.floor((this.seconds % 3600) / 60);
        const seconds = Math.floor(this.seconds % 60);

        return `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
}

export class DurationElement {
    private element: HTMLDivElement;

    public constructor(metaRight: HTMLDivElement) {
        const lengthRow = document.createElement('div');
        lengthRow.className = 'file-row';
        lengthRow.innerHTML = `
                <span class="file-label">Length:</span>
                <span class="file-value" data-duration>Loading…</span>
            `;
        metaRight.appendChild(lengthRow);
        this.element = lengthRow.querySelector<HTMLDivElement>('[data-duration]') as HTMLDivElement;
    }

    public set(duration: Duration): void {
        this.element.textContent = duration.format();
    }
}
