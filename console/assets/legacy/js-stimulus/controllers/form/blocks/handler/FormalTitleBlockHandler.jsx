import React from 'react';
import { translator } from '../../../../services/translator';
import { TextBlockHandler } from './TextBlockHandler';

export class FormalTitleBlockHandler extends TextBlockHandler {
    getPlaceholder() {
        return translator.trans('form.types.formal_title');
    }
}
