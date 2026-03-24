import {Errorable, ErrorClearable } from '../../../common/component/contracts';
import { Size } from '../../file';

export class FileStatus implements Errorable, ErrorClearable {
    private readonly wrapper: HTMLDivElement;
    private readonly progress: HTMLDivElement;
    private readonly progressBar: HTMLDivElement;
    private readonly progressLabel: HTMLSpanElement;
    private readonly statusLabel: HTMLDivElement;

    /**
     * Exponential Moving Average (EMA) to smooth out speed peaks.
     */
    private readonly emaAlpha: number = 0.2;
    private emaSpeed: number|null = null;

    public constructor(fileElement: HTMLElement) {
        this.wrapper = document.createElement('div');
        this.wrapper.className = 'file-status';

        this.progress = document.createElement('div');
        this.progress.className = 'file-progress';

        this.progressBar = document.createElement('div');
        this.progressBar.className = 'file-progress-bar';
        this.progressBar.style.width = '0%';
        this.progress.appendChild(this.progressBar);

        this.progressLabel = document.createElement('span');
        this.progressLabel.className = 'file-progress-label';
        this.progressLabel.textContent = '0%';
        this.progress.appendChild(this.progressLabel);

        this.statusLabel = document.createElement('div');
        this.statusLabel.className = 'file-status-label';
        this.statusLabel.textContent = '';

        this.wrapper.appendChild(this.progress);
        this.wrapper.appendChild(this.statusLabel);
        fileElement.appendChild(this.wrapper);
    }

    public clearErrors(): void {
        this.wrapper.classList.remove('is-error', 'is-warning');
        this.statusLabel.innerHTML = '';
    }

    private formatEta(seconds: number|null): string {
        if (seconds === null || !Number.isFinite(seconds)) {
            return '—';
        }

        if (seconds <= 0) {
            return '0s';
        }

        if (seconds < 60) {
            return `${Math.round(seconds)}s`;
        }

        const mins = Math.floor(seconds / 60);
        const secs = Math.round(seconds % 60);

        return `${mins}m ${secs}s`;
    }

    private formatSpeed(bytesPerSecond: number): string {
        if (!Number.isFinite(bytesPerSecond) || bytesPerSecond <= 0) {
            bytesPerSecond = 0;
        }

        const size = new Size(bytesPerSecond);

        return `${size.format()}/s`;
    }

    private formatProgress(bytesSent: number, bytesTotal: number, uploadStartedAt: number): string {
        const elapsedSeconds = (Date.now() - uploadStartedAt) / 1000;
        const instantSpeed = bytesSent / Math.max(elapsedSeconds, 0.001);

        this.emaSpeed = this.emaSpeed === null
            ? instantSpeed
            : this.emaAlpha * instantSpeed + (1 - this.emaAlpha) * this.emaSpeed;

        const remainingSeconds = bytesTotal !== bytesSent
            ? (bytesTotal - bytesSent) / Math.max(this.emaSpeed, 0.001)
            : 0;

        const formattedSent = new Size(bytesSent).format();
        const formattedTotal = new Size(bytesTotal).format();
        const formattedSpeed = this.formatSpeed(this.emaSpeed);
        const formattedEta = this.formatEta(remainingSeconds);

        return `Uploading file: ${formattedSent} / ${formattedTotal} · ${formattedSpeed} · ETA ${formattedEta}`;
    }

    public hide(): void {
        this.wrapper.classList.remove('is-visible', 'is-error', 'is-warning');
        this.progress.classList.remove('is-visible');
        this.progressLabel.classList.remove('is-on-bar');
        this.progressLabel.textContent = '0%';
        this.progressBar.style.width = '0%';
        this.statusLabel.classList.remove('is-visible');
        this.statusLabel.innerHTML = '';
        this.emaSpeed = null;
    }

    public setErrors(...errorMessages: string[]): void {
        this.wrapper.classList.remove('is-warning');
        this.wrapper.classList.add('is-visible', 'is-error');
        this.progress.classList.remove('is-visible');
        this.statusLabel.classList.add('is-visible');
        this.setLines(errorMessages);
    }

    private setLines(lines: string[]): void {
        this.statusLabel.innerHTML = '';

        lines.forEach((message: string): void => {
            const line = document.createElement('span');
            line.textContent = message;
            this.statusLabel.appendChild(line);
        });
    }

    public setWarnings(...warningMessages: string[]): void {
        this.wrapper.classList.remove('is-error');
        this.wrapper.classList.add('is-visible', 'is-warning');
        this.progress.classList.remove('is-visible');
        this.statusLabel.classList.add('is-visible');
        this.setLines(warningMessages);
    }

    public showMessage(message: string, withProgress: boolean = false): void {
        this.wrapper.classList.remove('is-error', 'is-warning');
        this.wrapper.classList.add('is-visible');
        this.progress.classList.toggle('is-visible', withProgress);
        this.statusLabel.classList.add('is-visible');
        this.setLines([message]);
    }

    public showProgress(bytesSent: number, bytesTotal: number, uploadStartedAt: number): void {
        this.wrapper.classList.remove('is-error', 'is-warning');
        this.wrapper.classList.add('is-visible');
        this.progress.classList.add('is-visible');
        this.statusLabel.classList.add('is-visible');

        this.updateProgressLabel(bytesSent, bytesTotal);
        this.setLines([this.formatProgress(bytesSent, bytesTotal, uploadStartedAt)]);
    }

    private updateProgressLabel(bytesSent: number, bytesTotal: number): void {
        const pct = bytesTotal > 0 ? (bytesSent / bytesTotal) * 100 : 0;
        const wrapRect = this.progress.getBoundingClientRect();
        const labelRect = this.progressLabel.getBoundingClientRect();
        const filledPx = (pct / 100) * wrapRect.width;
        const labelCenter = (labelRect.left - wrapRect.left) + (labelRect.width / 2);

        this.progressLabel.classList.toggle('is-on-bar', filledPx >= labelCenter);

        const text = `${pct.toFixed(1)}%`;
        this.progressBar.style.width = text;
        this.progressLabel.textContent = text;
    }
}
