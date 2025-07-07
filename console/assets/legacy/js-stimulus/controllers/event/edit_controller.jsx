import React from 'react';
import { Controller } from 'stimulus';
import { render } from 'react-dom';
import { EventMetadataEdit } from './components/EventMetadataEdit';

export default class extends Controller {
    static targets = ['publishedAt', 'image', 'categories', 'externalUrl', 'onlyForMembers', 'participants'];

    connect() {
        render(
            <EventMetadataEdit
                features={{
                    sharer: '1' === this.element.getAttribute('data-sharer'),
                }}
                publishedAtInput={this.publishedAtTarget.getAttribute('name')}
                imageInput={this.imageTarget.getAttribute('name')}
                externalUrlInput={this.externalUrlTarget.getAttribute('name')}
                categoriesInput={this.categoriesTarget.getAttribute('name')}
                onlyForMembersInput={this.onlyForMembersTarget.getAttribute('name')}
                participantsInput={this.participantsTarget.getAttribute('name')}
            />,
            this.element
        );
    }
}
