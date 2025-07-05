import React from 'react';
import { Controller } from 'stimulus';
import { render } from 'react-dom';
import { CustomContentEdit } from './components/CustomContentEdit';

export default class extends Controller {
    static targets = ['content'];

    connect() {
        render(<CustomContentEdit contentInput={this.contentTarget.getAttribute('name')} />, this.element);
    }
}
