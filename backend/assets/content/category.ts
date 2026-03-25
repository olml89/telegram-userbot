import { Entity } from '../common/models/entity';

export class Category extends Entity {
    public readonly name: string;

    public constructor(publicId: string, name: string) {
        super(publicId);

        this.name = name;
    }

    public override equals(other: Category): boolean {
        return super.equals(other);
    }
}
