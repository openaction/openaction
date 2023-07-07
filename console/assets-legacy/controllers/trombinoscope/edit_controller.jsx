import React from 'react';
import { Controller } from 'stimulus';
import { render } from 'react-dom';
import { PersonEdit } from './components/PersonEdit';

export default class extends Controller {
    static targets = [
        'fullName',
        'role',
        'content',
        'publishedAt',
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
        render(
            <PersonEdit
                fullNameInput={this.fullNameTarget.getAttribute('name')}
                roleInput={this.roleTarget.getAttribute('name')}
                contentInput={this.contentTarget.getAttribute('name')}
                publishedAtInput={this.publishedAtTarget.getAttribute('name')}
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
            this.element
        );
    }
}
