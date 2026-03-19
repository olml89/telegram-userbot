import './styles/base.css';
import './styles/buttons.css';
import './styles/components.css';
import './styles/forms.css';
import './styles/layout.css';
import './styles/pages/library/list.css';
import './styles/pages/library/forms/common.css';
import './styles/pages/library/forms/preview.css';
import './styles/pages/dashboard.css';
import './styles/pages/userbot.css';
import '../content/add-modal.css';

import { initPreviewModal } from '../content/preview-modal';
import { ContentAddModal } from '../content/add-modal';

document.addEventListener('DOMContentLoaded', (): void => {
    initPreviewModal();
    ContentAddModal.create()
});
