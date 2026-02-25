import { formatProgress } from './format.js';

export const createProgressBar = () => {
    const wrap = document.createElement('div');
    wrap.className = 'file-progress';

    const bar = document.createElement('div');
    bar.className = 'file-progress-bar';
    bar.style.width = '0%';

    const label = document.createElement('span');
    label.className = 'file-progress-label';
    label.textContent = '0%';

    const status = document.createElement('div');
    status.className = 'file-progress-status';
    status.textContent = `${formatProgress()}`;

    wrap.appendChild(bar);
    wrap.appendChild(label);

    return { wrap, bar, label, status };
};
