import { Controller } from 'stimulus';
import Sortable from 'sortablejs';
import { httpClient, createUrlEncoded } from '../services/http-client';

export default class extends Controller {
    static targets = ['element'];

    connect() {
        const self = this;
        this.sortable = new Sortable(this.elementTarget, {
            handle: this.element.getAttribute('data-handle') ?? '.handle',
            animation: 150,
            onUpdate: function (/**Event*/ evt) {
                self.request();
            },
        });
    }

    /**
     * @todo can usage this to data-action with button and this controller manage state show button
     */
    request() {
        const fieldId = this.element.getAttribute('data-field-id') ?? 'id';
        const fieldOrder = this.element.getAttribute('data-field-order') ?? 'order';
        const items = this.element.querySelectorAll('[data-id]');

        const data = [];
        let index = 0;
        for (const item of items) {
            data.push({
                [fieldId]: item.getAttribute('data-id'),
                [fieldOrder]: ++index,
            });
        }

        return httpClient.post(
            this.element.getAttribute('data-endpoint'),
            createUrlEncoded({ data: JSON.stringify(data) })
        );
    }
}
