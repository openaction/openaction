import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['hasForm', 'cta'];

    connect() {
        this.refresh();
        this.hasFormTarget.addEventListener('change', () => this.refresh());
    }

    refresh() {
        this.ctaTarget.style.display = this.hasFormTarget.checked ? 'none' : 'block';
    }
}
