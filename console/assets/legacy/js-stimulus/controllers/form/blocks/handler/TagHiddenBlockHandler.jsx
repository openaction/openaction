import React from 'react';
import { translator } from '../../../../services/translator';

export class TagHiddenBlockHandler {
    createEmptyData() {
        return { content: translator.trans('form.types.tag_hidden'), config: { tags: '' } };
    }

    createDefaultView(block) {
        return (
            <div>
                <div className="mb-2">{translator.trans('form.types.tag_hidden')}</div>

                <input type="text" className="form-control" value={block.config.tags} disabled={true} />
            </div>
        );
    }

    createFocusedView(block, onChange) {
        return (
            <div>
                <div className="mb-2">{translator.trans('form.types.tag_hidden')}</div>

                <input
                    type="text"
                    className="form-control form-field"
                    autoFocus={true}
                    value={block.config.tags}
                    placeholder={translator.trans('form.list_hidden_tags')}
                    onChange={(event) => onChange({ ...block, config: { tags: event.target.value } })}
                />
            </div>
        );
    }
}
