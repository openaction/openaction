import { Controller } from 'stimulus';
import { render } from 'react-dom';
import React from 'react';
import { ModuleChooser } from './components/ModuleChooser';
import { AreaChooser } from './components/AreaChooser';

export default class extends Controller {
    static targets = ['type', 'areas', 'tags', 'modules', 'tools'];

    connect() {
        render(
            <div>
                <div className="mb-5">
                    <AreaChooser
                        type={this.typeTarget.value}
                        typeInput={this.typeTarget.getAttribute('name')}
                        areas={this.areasTarget.value}
                        areasInput={this.areasTarget.getAttribute('name')}
                        tags={this.tagsTarget.value}
                        tagsInput={this.tagsTarget.getAttribute('name')}
                    />
                </div>

                <ModuleChooser
                    modules={this._filterSelectedValues(this.modulesTarget)}
                    modulesInput={this.modulesTarget.getAttribute('name')}
                    tools={this._filterSelectedValues(this.toolsTarget)}
                    toolsInput={this.toolsTarget.getAttribute('name')}
                />
            </div>,
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
