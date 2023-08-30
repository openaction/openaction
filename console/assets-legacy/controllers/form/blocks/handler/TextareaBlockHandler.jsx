import React from 'react';
import { translator } from '../../../../services/translator';

export class TextareaBlockHandler {
    createEmptyData() {
        return { content: translator.trans('form.question'), config: {} };
    }

    createDefaultView(block) {
        return (
            <div>
                <div className="mb-2">
                    {block.content}
                    {block.required ? <span className="text-danger ml-1">*</span> : ''}
                </div>

                <textarea
                    className="form-control"
                    disabled={true}
                    rows={4}
                    placeholder={translator.trans('form.types.textarea')}
                />
            </div>
        );
    }

    createFocusedView(block, onChange) {
        return (
            <div>
                <div className="mb-2">
                    <input
                        type="text"
                        className="form-control form-field"
                        autoFocus={true}
                        value={block.content}
                        onChange={(event) => onChange({ ...block, content: event.target.value })}
                    />
                </div>

                <div className="mb-4">
                    <div className="form-check">
                        <input
                            className="form-check-input"
                            type="checkbox"
                            checked={block.required}
                            onChange={(event) => onChange({ ...block, required: event.target.checked })}
                            id={'required-' + block.id}
                        />

                        <label className="form-check-label" htmlFor={'required-' + block.id}>
                            {translator.trans('form.required')}
                        </label>
                    </div>
                </div>

                <textarea
                    className="form-control"
                    disabled={true}
                    rows={4}
                    placeholder={translator.trans('form.types.textarea')}
                />
            </div>
        );
    }
}
