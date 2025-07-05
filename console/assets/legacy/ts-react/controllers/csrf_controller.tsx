import { request } from '../utils/http';
import { AbstractController } from './abstract_controller';

/*
 * Store and refresh the global CSRF token.
 */
export default class extends AbstractController {
    tokenValue: string;
    refreshUrlValue: string;

    static values = {
        token: String,
        refreshUrl: String,
    };

    doConnect() {
        window.Citipo.token = this.tokenValue;

        // Refresh every 15 minutes
        setInterval(() => this.refreshCsrfToken(this.refreshUrlValue), 900000);
    }

    refreshCsrfToken(refreshUrl: string) {
        request('GET', refreshUrl).then((res) => {
            window.Citipo.token = res.data.token;
        });
    }
}
