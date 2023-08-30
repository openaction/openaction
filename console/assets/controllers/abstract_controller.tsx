import { Controller } from 'stimulus';

export abstract class AbstractController extends Controller {
    isConnected: boolean;

    connect(): void {
        // Register this controller in the DOM to ease instance access
        this.element[this.identifier + '_controller'] = this;

        // Call custom doConnect
        this.doConnect();

        // Allow tests to check this controller was connected
        this.isConnected = true;
    }

    disconnect(): void {
        // Allow tests to check this controller was disconnected
        this.isConnected = false;

        // Call custom doDisonnect
        this.doDisonnect();

        // Clear reference to release memory
        this.element[this.identifier + '_controller'] = null;
    }

    doConnect(): void {}

    doDisonnect(): void {}
}
