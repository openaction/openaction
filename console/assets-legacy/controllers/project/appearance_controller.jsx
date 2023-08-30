import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['fields', 'options'];

    connect() {
        this.fields = {};
        for (let i in this.fieldsTargets) {
            this.fields[this.fieldsTargets[i].value] = this.fieldsTargets[i];
            this.fieldsTargets[i].addEventListener('change', () => this.refresh());
        }

        this.options = {};
        for (let i in this.optionsTargets) {
            this.options[this.optionsTargets[i].getAttribute('data-theme')] = this.optionsTargets[i];
        }

        this.refresh();
    }

    refresh() {
        let focused = null;
        for (let i in this.fields) {
            if (this.fields[i].checked) {
                focused = this.fields[i].value;
            }
        }

        const themes = Object.keys(this.options);
        for (let i in themes) {
            this.options[themes[i]].style.display = 'none';
        }

        if (typeof this.options[focused] !== 'undefined') {
            this.options[focused].style.display = 'block';
        }
    }
}
