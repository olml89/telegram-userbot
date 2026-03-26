import { Content } from '../../content';
import { Mode } from '../../mode';
import { CellElement } from './cell-element';

export class PriceInfo extends CellElement {
    public constructor(content: Content) {
        super();

        const priceInfo = document.createElement('div');
        priceInfo.appendChild(this.createPriceValue(content.price));
        priceInfo.appendChild(this.createModePill(content.mode));

        this.cell.appendChild(priceInfo);
    }

    private createPriceValue(price: number): HTMLDivElement {
        const priceValue = document.createElement('div');
        priceValue.classList.add('price-value');
        priceValue.textContent = `$${price.toString()}`;

        return priceValue;
    }

    private createModePill(mode: Mode): HTMLSpanElement {
        const modePill = document.createElement('span');
        modePill.classList.add('pill', 'pill-muted');
        modePill.textContent = mode.name;

        return modePill;
    }
}
