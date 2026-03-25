export interface Identifiable {
    equals(other: Identifiable): boolean;
}

export type Payload = {
    publicId: string;
};

export abstract class Entity implements Identifiable {
    public readonly publicId: string;

    protected constructor(publicId: string) {
        this.publicId = publicId;
    }

    public equals(other: Entity): boolean {
        return this.publicId === other.publicId;
    }
}

export type EntityFactory<T extends Entity> = {
    from(payload: Payload): T;
};

