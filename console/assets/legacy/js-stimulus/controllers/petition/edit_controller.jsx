import React from 'react';
import { Controller } from 'stimulus';
import { render } from 'react-dom';
import { LocalizedPetitionEdit } from './components/LocalizedPetitionEdit';
import { CONTENT_EDITOR_WEB_OPTIONS } from '../../editor/ContentBuilderOptions';
import { Editor } from '../../editor/Editor';

export default class extends Controller {
    static targets = [
        'topbar',
        'editor',
        'title',
        'content',
        'description',
        'submitButtonLabel',
        'optinLabel',
        'legalities',
        'addressedTo',
        'categories',
        'image',
        'parentSlug',
        'parentStartAt',
        'parentEndAt',
        'parentSignaturesGoal',
        'parentAuthors',
        'parentPublishedAt',
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
            <LocalizedPetitionEdit
                editor={editor}
                features={{
                    sharer: '1' === this.element.getAttribute('data-sharer'),
                }}
                titleInput={this.titleTarget.getAttribute('name')}
                contentInput={this.contentTarget.getAttribute('name')}
                descriptionInput={this.descriptionTarget.getAttribute('name')}
                submitButtonLabelInput={this.submitButtonLabelTarget.getAttribute('name')}
                optinLabelInput={this.optinLabelTarget.getAttribute('name')}
                legalitiesInput={this.legalitiesTarget.getAttribute('name')}
                addressedToInput={this.addressedToTarget.getAttribute('name')}
                categoriesInput={this.categoriesTarget.getAttribute('name')}
                imageInput={this.imageTarget.getAttribute('name')}
                parentSlugInput={this.parentSlugTarget.getAttribute('name')}
                parentStartAtInput={this.parentStartAtTarget.getAttribute('name')}
                parentEndAtInput={this.parentEndAtTarget.getAttribute('name')}
                parentSignaturesGoalInput={this.parentSignaturesGoalTarget.getAttribute('name')}
                parentAuthorsInput={this.parentAuthorsTarget.getAttribute('name')}
                parentPublishedAtInput={this.parentPublishedAtTarget.getAttribute('name')}
            />,
            this.topbarTarget
        );
    }
}
