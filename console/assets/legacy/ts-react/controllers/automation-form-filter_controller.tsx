import { AbstractController } from './abstract_controller';

export default class extends AbstractController {
    readonly triggerFieldTarget: HTMLSelectElement;
    readonly formFilterTarget: HTMLDivElement;
    readonly tagFilterTarget: HTMLDivElement;

    static targets = ['triggerField', 'formFilter', 'tagFilter'];

    connect() {
        const refresh = () => {
            const trigger = this.triggerFieldTarget.value;

            this.formFilterTarget.style.display = trigger === 'new_form_answer' ? 'block' : 'none';
            this.tagFilterTarget.style.display = trigger === 'contact_tagged' ? 'block' : 'none';
        };

        refresh();
        this.triggerFieldTarget.addEventListener('change', refresh);
    }
}
