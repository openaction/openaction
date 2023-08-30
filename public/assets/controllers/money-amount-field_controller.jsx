import React from 'react';
import { Controller } from '@hotwired/stimulus';
import {render} from 'react-dom';
import {MoneyAmountSelector} from './components/MoneyAmountSelector';

export default class extends Controller {
    static values = {
        suggestions: String,
        customLabel: String,
    };

    connect() {
        // Create a wrapper
        const wrapper = document.createElement('div');
        this.element.parentNode.insertBefore(wrapper, this.element);
        wrapper.appendChild(this.element);

        render(
            <MoneyAmountSelector
                suggestions={this.suggestionsValue.split('|')}
                defaultValue={this.element.getAttribute('value')}
                fieldName={this.element.getAttribute('name')}
                customLabel={this.customLabelValue}
            />,
            wrapper
        );
    }
}
