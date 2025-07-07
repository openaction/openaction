import React from 'react';
import { translator } from '../../../../services/translator';

export class ParagraphBlockHandler {
    createEmptyData() {
        return { content: translator.trans('form.paragraph'), config: {} };
    }

    createDefaultView(block) {
        return <div>{block.content}</div>;
    }

    createFocusedView(block, onChange) {
        return (
            <textarea
                className="form-control form-field"
                rows={5}
                value={block.content}
                onChange={(event) => onChange({ ...block, content: event.target.value })}
            />
        );
    }
}
