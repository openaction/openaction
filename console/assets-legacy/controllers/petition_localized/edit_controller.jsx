import React from 'react';
import { Controller } from 'stimulus';
import { render } from 'react-dom';
import { PetitionLocalizedEdit } from './components/PetitionLocalizedEdit';
import { CONTENT_EDITOR_WEB_OPTIONS } from '../../editor/ContentBuilderOptions';
import { Editor } from '../../editor/Editor';

export default class extends Controller {
    static targets = [
        'topbar',
        'editor',
        'title',
        'content',
        'description',
        'addressedTo',
        'legalities',
        'submitButtonLabel',
        'optinLabel',
        'image',
        'categories',
    ];

    connect() {
        const id = this.editorTarget.getAttribute('id');
        const uploadUrl = this.editorTarget.getAttribute('data-upload-url');

        const editor = new Editor('contentbuilder', id, CONTENT_EDITOR_WEB_OPTIONS, uploadUrl);
        if (!window.editors) {
            window.editors = {};
        }

        window.editors[id] = editor;

        render(
            <PetitionLocalizedEdit
                editor={editor}
                features={{
                    sharer: '1' === this.element.getAttribute('data-sharer'),
                }}
                titleInput={this.titleTarget.getAttribute('name')}
                contentInput={this.contentTarget.getAttribute('name')}
                addressedToInput={this.addressedToTarget.getAttribute('name')}
                legalitiesInput={this.legalitiesTarget.getAttribute('name')}
                submitButtonLabelInput={this.submitButtonLabelTarget.getAttribute('name')}
                optinLabelInput={this.optinLabelTarget.getAttribute('name')}
                descriptionInput={this.descriptionTarget.getAttribute('name')}
                imageInput={this.imageTarget.getAttribute('name')}
                categoriesInput={this.categoriesTarget.getAttribute('name')}
            />,
            this.topbarTarget
        );
    }
}
