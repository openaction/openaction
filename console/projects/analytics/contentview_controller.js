import { Controller } from 'stimulus';

export default class extends Controller {
    static values = {
        type: String,
        id: String,
    };

    connect() {
        window.Citipo.Event('contentview', {
            meta: {
                type: this.typeValue,
                id: this.idValue,
            },
        });
    }
}
