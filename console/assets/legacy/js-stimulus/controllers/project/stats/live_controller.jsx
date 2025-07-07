import { Controller } from 'stimulus';
import { httpClient } from '../../../services/http-client';

export default class extends Controller {
    connect() {
        this._refresh();
        setInterval(() => this._refresh(), 5000);
    }

    _refresh() {
        httpClient.get(this.element.getAttribute('data-url')).then((response) => {
            this.element.innerText = response.data.count;
        });
    }
}
