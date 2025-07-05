import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['button', 'dot', 'toggle'];

    connect() {
        // Show dot if never clicked or new announcements since last click
        const newestTimestamp = this.element.getAttribute('data-latest');
        const lastClickTimestamp = localStorage.getItem('announcements_last_click_timestamp');

        if (!lastClickTimestamp || lastClickTimestamp < newestTimestamp) {
            this.dotTarget.style.display = 'inline-block';
        }

        // On click: open, hide dot, store new timestamp
        this.buttonTarget.addEventListener('click', () => {
            this.dotTarget.style.display = 'none';

            if (newestTimestamp) {
                localStorage.setItem('announcements_last_click_timestamp', newestTimestamp + 1);
            }

            for (let i in this.toggleTargets) {
                if (!this.toggleTargets.hasOwnProperty(i)) {
                    continue;
                }

                if (this.toggleTargets[i].style.display === 'block') {
                    this.toggleTargets[i].style.display = 'none';
                } else {
                    this.toggleTargets[i].style.display = 'block';
                }
            }
        });
    }
}
