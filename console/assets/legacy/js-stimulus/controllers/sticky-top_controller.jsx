import * as Sticky from 'sticky-js';
import { Controller } from 'stimulus';

export default class extends Controller {
    connect() {
        new Sticky('.sticky-top');
    }
}
