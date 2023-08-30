import { Controller } from 'stimulus';

export default class extends Controller {
    static values = {
        event: String,
    };

    trigger() {
        window.Citipo.Event('customevent', {
            meta: {
                event: this.eventValue,
            },
        });
    }
}
