import './styles/base.css';
import './styles/layout.css';
import './styles/components.css';
import './styles/pages/library/list.css';
import './styles/pages/library/forms/common.css';
import './styles/pages/library/forms/preview.css';
import './styles/pages/library/forms/add.css';
import './styles/pages/dashboard.css';
import './styles/pages/userbot.css';

import { initCustomSelects } from './components/custom-select.js';
import { initPreviewModal } from './components/content/preview.js';
import { initAddModal } from './components/content/add.js';
import { initModePrice } from './components/content/price.js';
import { initIntensity } from './components/content/intensity.js';
import { initTags } from './components/content/tags.js';
import { initFileUpload } from './components/content/upload-file/upload-file.js';
import { initContentSave } from './components/content/save-content.js';

document.addEventListener('DOMContentLoaded', () => {
    initCustomSelects();
    initPreviewModal();
    initAddModal();
    initModePrice();
    initIntensity();
    initTags();

    const uploadFileState = initFileUpload();
    initContentSave(uploadFileState?.getFileIds);
});
