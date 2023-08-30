import { Controller } from 'stimulus';

export default class extends Controller {
    connect() {
        this.element.style.position = 'absolute';
        this.element.style.right = '100%';
        this.element.style.bottom = '100%';
        this.element.style.zIndex = '-1';
    }
}
