import React from 'react';
import { translator } from '../../../../services/translator';
import { TextBlockHandler } from './TextBlockHandler';

export class WorkPhoneBlockHandler extends TextBlockHandler {
    getPlaceholder() {
        return translator.trans('form.types.work_phone');
    }
}
