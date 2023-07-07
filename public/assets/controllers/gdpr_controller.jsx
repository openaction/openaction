import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'globalNewsletter',
        'globalSms',
        'globalCalls',
        'projectNewsletter',
        'projectSms',
        'projectCalls',
    ];

    connect() {
        // Global newsletter
        const refreshGlobalNewsletter = () => {
            this.projectNewsletterTargets.forEach((target) => {
                if (this.globalNewsletterTarget.checked) {
                    target.checked = true;
                }
            });
        };

        refreshGlobalNewsletter();
        this.globalNewsletterTarget.addEventListener('change', refreshGlobalNewsletter);

        // Projects newsletter
        this.projectNewsletterTargets.forEach((target) => {
            target.addEventListener('change', () => {
                if (!target.checked) {
                    this.globalNewsletterTarget.checked = false;
                }
            });
        });

        // Global SMS
        const refreshGlobalSms = () => {
            this.projectSmsTargets.forEach((target) => {
                if (this.globalSmsTarget.checked) {
                    target.checked = true;
                }
            });
        };

        refreshGlobalSms();
        this.globalSmsTarget.addEventListener('change', refreshGlobalSms);

        // Projects SMS
        this.projectSmsTargets.forEach((target) => {
            target.addEventListener('change', () => {
                if (!target.checked) {
                    this.globalSmsTarget.checked = false;
                }
            });
        });

        // Global calls
        const refreshGlobalCalls = () => {
            this.projectCallsTargets.forEach((target) => {
                if (this.globalCallsTarget.checked) {
                    target.checked = true;
                }
            });
        };

        refreshGlobalCalls();
        this.globalCallsTarget.addEventListener('change', refreshGlobalCalls);

        // Projects calls
        this.projectCallsTargets.forEach((target) => {
            target.addEventListener('change', () => {
                if (!target.checked) {
                    this.globalCallsTarget.checked = false;
                }
            });
        });
    }
}
