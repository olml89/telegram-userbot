import './base.css';
import './components/common.css';
import './layout.css';
import './pages/dashboard.css';
import './pages/library.css';
import './pages/userbot.css';

import { initPreviewModal } from './content/preview/preview-modal';
import { ContentLibrary } from './content/list/content-library';

document.addEventListener('DOMContentLoaded', (): void => {
    initPreviewModal();
    ContentLibrary.create()
});
