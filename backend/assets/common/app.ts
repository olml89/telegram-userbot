import './styles/base.css';
import './styles/buttons.css';
import './styles/components.css';
import './styles/forms.css';
import './styles/layout.css';
import './styles/modals.css';
import './styles/pages/library/forms/common.css';
import './styles/pages/library/forms/preview.css';
import './styles/pages/dashboard.css';
import './styles/pages/userbot.css';
import '../content/list/content-library/content-library.css';

import { initPreviewModal } from '../content/preview/preview-modal';
import { ContentLibrary } from '../content/list/content-library/content-library';

document.addEventListener('DOMContentLoaded', (): void => {
    initPreviewModal();
    ContentLibrary.create()
});
