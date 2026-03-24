import { Select } from '../common/component/select';
import { Language } from './language';

export class LanguageSelect extends Select<Language|null> {
    public override getValue(): Language|null {
        return super.getValue() as Language|null;
    }

    public static from(selectContainer: HTMLLabelElement|null): LanguageSelect|null {
        return super.createFrom('category', selectContainer) as LanguageSelect|null;
    }
}
