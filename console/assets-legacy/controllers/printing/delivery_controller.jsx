import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['input', 'addressed', 'unaddressed'];

    connect() {
        this.refresh();
        this.inputTarget.addEventListener('change', () => this.refresh());
    }

    refresh() {
        if (this.inputTarget.checked) {
            this.addressedTarget.style.display = 'block';
            this.unaddressedTarget.style.display = 'none';
        } else {
            this.addressedTarget.style.display = 'none';
            this.unaddressedTarget.style.display = 'block';
        }
    }
}
