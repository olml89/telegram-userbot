import { Entity, Payload } from '../models/entity';
import { Mode} from './mode';
import { Status } from './status';
import { Language } from './language';
import { Category } from './category';
import { Tag } from './tag';
import { File, FilePayload, Size } from './file';

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
    public readonly list: File[];

    public constructor(payload: ContentFilesPayload) {
        this.types = payload.types;
        this.list = payload.list.map((file: FilePayload): File => File.from(file));
    }

    public size(): Size {
        return this.list.reduce(
            (total: Size, file: File): Size => total.add(file.size),
            new Size(),
        );
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

