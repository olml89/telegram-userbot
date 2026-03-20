import { CustomSelect } from '../../common/component/custom-select';
import { Language } from './language';

export class LanguageSelect extends CustomSelect<Language|null> {
    public override getValue(): Language|null {
        return this.enum as Language|null;
    }

    public static from(selectContainer: HTMLLabelElement|null): LanguageSelect|null {
        return super.createFrom('mode', selectContainer) as LanguageSelect|null;
    }
}
