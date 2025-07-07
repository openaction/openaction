import { Controller } from 'stimulus';
import { translator } from '../../services/translator';

const regex =
    /[^A-Za-z0-9 \\r\\n@£$¥èéùìòÇØøÅå\u0394_\u03A6\u0393\u039B\u03A9\u03A0\u03A8\u03A3\u0398\u039EÆæßÉ!\"#$%&amp;'()*+,./:;&lt;=&gt;?¡ÄÖÑÜ§¿äöñüà^{}\\\\\\[~\\]|\u20AC]{1,160}/g;

export default class extends Controller {
    connect() {
        this.input = this.element.querySelector('textarea');
        this.label = this.element.querySelector('label');
        this.submit = document.querySelector('#' + this.element.getAttribute('data-button-submit'));
        this.input.addEventListener('input', this.validate.bind(this));
    }

    validate(event) {
        const elError = this.element.querySelector('div.gsm-invalid');
        if (elError) {
            this.element.removeChild(elError);
        }

        const removeOrAddClass = event.target.value.match(regex) ? 'add' : 'remove';
        this.input.classList[removeOrAddClass]('is-invalid');
        this.label.classList[removeOrAddClass]('text-danger');

        this.submit.removeAttribute('disabled');
        if ('add' === removeOrAddClass) {
            this.element.appendChild(this.labelError());
            this.submit.setAttribute('disabled', true);
        }
    }

    labelError() {
        const div = document.createElement('div');
        div.classList.add('gsm-invalid');
        div.innerHTML = `<small class="text-danger">${translator.trans('community.texting.gsm_invalid')}</small>`;

        return div;
    }
}
