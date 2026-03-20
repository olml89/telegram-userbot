import { ContentAddModal } from './add-modal';
import { CategorySelect } from './category/category-select';
import { ModeSelect } from './mode/mode-select';
import { Content } from './content';
import { assertImported, querySelector } from '../common/importer';

export class ContentsComponent  {
    private readonly contentsTable: HTMLTableElement;
    private readonly category: CategorySelect;
    private readonly mode: ModeSelect;
    private readonly openContentAddModalBtn: HTMLButtonElement;
    private readonly contentAddModal: ContentAddModal;

    public constructor(
        contentsTable: HTMLTableElement,
        category: CategorySelect,
        mode: ModeSelect,
        openContentAddModalBtn: HTMLButtonElement,
        contentAddModal: ContentAddModal,
    ) {
        this.contentsTable = contentsTable;
        this.category = category;
        this.mode = mode;
        this.openContentAddModalBtn = openContentAddModalBtn;
        this.contentAddModal = contentAddModal;

        this.openContentAddModalBtn.addEventListener('click', (): void => this.contentAddModal.open());
        this.contentAddModal.onAddedContent((content: Content): void => console.log(content));
    }

    public static create(): ContentsComponent|null {
        const contentsTable = querySelector<HTMLTableElement>(document, '[data-library-table-body]');
        const category = CategorySelect.from(querySelector<HTMLLabelElement>(document, '[data-library-category]'));
        const mode = ModeSelect.from(querySelector<HTMLLabelElement>(document, '[data-library-mode]'));
        const openContentAddModalBtn = querySelector<HTMLButtonElement>(document, '[data-content-open]');
        const contentAddModal = ContentAddModal.create(document.getElementById('contentAddModal') as HTMLDivElement|null);

        const required = {
            contentsTable,
            category,
            mode,
            openContentAddModalBtn,
            contentAddModal,
        };

        if (!assertImported('contents-component', required)) {
            return null;
        }

        return new ContentsComponent(
            required.contentsTable,
            required.category,
            required.mode,
            required.openContentAddModalBtn,
            required.contentAddModal,
        )
    }
}
