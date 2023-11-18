import React from 'react';
import { Controller } from 'stimulus';
import { render } from 'react-dom';
import { PageEdit } from './components/PageEdit';
import { Editor } from '../../editor/Editor';
import { CONTENT_EDITOR_WEB_OPTIONS } from '../../editor/ContentBuilderOptions';

export default class extends Controller {
    static targets = [
        'topbar',
        'editor',
        'title',
        'content',
        'description',
        'image',
        'categories',
        'parentId',
        'onlyForMembers',
    ];

    connect() {
        const id = this.editorTarget.getAttribute('id');
        const uploadUrl = this.editorTarget.getAttribute('data-upload-url');
        const editor = new Editor('contentbuilder', id, CONTENT_EDITOR_WEB_OPTIONS, uploadUrl);

        render(
            <PageEdit
                editor={editor}
                features={{
                    sharer: '1' === this.element.getAttribute('data-sharer'),
                }}
                titleInput={this.titleTarget.getAttribute('name')}
                contentInput={this.contentTarget.getAttribute('name')}
                descriptionInput={this.descriptionTarget.getAttribute('name')}
                imageInput={this.imageTarget.getAttribute('name')}
                parentIdInput={this.parentIdTarget.getAttribute('name')}
                categoriesInput={this.categoriesTarget.getAttribute('name')}
                onlyForMembersInput={this.onlyForMembersTarget.getAttribute('name')}
            />,
            this.topbarTarget
        );
    }
}
