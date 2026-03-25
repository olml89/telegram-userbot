import { Entity, EntityFactory, Payload } from './entity';

type PaginationPayload = {
    page: number;
    perPage: number;
    totalCount: number;
};

export class Pagination {
    public readonly page: number;
    public readonly perPage: number;
    public readonly totalCount: number;

    public constructor(page: number, perPage: number, totalCount: number) {
        if (page < 1) {
            throw Error('page must be greater than 0');
        }

        if (perPage < 1) {
            throw Error('perPage must be greater than 0');
        }

        if (totalCount < 0) {
            throw Error('totalCount cannot be negative');
        }

        this.page = page;
        this.perPage = perPage;
        this.totalCount = totalCount;
    }

    public firstPage(): Pagination {
        return new Pagination(
            1,
            this.perPage,
            this.totalCount,
        );
    }

    public formatCount(): string {
        const firstIndex = (this.page - 1) * this.perPage + 1;
        const lastIndex = Math.min(this.page * this.perPage, this.totalCount);

        return `Showing ${firstIndex} - ${lastIndex} of ${this.totalCount}`;
    }

    public formatPage(): string {
        return `Page ${this.page} of ${this.pageCount()}`;
    }

    public isFirstPage(): boolean {
        return this.page === 1;
    }

    public isLastPage(): boolean {
        return this.page === this.pageCount();
    }

    public isEmpty(): boolean {
        return this.totalCount === 0;
    }

    public increaseTotalCount(): Pagination {
        return new Pagination(
            this.page,
            this.perPage,
            this.totalCount + 1,
        );
    }

    public nextPage(): Pagination {
        return new Pagination(
            this.page + 1,
            this.perPage,
            this.totalCount,
        );
    }

    private pageCount(): number {
        return Math.ceil(this.totalCount / this.perPage);
    }

    public previousPage(): Pagination {
        return new Pagination(
            this.page - 1,
            this.perPage,
            this.totalCount,
        )
    }

    public resetTotalCount(): Pagination {
        return new Pagination(
            this.page,
            this.perPage,
            0,
        );
    }
}

type PaginatedPayload<TPayload extends Payload = Payload> = PaginationPayload & {
    list: TPayload[];
};

export class Paginated<T extends Entity = Entity> {
    public readonly pagination: Pagination;
    public readonly list: T[];

    private constructor(pagination: Pagination, list: T[]) {
        this.pagination = pagination;
        this.list = list;
    }

    public static from<T extends Entity, TPayload extends Payload = Payload>(
        payload: PaginatedPayload<TPayload>,
        entityFactory: EntityFactory<T>,
    ): Paginated<T> {
        return new Paginated<T>(
            new Pagination(
                payload.page,
                payload.perPage,
                payload.totalCount,
            ),
            payload.list.map((item: TPayload) => entityFactory.from(item)),
        );
    }
}
