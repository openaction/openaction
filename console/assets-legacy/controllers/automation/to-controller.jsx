import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['typeField', 'emailField'];

    connect() {
        this.refresh();

        const inputs = this.typeFieldTarget.querySelectorAll('input');

        for (let i in inputs) {
            if (inputs.hasOwnProperty(i)) {
                inputs[i].addEventListener('change', () => this.refresh());
            }
        }
    }

    refresh() {
        if ('everyone' === this.typeFieldTarget.querySelector('input:checked').value) {
            this.emailFieldTarget.style.display = 'none';
        } else {
            this.emailFieldTarget.style.display = 'block';
        }
    }
}
