import React from 'react';
import { translator } from '../../../../services/translator';

export class PictureBlockHandler {
    getPlaceholder() {
        return translator.trans('form.types.picture');
    }

    createEmptyData() {
        return { content: translator.trans('form.question'), config: {} };
    }

    createDefaultView(block) {
        return (
            <div>
                <div className="mb-2">
                    {block.content}
                    {block.required ? <span className="picture-danger ml-1">*</span> : ''}
                </div>

                <input type="text" className="form-control" disabled={true} placeholder={this.getPlaceholder()} />
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

                <input type="text" className="form-control" disabled={true} placeholder={this.getPlaceholder()} />
            </div>
        );
    }
}
