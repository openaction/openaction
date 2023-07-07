import { AbstractController } from './abstract_controller';

export default class extends AbstractController {
    readonly triggerFieldTarget: HTMLSelectElement;
    readonly formFilterTarget: HTMLDivElement;

    static targets = ['triggerField', 'formFilter'];

    connect() {
        const refresh = () => {
            this.formFilterTarget.style.display =
                this.triggerFieldTarget.value === 'new_form_answer' ? 'block' : 'none';
        };

        refresh();
        this.triggerFieldTarget.addEventListener('change', refresh);
    }
}
