import './index.css'
import {Application, Controller} from "@hotwired/stimulus"
import {createRoot} from "react-dom/client";
import {createElement, type FunctionComponent} from "react";

/*
 * Load all React controllers
 */

const reactControllers: Record<string, { default: FunctionComponent }> = import.meta.glob(['./**/*.tsx'], {
    base: './controllers',
    eager: true,
});

/*
 * Stimulus helper to render React controllers
 */

const app = Application.start();

app.register('modern-react', class extends Controller {
    declare componentValue: string;
    declare propsValue: object;

    static values = {
        component: String,
        props: Object,
    };

    connect() {
        const element = this.element as any;

        // Use a timeout to avoid mounting and demounting right after
        if (element.timeout) {
            clearTimeout(element.timeout);
        }

        element.timeout = setTimeout(() => {
            const component = reactControllers['./' + this.componentValue + '.tsx'];
            if (typeof component === 'undefined') {
                throw new Error('React controller "' + this.componentValue + '" does not exist');
            }

            element.root = createRoot(element);
            element.root.render(createElement(component.default, this.propsValue, null));
        }, 100);
    }

    disconnect(): void {
        const element = this.element as any;

        if (element.root) {
            element.root.unmount();
        }
    }
});
