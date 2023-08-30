import React from 'react';
import { translator } from '../../../../services/translator';

export class HeaderBlockHandler {
    createEmptyData() {
        return { content: translator.trans('form.untitled'), config: {} };
    }

    createDefaultView(block) {
        return <h5>{block.content}</h5>;
    }

    createFocusedView(block, onChange) {
        return (
            <input
                type="text"
                className="form-control form-control-lg form-field mb-3"
                autoFocus={true}
                value={block.content}
                onChange={(event) => onChange({ ...block, content: event.target.value })}
            />
        );
    }
}
