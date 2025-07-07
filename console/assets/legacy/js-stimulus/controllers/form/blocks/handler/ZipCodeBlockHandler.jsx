import React from 'react';
import { translator } from '../../../../services/translator';
import { TextBlockHandler } from './TextBlockHandler';

export class ZipCodeBlockHandler extends TextBlockHandler {
    getPlaceholder() {
        return translator.trans('form.types.zip_code');
    }
}
