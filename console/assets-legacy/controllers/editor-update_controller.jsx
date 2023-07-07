import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['close'];

    connect() {
        if (localStorage.getItem('editor-update_1_closed')) {
            return;
        }

        this.element.classList.remove('d-none');

        this.closeTarget.addEventListener('click', () => {
            this.element.classList.add('d-none');
            localStorage.setItem('editor-update_1_closed', '1');
        });
    }
}
