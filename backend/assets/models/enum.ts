import { Comparable } from './comparable';

export class Enum implements Comparable {
    public readonly value: string;
    public readonly name: string;

    public constructor(value: string, name: string) {
        this.value = value;
        this.name = name;
    }

    public equals(other: Enum): boolean {
        return this.value === other.value;
    }
}
