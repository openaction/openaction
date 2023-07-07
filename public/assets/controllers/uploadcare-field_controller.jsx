import { Controller } from '@hotwired/stimulus';
import uploadcare from 'uploadcare-widget/uploadcare.full.min.js'
import '../styles/uploadcare.css';

export default class extends Controller {
    static values = {
        publicKey: String,
        signature: String,
        expire: String,
    };

    connect() {
        const field = this.element;

        // Create hidden input
        const input = document.createElement('input');
        input.type = 'hidden';
        input.value = field.value;

        field.parentNode.appendChild(input);

        // Initialize widget
        const widget = uploadcare.Widget(input, {
            publicKey: this.publicKeyValue,
            secureSignature: this.signatureValue,
            secureExpire: this.expireValue,
        });

        widget.validators.push((fileInfo) => {
            if (fileInfo.name === null) {
                return;
            }

            const extension = fileInfo.name.split('.').pop();

            if (['pdf', 'png', 'jpg'].indexOf(extension) === -1) {
                throw new Error('fileType');
            }
        });

        // Populate field on value change
        widget.onChange((file) => {
            if (!file) {
                field.value = '';

                return;
            }

            file.done((fileInfo) => {
                field.value = fileInfo.cdnUrl + '';
            });
        });
    }
}
