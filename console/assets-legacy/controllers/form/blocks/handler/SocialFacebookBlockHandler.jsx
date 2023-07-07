import React from 'react';
import { translator } from '../../../../services/translator';
import { TextBlockHandler } from './TextBlockHandler';

export class SocialFacebookBlockHandler extends TextBlockHandler {
    getPlaceholder() {
        return translator.trans('form.types.social_facebook');
    }
}
