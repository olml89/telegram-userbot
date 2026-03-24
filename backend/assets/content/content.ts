import { Entity } from '../common/models/entity';
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

export type Content = Entity & {
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
};
