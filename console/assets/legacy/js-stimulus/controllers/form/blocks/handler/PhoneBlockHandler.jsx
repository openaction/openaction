import React from 'react';
import { translator } from '../../../../services/translator';
import { TextBlockHandler } from './TextBlockHandler';

export class PhoneBlockHandler extends TextBlockHandler {
    getPlaceholder() {
        return translator.trans('form.types.phone');
    }
}
