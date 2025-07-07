import { Controller } from 'stimulus';
import { render } from 'react-dom';
import React from 'react';
import { AreaChooser } from './components/AreaChooser';

export default class extends Controller {
    static targets = ['type', 'areas', 'tags'];

    connect() {
        render(
            <AreaChooser
                type={this.typeTarget.value}
                typeInput={this.typeTarget.getAttribute('name')}
                areas={this.areasTarget.value}
                areasInput={this.areasTarget.getAttribute('name')}
                tags={this.tagsTarget.value}
                tagsInput={this.tagsTarget.getAttribute('name')}
            />,
            this.element
        );
    }
}
