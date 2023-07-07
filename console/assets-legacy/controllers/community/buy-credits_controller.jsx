import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['input', 'quantity', 'beforeTax', 'vat', 'afterTax'];

    connect() {
        this.refresh();
    }

    refresh() {
        const quantity = Math.ceil(this.inputTarget.value);
        const unitPrice = parseFloat(this.element.getAttribute('data-unit-price'));
        const beforeTax = quantity * unitPrice;

        const quantityFormatter = new Intl.NumberFormat('fr-FR');
        const currencyFormatter = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' });

        this.quantityTarget.innerText = quantityFormatter.format(quantity);
        this.beforeTaxTarget.innerText = currencyFormatter.format(beforeTax);
        this.vatTarget.innerText = currencyFormatter.format(beforeTax * 0.2);
        this.afterTaxTarget.innerText = currencyFormatter.format(beforeTax * 1.2);
    }
}
