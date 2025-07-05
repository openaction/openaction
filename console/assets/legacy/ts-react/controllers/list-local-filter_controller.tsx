import { AbstractController } from './abstract_controller';
import Fuse from 'fuse.js';

export default class extends AbstractController {
    static targets = ['input', 'item'];

    inputTarget: HTMLInputElement;
    itemTargets: HTMLElement[];

    doConnect() {
        let registry: { [key: string]: HTMLElement } = {};
        let references: { id: string; reference: string }[] = [];

        for (let i in this.itemTargets) {
            registry[this.itemTargets[i].getAttribute('data-id')] = this.itemTargets[i];
            references.push({
                id: this.itemTargets[i].getAttribute('data-id'),
                reference: this.itemTargets[i].getAttribute('data-reference'),
            });
        }

        const fuse = new Fuse(references, { threshold: 0, keys: ['reference'] });

        this.inputTarget.addEventListener('input', (e) => {
            const query = (e.currentTarget as HTMLInputElement).value;

            if (!query) {
                Object.values(registry).forEach((item) => {
                    item.classList.remove('d-none');
                });

                return;
            }

            const results = fuse.search((e.currentTarget as HTMLInputElement).value).map((i) => i.item);

            let displayedResults = {};
            for (let id in registry) {
                displayedResults[id] = false;
            }

            for (let i in results) {
                displayedResults[results[i].id] = true;
            }

            for (let id in displayedResults) {
                if (displayedResults[id]) {
                    registry[id].classList.remove('d-none');
                } else {
                    registry[id].classList.add('d-none');
                }
            }
        });
    }
}
