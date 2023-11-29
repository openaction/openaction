import React from 'react';
import { Controller } from 'stimulus';
import { render } from 'react-dom';
import { PersonEdit } from './components/PersonEdit';
import { Editor } from '../../editor/Editor';
import { CONTENT_EDITOR_WEB_OPTIONS } from '../../editor/ContentBuilderOptions';

export default class extends Controller {
    static targets = [
        'topbar',
        'editor',
        'fullName',
        'role',
        'content',
        'publishedAt',
        'socialWebsite',
        'socialEmail',
        'socialFacebook',
        'socialTwitter',
        'socialInstagram',
        'socialLinkedIn',
        'socialYoutube',
        'socialMedium',
        'socialTelegram',
        'categories',
        'image',
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
            <PersonEdit
                editor={editor}
                fullNameInput={this.fullNameTarget.getAttribute('name')}
                roleInput={this.roleTarget.getAttribute('name')}
                contentInput={this.contentTarget.getAttribute('name')}
                publishedAtInput={this.publishedAtTarget.getAttribute('name')}
                socialWebsiteInput={this.socialWebsiteTarget.getAttribute('name')}
                socialEmailInput={this.socialEmailTarget.getAttribute('name')}
                socialFacebookInput={this.socialFacebookTarget.getAttribute('name')}
                socialTwitterInput={this.socialTwitterTarget.getAttribute('name')}
                socialInstagramInput={this.socialInstagramTarget.getAttribute('name')}
                socialLinkedInInput={this.socialLinkedInTarget.getAttribute('name')}
                socialYoutubeInput={this.socialYoutubeTarget.getAttribute('name')}
                socialMediumInput={this.socialMediumTarget.getAttribute('name')}
                socialTelegramInput={this.socialTelegramTarget.getAttribute('name')}
                categoriesInput={this.categoriesTarget.getAttribute('name')}
                imageInput={this.imageTarget.getAttribute('name')}
            />,
            this.topbarTarget
        );
    }
}
