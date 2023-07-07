import React from 'react';

export class HtmlBlockHandler {
    createEmptyData() {
        return { content: '', config: {} };
    }

    createDefaultView(block) {
        return <div dangerouslySetInnerHTML={{ __html: block.content }}></div>;
    }

    createFocusedView(block, onChange) {
        return (
            <textarea
                className="form-control form-field text-monospace"
                rows={5}
                value={block.content}
                onChange={(event) => onChange({ ...block, content: event.target.value })}
            />
        );
    }
}
