import { Select, ValidatableSelect } from '../../common/component/select';
import { Language } from './language';

export class LanguageSelect extends Select<Language|null> {
    public override getValue(): Language|null {
        return super.getValue() as Language|null;
    }

    public static from(selectContainer: HTMLLabelElement|null): LanguageSelect|null {
        return super.createFrom('category', selectContainer) as LanguageSelect|null;
    }
}


export class ValidatableLanguageSelect extends ValidatableSelect<Language|null> {
    public static from(selectContainer: HTMLLabelElement|null): ValidatableLanguageSelect|null {
        const languageSelect = LanguageSelect.from(selectContainer);

        return super.createFrom('mode', selectContainer, languageSelect) as ValidatableLanguageSelect|null;
    }
}
