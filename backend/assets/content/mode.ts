import { Enum } from '../models/enum';

export class Mode extends Enum {
    public override equals(other: Mode): boolean {
        return super.equals(other);
    }

    public isTeasing(): boolean {
        return this.value === 'teasing';
    }
}
