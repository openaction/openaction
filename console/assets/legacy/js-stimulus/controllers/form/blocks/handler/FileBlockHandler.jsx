import React from 'react';
import { translator } from '../../../../services/translator';
import { TextBlockHandler } from './TextBlockHandler';

export class FileBlockHandler extends TextBlockHandler {
    getPlaceholder() {
        return translator.trans('form.types.file');
    }

    createDisabledField(placeholder) {
        return <input type="file" className="form-control" disabled={true} placeholder={placeholder} />;
    }
}
