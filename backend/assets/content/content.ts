import { Entity, Payload } from '../models/entity';
import { Mode} from './mode';
import { Status } from './status';
import { Category } from './category';
import { Tag} from './tag';
import { File } from './file';

type FileCounter = {
    images: number,
    audios: number,
    videos: number,
    documents: number,
}

export type FileContainer = {
    count: FileCounter,
    list: File[],
}

type ContentPayload = Payload & {
    title: string;
    description: string;
    price: number;
    sales: number;
    mode: Mode;
    status: Status;
    category: Category;
    tags: Tag[];
    files: FileContainer;
    createdAt: string;
    updatedAt: string;
}

export class Content extends Entity {
    public readonly title: string;
    public readonly description: string;
    public readonly price: number;
    public readonly sales: number;
    public readonly mode: Mode;
    public readonly status: Status;
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
        sales: number,
        mode: Mode,
        status: Status,
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
        this.sales = sales;
        this.mode = mode;
        this.status = status;
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
            payload.sales,
            payload.mode,
            payload.status,
            payload.category,
            payload.tags,
            payload.files,
            payload.createdAt,
            payload.updatedAt,
        );
    }

    public override equals(other: Content): boolean {
        return super.equals(other);
    }
}

