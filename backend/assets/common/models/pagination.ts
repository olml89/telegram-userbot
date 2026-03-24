import { Entity } from './entity';

export class Pagination {
    public readonly page: number;
    public readonly perPage: number;
    public readonly totalCount: number;
    public readonly pageCount: number;

    public constructor(page: number, perPage: number, totalCount: number, pageCount: number) {
        if (page < 1) {
            throw Error('page must be greater than 0');
        }

        if (perPage < 1) {
            throw Error('perPage must be greater than 0');
        }

        this.page = page;
        this.perPage = perPage;
        this.totalCount = totalCount;
        this.pageCount = pageCount;
    }

    public static fromJson(payload: any): Pagination {
        return new Pagination(
            payload.page,
            payload.perPage,
            payload.totalCount,
            payload.pageCount,
        );
    }

    public formatCount(): string {
        const firstIndex = (this.page - 1) * this.perPage + 1;
        const lastIndex = Math.min(this.page * this.perPage, this.totalCount);

        return `Showing ${firstIndex} - ${lastIndex} of ${this.totalCount}`;
    }

    public formatPage(): string {
        return `Page ${this.page} of ${this.pageCount}`;
    }

    public hasNextPage(): boolean {
        return this.page < this.pageCount;
    }

    public hasPreviousPage(): boolean {
        return this.page > 1;
    }

    public isEmpty(): boolean {
        return this.totalCount === 0;
    }

    public modifyPage(direction: 1|-1): Pagination {
        return new Pagination(
            this.page + direction,
            this.perPage,
            this.totalCount,
            this.pageCount
        );
    }

    public reset(): Pagination {
        return new Pagination(
            1,
            this.perPage,
            0,
            0,
        );
    }
}

export class Paginated<T extends Entity = Entity> {
    public readonly pagination: Pagination;
    public readonly list: T[];

    private constructor(pagination: Pagination, list: T[]) {
        this.pagination = pagination;
        this.list = list;
    }

    public static from<T extends Entity>(payload: any): Paginated<T> {
        const { list, page, perPage, totalCount, pageCount } = payload;

        return new Paginated<T>(
            new Pagination(page, perPage, totalCount, pageCount),
            list as T[],
        );
    }
}
