import React from 'react';
import { render, unmountComponentAtNode } from 'react-dom';
import { AbstractController } from './abstract_controller';

/*
 * Render a given React component as a controller (ie. receiving server data as props).
 */
export default class extends AbstractController {
    componentValue: string;
    propsValue: object;

    static values = {
        component: String,
        props: Object,
    };

    doConnect() {
        let element = this.element as any;

        // Use a timeout to avoid mounting and demounting right after
        if (element.timeout) {
            clearTimeout(element.timeout);
        }

        element.timeout = setTimeout(() => {
            const component = window.Citipo.resolveReactComponent(this.componentValue);
            render(React.createElement(component, this.propsValue, null), this.element);
        }, 150);
    }

    doDisconnect() {
        unmountComponentAtNode(this.element);
    }
}
