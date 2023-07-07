import { Controller } from 'stimulus';

export default class extends Controller {
    connect() {
        const copyToClipboard = () => {
            const textarea = document.createElement('textarea');
            textarea.value = this.element.getAttribute('data-copy');
            textarea.style.position = 'absolute';
            textarea.style.left = '-9999px';
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
        };

        this.element.addEventListener('click', copyToClipboard);
    }
}
