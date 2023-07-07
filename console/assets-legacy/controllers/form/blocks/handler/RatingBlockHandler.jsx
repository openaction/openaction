import React from 'react';
import { translator } from '../../../../services/translator';
import { ChoiceEditor } from './components/ChoiceEditor';

export class RatingBlockHandler {
    createEmptyData() {
        return {
            content: translator.trans('form.question'),
            config: { choices: [translator.trans('form.untitled')] },
        };
    }

    createDefaultView(block) {
        return (
            <div>
                <div className="mb-2">
                    {block.content}
                    {block.required ? <span className="text-danger ml-1">*</span> : ''}
                </div>

                <div className="row">
                    {block.config.choices.map((choice) => (
                        <div className="col" key={choice}>
                            <div className="text-center">
                                <input type="radio" disabled={true} />
                                <div className="mt-1">{choice}</div>
                            </div>
                        </div>
                    ))}
                </div>
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

                <ChoiceEditor
                    choices={block.config.choices || []}
                    onChange={(choices) => onChange({ ...block, config: { choices: choices } })}
                />
            </div>
        );
    }
}
