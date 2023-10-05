import { Controller } from '@hotwired/stimulus';
import '../styles/customblocks.css';

window.QomonIframes = [];

window.addEventListener('message', message => {
    for (let i in window.QomonIframes) {
        if (window.QomonIframes[i].contentWindow === message.source) {
            window.QomonIframes[i].style.height = message.data.height + 'px';
            window.QomonIframes[i].classList.add('contenteditor-customblock-qomon-resized');
            window.QomonIframes[i].scrolling = 'no';
        }
    }
});

export default class extends Controller {
    static values = {
        name: String,
        data: Object,
    }

    connect() {
        if (this.nameValue === 'QomonPetition') {
            return this.qomonPetition(this.dataValue);
        }

        if (this.nameValue === 'QomonForm') {
            return this.qomonForm(this.dataValue);
        }
    }

    qomonPetition(data) {
        const iframe = document.createElement('iframe');
        iframe.src = data.url + '?embed=1';
        iframe.frameBorder = '0';

        window.QomonIframes.push(iframe);

        const div = document.createElement('div');
        div.classList.add('contenteditor-customblock-qomon-petition');
        div.appendChild(iframe);

        this.element.appendChild(div);
    }

    qomonForm(data) {
        const iframe = document.createElement('iframe');
        iframe.src = data.url + '?embed=1';
        iframe.frameBorder = '0';

        window.QomonIframes.push(iframe);

        const div = document.createElement('div');
        div.classList.add('contenteditor-customblock-qomon-form');
        div.appendChild(iframe);

        this.element.appendChild(div);
    }
}
