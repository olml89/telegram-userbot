import { Entity } from '../common/models/entity';
import { Mode} from './mode/mode';
import { Status } from './status/status';
import { Category } from './category/category';
import { Tag} from './tag/tag';
import { File } from './file/file';

export type Content = Entity & {
    title: string,
    description: string,
    price: number,
    sales: number,
    mode: Mode,
    status: Status,
    category: Category,
    tags: Tag[],
    files: File[],
    createdAt: string,
    updatedAt: string,
};
