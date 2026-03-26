import {Select, ValidatableSelect} from '../../components/select/select';
import { Language } from '../language';

export class LanguageSelect extends Select<Language|null> {
    public override getValue(): Language|null {
        if (this.selectedOption === null) {
            return null;
        }

        return new Language(
            this.selectedOption.getValue(),
            this.selectedOption.getLabel()
        );
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
