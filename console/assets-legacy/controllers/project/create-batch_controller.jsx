import { Controller } from 'stimulus';
import { render } from 'react-dom';
import { SmallAreaChooser } from './components/SmallAreaChooser';
import React from 'react';

export default class extends Controller {
    static targets = ['type', 'areas'];

    connect() {
        render(
            <SmallAreaChooser
                type={this.typeTarget.value}
                typeInput={this.typeTarget.getAttribute('name')}
                areas={this.areasTarget.value}
                areasInput={this.areasTarget.getAttribute('name')}
                itemKey={this.element.getAttribute('data-key')}
            />,
            this.element
        );
    }
}
