import React from 'react';
import { render } from 'react-dom';
import { Controller } from 'stimulus';
import { ColorChooser } from '../components/ColorPicker';

export default class extends Controller {
    static targets = ['input'];

    connect() {
        render(
            <ColorChooser
                inputName={this.inputTarget.getAttribute('name')}
                color={this.inputTarget.value}
                choices={this.element.getAttribute('data-choices')}
            />,
            this.element
        );
    }
}
