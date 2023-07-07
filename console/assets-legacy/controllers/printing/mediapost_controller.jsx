import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['input', 'yes', 'no'];

    connect() {
        this.refresh();
        this.inputTarget.addEventListener('change', () => this.refresh());
    }

    refresh() {
        if (this.inputTarget.checked) {
            this.yesTarget.style.display = 'block';
            this.noTarget.style.display = 'none';
        } else {
            this.yesTarget.style.display = 'none';
            this.noTarget.style.display = 'block';
        }
    }
}
