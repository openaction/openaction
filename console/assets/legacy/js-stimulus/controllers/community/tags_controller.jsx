import { Controller } from 'stimulus';
import React from 'react';
import { render } from 'react-dom';
import { TagsInput } from './components/TagsInput';

export default class extends Controller {
    static targets = ['input'];

    connect() {
        render(
            <TagsInput
                allowAdd={'1' === this.element.getAttribute('data-allow-add')}
                tags={this.inputTarget.value}
                name={this.inputTarget.getAttribute('name')}
            />,
            this.element
        );
    }
}
