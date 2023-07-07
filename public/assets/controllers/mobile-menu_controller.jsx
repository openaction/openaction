import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['button', 'menu'];

    connect() {
        this.buttonTarget.addEventListener('click', () => this.toggle());
    }

    toggle() {
        if (this.element.classList.contains('header-view-open')) {
            // Should be closed
            this.element.classList.remove('header-view-open');
            this.menuTarget.style.display = 'none';

            return;
        }

        // Should be opened
        this.element.classList.add('header-view-open');
        this.menuTarget.style.display = 'block';
    }
}
