import { Controller } from 'stimulus';
import { render } from 'react-dom';
import React from 'react';
import { ModuleChooser } from './components/ModuleChooser';

export default class extends Controller {
    static targets = ['modules', 'tools'];

    connect() {
        render(
            <ModuleChooser
                modules={this._filterSelectedValues(this.modulesTarget)}
                modulesInput={this.modulesTarget.getAttribute('name')}
                tools={this._filterSelectedValues(this.toolsTarget)}
                toolsInput={this.toolsTarget.getAttribute('name')}
            />,
            this.element
        );
    }

    _filterSelectedValues(selectNode) {
        let selected = [];
        for (let i in selectNode.options) {
            if (selectNode.options.hasOwnProperty(i) && selectNode.options[i].selected) {
                selected.push(selectNode.options[i].value);
            }
        }

        return selected;
    }
}
