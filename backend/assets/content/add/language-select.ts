import { ValidatableSelect } from '../../common/component/select';
import { Language } from '../language';
import { LanguageSelect } from '../language-select';

export class ValidatableLanguageSelect extends ValidatableSelect<Language|null> {
    public static from(selectContainer: HTMLLabelElement|null): ValidatableLanguageSelect|null {
        const languageSelect = LanguageSelect.from(selectContainer);

        return super.createFrom('mode', selectContainer, languageSelect) as ValidatableLanguageSelect|null;
    }
}
