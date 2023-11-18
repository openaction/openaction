import React from 'react';
import { Controller } from 'stimulus';
import { render } from 'react-dom';
import { PostEdit } from './components/PostEdit';
import { CONTENT_EDITOR_WEB_OPTIONS } from '../../editor/ContentBuilderOptions';
import { Editor } from '../../editor/Editor';

export default class extends Controller {
    static targets = [
        'topbar',
        'editor',
        'title',
        'content',
        'description',
        'externalUrl',
        'video',
        'publishedAt',
        'image',
        'categories',
        'authors',
        'onlyForMembers',
        'quote',
    ];

    connect() {
        const id = this.editorTarget.getAttribute('id');
        const uploadUrl = this.editorTarget.getAttribute('data-upload-url');
        const editor = new Editor('contentbuilder', id, CONTENT_EDITOR_WEB_OPTIONS, uploadUrl);

        render(
            <PostEdit
                editor={editor}
                features={{
                    sharer: '1' === this.element.getAttribute('data-sharer'),
                }}
                titleInput={this.titleTarget.getAttribute('name')}
                contentInput={this.contentTarget.getAttribute('name')}
                quoteInput={this.quoteTarget.getAttribute('name')}
                descriptionInput={this.descriptionTarget.getAttribute('name')}
                externalUrlInput={this.externalUrlTarget.getAttribute('name')}
                videoInput={this.videoTarget.getAttribute('name')}
                videoValue={this.videoTarget.value}
                publishedAtInput={this.publishedAtTarget.getAttribute('name')}
                imageInput={this.imageTarget.getAttribute('name')}
                categoriesInput={this.categoriesTarget.getAttribute('name')}
                authorsInput={this.authorsTarget.getAttribute('name')}
                onlyForMembersInput={this.onlyForMembersTarget.getAttribute('name')}
            />,
            this.topbarTarget
        );
    }
}
