import React from 'react';
import { Controller } from 'stimulus';
import { render } from 'react-dom';
import { PageEdit } from './components/PageEdit';

export default class extends Controller {
    static targets = ['title', 'content', 'description', 'image', 'categories', 'onlyForMembers'];

    connect() {
        render(
            <PageEdit
                features={{
                    sharer: '1' === this.element.getAttribute('data-sharer'),
                }}
                titleInput={this.titleTarget.getAttribute('name')}
                contentInput={this.contentTarget.getAttribute('name')}
                descriptionInput={this.descriptionTarget.getAttribute('name')}
                imageInput={this.imageTarget.getAttribute('name')}
                categoriesInput={this.categoriesTarget.getAttribute('name')}
                onlyForMembersInput={this.onlyForMembersTarget.getAttribute('name')}
            />,
            this.element
        );
    }
}
