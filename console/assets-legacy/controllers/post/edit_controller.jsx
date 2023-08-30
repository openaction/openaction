import React from 'react';
import { Controller } from 'stimulus';
import { render } from 'react-dom';
import { PostEdit } from './components/PostEdit';

export default class extends Controller {
    static targets = [
        'title',
        'content',
        'description',
        'video',
        'publishedAt',
        'image',
        'categories',
        'onlyForMembers',
        'quote',
    ];

    connect() {
        render(
            <PostEdit
                features={{
                    sharer: '1' === this.element.getAttribute('data-sharer'),
                }}
                titleInput={this.titleTarget.getAttribute('name')}
                contentInput={this.contentTarget.getAttribute('name')}
                quoteInput={this.quoteTarget.getAttribute('name')}
                descriptionInput={this.descriptionTarget.getAttribute('name')}
                videoInput={this.videoTarget.getAttribute('name')}
                videoValue={this.videoTarget.value}
                publishedAtInput={this.publishedAtTarget.getAttribute('name')}
                imageInput={this.imageTarget.getAttribute('name')}
                categoriesInput={this.categoriesTarget.getAttribute('name')}
                onlyForMembersInput={this.onlyForMembersTarget.getAttribute('name')}
            />,
            this.element
        );
    }
}
