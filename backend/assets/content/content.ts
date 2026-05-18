import { Entity, Payload } from '../models/entity';
import { Pagination } from '../models/pagination';
import { Mode} from './mode';
import { Status } from './status';
import { Language } from './language';
import { Category } from './category';
import { Tag } from './tag';
import { File, FilePayload, Image, Size, Video } from './file';

type ContentFileTypesPayload = {
    images: number,
    audios: number,
    videos: number,
    documents: number,
}

type ContentFilesPayload = {
    types: ContentFileTypesPayload,
    list: FilePayload[],
}

type ContentPayload = Payload & {
    title: string;
    description: string;
    price: number;
    intensity: number;
    sales: number;
    mode: Mode;
    status: Status;
    language: Language;
    category: Category;
    tags: Tag[];
    files: ContentFilesPayload;
    createdAt: string;
    updatedAt: string;
}

export class FileContainer {
    public readonly types: ContentFileTypesPayload;
    private readonly list: File[];

    public constructor(payload: ContentFilesPayload) {
        this.types = payload.types;
        this.list = payload.list.map((file: FilePayload): File => File.from(file));
    }

    public all(): File[] {
        return this.list;
    }

    public delete(file: File): void {
        const index = this.list.indexOf(file);

        if (index !== -1) {
            this.list.splice(this.list.indexOf(file), 1);
        }
    }

    public length(): number {
        return this.list.length;
    }

    public size(): Size {
        return this.list.reduce(
            (total: Size, file: File): Size => total.add(file.size),
            new Size(),
        );
    }

    public withThumbnail(): (Image|Video)[] {
        return this
            .list
            .filter((file: File): file is Image|Video => file instanceof Image || file instanceof Video);
    }

    public withoutThumbnail(): Exclude<File, Image|Video>[] {
        return this
            .list
            .filter((file: File): file is Exclude<File, Image|Video> => !(file instanceof Image || file instanceof Video));
    }
}

export class FindContentParams {
    public constructor(
        public readonly search: string|null = null,
        public readonly category: Category|null = null,
        public readonly mode: Mode|null = null,
    ) {}

    public matches(content: Content): boolean {
        if (this.category && !this.category.equals(content.category)) {
            return false;
        }

        if (this.mode && !this.mode.equals(content.mode)) {
            return false;
        }

        const search = this.search?.toLowerCase();

        if (search) {
            return content.title.toLowerCase().includes(search)
                || content.description.toLowerCase().includes(search)
                || content.tags.some((tag: Tag) => tag.name.toLowerCase().includes(search));
        }

        return true;
    }

    public build(pagination: Pagination): string {
        const params = new URLSearchParams();

        if (this.search) {
            params.set('search', this.search);
        }

        if (this.category) {
            params.set('categoryId', this.category.publicId);
        }

        if (this.mode) {
            params.set('mode', this.mode.value);
        }

        params.set('page', pagination.page.toString());
        params.set('perPage', pagination.perPage.toString());
        const query = params.toString();

        return query ? `?${query}` : '';
    }
}

export class Content extends Entity {
    public readonly title: string;
    public readonly description: string;
    public readonly price: number;
    public readonly intensity: number;
    public readonly sales: number;
    public readonly mode: Mode;
    public readonly status: Status;
    public readonly language: Language;
    public readonly category: Category;
    public readonly tags: Tag[];
    public readonly files: FileContainer;
    public readonly createdAt: string;
    public readonly updatedAt: string;

    public constructor(
        publicId: string,
        title: string,
        description: string,
        price: number,
        intensity: number,
        sales: number,
        mode: Mode,
        status: Status,
        language: Language,
        category: Category,
        tags: Tag[],
        files: FileContainer,
        createdAt: string,
        updatedAt: string,
    ) {
        super(publicId);

        this.title = title;
        this.description = description;
        this.price = price;
        this.intensity = intensity;
        this.sales = sales;
        this.mode = mode;
        this.status = status;
        this.language = language;
        this.category = category;
        this.tags = tags;
        this.files = files;
        this.createdAt = createdAt;
        this.updatedAt = updatedAt;
    }

    public static from(payload: ContentPayload): Content {
        return new Content(
            payload.publicId,
            payload.title,
            payload.description,
            payload.price,
            payload.intensity,
            payload.sales,
            payload.mode,
            payload.status,
            payload.language,
            payload.category,
            payload.tags,
            new FileContainer(payload.files),
            payload.createdAt,
            payload.updatedAt,
        );
    }

    public override equals(other: Content): boolean {
        return super.equals(other);
    }
}

