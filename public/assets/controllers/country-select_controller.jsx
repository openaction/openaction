import React from 'react';
import { render } from 'react-dom';
import { Controller } from '@hotwired/stimulus';
import { CountrySelect } from './components/CountrySelect';

export default class extends Controller {
    connect() {
        // Try to detect current country
        this._setDefaultCountry((country) => {
            // Render a better select field with flags
            this._renderFlagsSelect(country);
        });
    }

    _setDefaultCountry(cb) {
        // If it exists in cache, use it
        const country = localStorage.getItem('country');

        if (country) {
            const selectedOption = this.element.querySelector('option[selected]');
            if (selectedOption) {
                selectedOption.selected = false;
            }

            const countryOption = this.element.querySelector('option[value="'+country+'"]');
            if (countryOption) {
                countryOption.selected = true;
            }

            cb(country);

            return;
        }

        // Otherwise find it using the API
        fetch('/api/country')
            .then(res => res.json())
            .then(({ country }) => {
                localStorage.setItem('country', country.toUpperCase());

                const selectedOption = this.element.querySelector('option[selected]');
                if (selectedOption) {
                    selectedOption.selected = false;
                }

                const countryOption = this.element.querySelector('option[value="'+country+'"]');
                if (countryOption) {
                    countryOption.selected = true;
                }

                cb(country.toUpperCase());
            })
        ;
    }

    _renderFlagsSelect(defaultValue) {
        // Create a wrapper
        const wrapper = document.createElement('div');
        this.element.parentNode.insertBefore(wrapper, this.element);
        wrapper.appendChild(this.element);

        // Render the new select field
        const options = [];
        let defaultOption = null;

        this.element.querySelectorAll('option').forEach(option => {
            const data = {
                value: option.value,
                label: <><span className={'mr-1 fi fi-'+option.innerText.toLowerCase()} /> {option.innerText}</>,
            };

            if (option.value === defaultValue) {
                defaultOption = data;
            }

            options.push(data);
        });

        render(
            <CountrySelect
                options={options}
                defaultValue={defaultOption}
                fieldName={this.element.getAttribute('name')}
            />,
            wrapper
        );
    }
}
