import { BusyAware, ChangeAware, Component } from '../../components/contracts';
import { Pagination } from '../../models/pagination';
import { SearchInput } from './search-input';
import { PaginationComponent } from './pagination-component';
import { ContentList } from './content-list';
import { CategorySelect } from '../category-select';
import { ModeSelect} from '../mode-select';
import { Content, FindContentParams } from '../content';
import { ContentAddModal } from '../add/add-modal';
import { ContentPreviewModal } from '../preview/preview-modal';
import { BackendApi, BackendError } from '../../utils/backend';
import { assertImported, querySelector } from '../../utils/importer';

class ContentQueryFields implements BusyAware, ChangeAware, Component<FindContentParams> {
    public constructor(
        private readonly searchInput: SearchInput,
        private readonly category: CategorySelect,
        private readonly mode: ModeSelect,
    ) {}

    public static from(libraryFilters: HTMLDivElement|null): ContentQueryFields|null {
        const searchInput = SearchInput.from(querySelector<HTMLInputElement>(libraryFilters, '[data-content-search]'));
        const category = CategorySelect.from(querySelector<HTMLLabelElement>(libraryFilters, '[data-library-category]'));
        const mode = ModeSelect.from(querySelector<HTMLLabelElement>(libraryFilters, '[data-library-mode]'));

        const required = {
            searchInput,
            category,
            mode,
        };

        if (!assertImported('content-query-fields', required)) {
            return null;
        }

        return new ContentQueryFields(
            required.searchInput,
            required.category,
            required.mode,
        );
    }

    public getValue(): FindContentParams {
        return new FindContentParams(
            this.searchInput.getValue(),
            this.category.getValue(),
            this.mode.getValue(),
        );
    }

    public onChange(listener: () => void): void {
        this.searchInput.onChange((): void => listener());
        this.category.onChange((): void => listener());
        this.mode.onChange((): void => listener());
    }

    public setBusy(isBusy: boolean) {
        this.searchInput.setBusy(isBusy);
        this.category.setBusy(isBusy);
        this.mode.setBusy(isBusy);
    }
}

export class ContentLibrary implements BusyAware {
    private readonly contentQueryFields: ContentQueryFields;
    private readonly paginationComponent: PaginationComponent;
    private readonly contentList: ContentList;
    private readonly openContentAddModalBtn: HTMLButtonElement;
    private readonly contentAddModal: ContentAddModal;
    private readonly contentPreviewModal: ContentPreviewModal;
    private readonly backend: BackendApi = new BackendApi();

    private isBusy: boolean = false;

    public constructor(
        contentQueryFields: ContentQueryFields,
        paginationComponent: PaginationComponent,
        contentList: ContentList,
        openContentAddModalBtn: HTMLButtonElement,
        contentAddModal: ContentAddModal,
        contentPreviewModal: ContentPreviewModal,
    ) {
        this.contentQueryFields = contentQueryFields;
        this.paginationComponent = paginationComponent;
        this.contentList = contentList
        this.openContentAddModalBtn = openContentAddModalBtn;
        this.contentAddModal = contentAddModal;
        this.contentPreviewModal = contentPreviewModal;

        this.openContentAddModalBtn.addEventListener('click', (): void => {
            this.contentAddModal.open()
        });

        this.contentAddModal.onAddedContent((content: Content): void => {
            this.add(content)
        });

        this.contentPreviewModal.onDeletedContent(async (content: Content): Promise<void> => {
            await this.delete(content)
        });

        this.contentPreviewModal.onDeletedFile((content: Content): void => {
            this.contentList.update(content)
        });

        this.contentQueryFields.onChange((): void => {
            this.paginationComponent.restart();
        });

        this.paginationComponent.onChange(async (pagination: Pagination): Promise<void> => {
            await this.load(pagination);
        });

        /**
         * Initial content rendering
         */
        void this.load(this.paginationComponent.getValue());
    }

    public static create(): ContentLibrary|null {
        const contentList = ContentList.from(
            querySelector<HTMLDivElement>(document, '[data-library-notifications]'),
            querySelector<HTMLTableElement>(document, '[data-library-table]'),
        );

        const contentQueryFields = ContentQueryFields.from(
            querySelector<HTMLDivElement>(document, '[data-library-filters]'),
        );

        const paginationComponent = PaginationComponent.from(
            querySelector<HTMLDivElement>(document, '[data-library-counter]'),
            querySelector<HTMLDivElement>(document, '[data-library-pagination]'),
        );

        const openContentAddModalBtn = querySelector<HTMLButtonElement>(document, '[data-content-add-open]');
        const contentAddModal = ContentAddModal.from(document.getElementById('contentAddModal') as HTMLDivElement|null);
        const contentPreviewModal = ContentPreviewModal.from(document.getElementById('contentPreviewModal') as HTMLDivElement|null);

        const required = {
            contentList,
            contentQueryFields,
            paginationComponent,
            openContentAddModalBtn,
            contentAddModal,
            contentPreviewModal,
        };

        if (!assertImported('contents-component', required)) {
            return null;
        }

        return new ContentLibrary(
            required.contentQueryFields,
            required.paginationComponent,
            required.contentList,
            required.openContentAddModalBtn,
            required.contentAddModal,
            required.contentPreviewModal,
        );
    }

    private add(content: Content): void {
        /**
         * A) Content does not match with the current filters
         * B) Content matches with the current filters, but pagination is not set on the first page
         *
         * - Reload to fetch the latest content
         */
        if (!this.contentQueryFields.getValue().matches(content) || !this.paginationComponent.isFirstPage()) {
            this.paginationComponent.restart();

            return;
        }

        this.contentList.prepend(content);
        this.paginationComponent.firstPage();
        this.paginationComponent.increaseTotalCount();
    }

    private async delete(content: Content): Promise<void> {
        try {
            const nextPageContents = await this.backend.findContents(
                this.contentQueryFields.getValue(),
                this.paginationComponent.getValue().nextPage(),
            );

            this.contentList.delete(content, nextPageContents.first());
        } catch (e: any) {
            const backendError = e as BackendError;
            console.log(backendError.consoleMessage);
            this.contentList.delete(content);
            this.contentList.error(backendError);
        } finally {
            this.paginationComponent.decreaseTotalCount();
        }
    }

    private async load(pagination: Pagination): Promise<void> {
        if (this.isBusy) {
            return;
        }

        this.setBusy(true);

        try {
            const findContentParams = this.contentQueryFields.getValue()

            const paginatedContents = await this.backend.findContents(
                findContentParams,
                pagination,
            );

            this.contentList.load(paginatedContents.list, findContentParams.search);
            this.paginationComponent.update(paginatedContents.pagination);
        } catch (e: any) {
            const backendError = e as BackendError;
            console.log(backendError.consoleMessage);
            this.contentList.load();
            this.contentList.error(backendError);
            this.paginationComponent.reset();
        } finally {
            this.setBusy(false);
        }
    }

    public setBusy(isBusy: boolean): void {
        this.isBusy = isBusy;
        this.contentList.setBusy(isBusy);
        this.contentQueryFields.setBusy(isBusy);
        this.paginationComponent.setBusy(isBusy);
    }
}
