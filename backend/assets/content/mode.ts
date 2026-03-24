import { Enum } from '../common/models/enum';

export class Mode implements Enum {
    constructor(
        public readonly name: string,
        public readonly value: string,
    ) {}

    public isTeasing(): boolean {
        return this.value === 'teasing';
    }
}
