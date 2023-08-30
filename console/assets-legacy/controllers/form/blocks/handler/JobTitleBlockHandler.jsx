import React from 'react';
import { translator } from '../../../../services/translator';
import { TextBlockHandler } from './TextBlockHandler';

export class JobTitleBlockHandler extends TextBlockHandler {
    getPlaceholder() {
        return translator.trans('form.types.job_title');
    }
}
