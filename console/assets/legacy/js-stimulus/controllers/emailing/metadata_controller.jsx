import React from 'react';
import { Controller } from 'stimulus';
import { render } from 'react-dom';
import { FilterConfigurator } from './components/FilterConfigurator';

export default class extends Controller {
    static targets = ['onlyForMembersInput', 'areasInput', 'tagsInput', 'tagsTypeInput', 'contactsInput'];

    connect() {
        render(
            <FilterConfigurator
                features={{
                    tags: '1' === this.element.getAttribute('data-tags'),
                    areas: '1' === this.element.getAttribute('data-areas'),
                    specific: '1' === this.element.getAttribute('data-specific'),
                    members: '1' === this.element.getAttribute('data-members'),
                }}
                type={
                    this.areasInputTarget.value
                        ? 'area'
                        : this.tagsInputTarget.value
                        ? 'tag'
                        : this.contactsInputTarget.value
                        ? 'contact'
                        : this.onlyForMembersInputTarget.value
                        ? 'member'
                        : 'none'
                }
                onlyForMembersInput={this.onlyForMembersInputTarget.getAttribute('name')}
                areas={this.areasInputTarget.value}
                areasInput={this.areasInputTarget.getAttribute('name')}
                tags={this.tagsInputTarget.value}
                tagsInput={this.tagsInputTarget.getAttribute('name')}
                tagsType={this.tagsTypeInputTarget.value}
                tagsTypeInput={this.tagsTypeInputTarget.getAttribute('name')}
                contacts={this.contactsInputTarget.value}
                contactsInput={this.contactsInputTarget.getAttribute('name')}
            />,
            this.element
        );
    }
}
