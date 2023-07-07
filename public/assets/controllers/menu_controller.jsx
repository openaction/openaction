import { Controller } from '@hotwired/stimulus';
import Cookies from 'js-cookie';

export default class extends Controller {
    static targets = ['anonymous', 'loggedin', 'fullname'];

    connect() {
        const cookieName = this.element.getAttribute('data-cookie');
        const cookie = cookieName ? Cookies.get(cookieName) || null : null;

        // No cookie or cookie is empty, keep anonymous display
        if (!cookie) {
            return;
        }

        const payload = JSON.parse(cookie);

        // Populate name
        this.fullnameTarget.innerText = payload.firstName + ' ' + payload.lastName;

        // Hide anonymous display and show logged in display
        for (let i in this.anonymousTargets) {
            this.anonymousTargets[i].style.display = 'none';
        }

        for (let i in this.loggedinTargets) {
            this.loggedinTargets[i].style.display = 'inline-block';
        }
    }
}
