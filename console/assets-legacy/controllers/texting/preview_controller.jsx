import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['input', 'preview', 'length'];

    connect() {
        this.previewTarget.innerHTML = this._nl2br(this._htmlEncode(this.inputTarget.value));
        this.lengthTarget.textContent = this.inputTarget.value.length + ' / 140';

        this.inputTarget.addEventListener('input', () => {
            this.previewTarget.innerHTML = this._nl2br(this._htmlEncode(this.inputTarget.value));
            this.lengthTarget.textContent = this.inputTarget.value.length + ' / 140';
        });
    }

    _nl2br(str) {
        return str.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br />$2');
    }

    _htmlEncode(str) {
        const textarea = document.createElement('textarea');
        textarea.innerText = str;

        return textarea.innerHTML;
    }
}
