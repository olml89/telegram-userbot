import { Entity, Payload } from '../models/entity';

export type TagPayload = Payload & {
    name: string;
}

export class Tag extends Entity {
    public readonly name: string;

    public constructor(publicId: string, name: string) {
        super(publicId);

        this.name = name;
    }

    public static from(payload: TagPayload): Tag {
        return new Tag(payload.publicId, payload.name);
    }

    public override equals(other: Tag): boolean {
        return super.equals(other);
    }
}

