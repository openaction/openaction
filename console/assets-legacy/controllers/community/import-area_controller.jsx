import { Controller } from 'stimulus';
import React from 'react';
import { render } from 'react-dom';
import { DefaultImportArea } from './components/DefaultImportArea';

export default class extends Controller {
    connect() {
        render(<DefaultImportArea inputName={this.element.getAttribute('data-input-name')} />, this.element);
    }
}
