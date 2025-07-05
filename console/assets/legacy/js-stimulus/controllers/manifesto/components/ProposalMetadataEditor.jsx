import React from 'react';
import { translator } from '../../../services/translator';

export function ProposalMetadataEditor(props) {
    const handleInputChange = (e, field) => {
        handleMetadataChange(field, e.target.value);
    };

    const handleMetadataChange = (field, value) => {
        props.onMetadataChange({
            ...props.metadata,
            [field]: value ? value : null,
        });
    };

    return (
        <div>
            <div className="mb-4">
                <div className="text-muted">{translator.trans('manifesto.proposal.metadata_modal.help')}</div>
            </div>

            <div className="mb-3">
                <div className="mb-2">
                    <strong>{translator.trans('manifesto.proposal.metadata_modal.status.label')}</strong>
                </div>

                <div className="mb-1">
                    <select
                        className="form-control"
                        value={props.metadata.status || ''}
                        onChange={(e) => handleInputChange(e, 'status')}
                    >
                        <option value={null}></option>
                        <option value="todo">
                            {translator.trans('manifesto.proposal.metadata_modal.status.choices.todo')}
                        </option>
                        <option value="in_progress">
                            {translator.trans('manifesto.proposal.metadata_modal.status.choices.in_progress')}
                        </option>
                        <option value="done">
                            {translator.trans('manifesto.proposal.metadata_modal.status.choices.done')}
                        </option>
                    </select>
                </div>
            </div>

            <div className="mb-3">
                <div className="mb-2">
                    <strong>{translator.trans('manifesto.proposal.metadata_modal.statusDescription.label')}</strong>
                </div>

                <div className="mb-1">
                    <textarea
                        rows={3}
                        className="form-control"
                        value={props.metadata.statusDescription || ''}
                        disabled={!props.metadata.status}
                        onChange={(e) => handleInputChange(e, 'statusDescription')}
                    ></textarea>
                </div>

                <div className="text-muted">
                    {translator.trans('manifesto.proposal.metadata_modal.statusDescription.help')}
                </div>
            </div>

            <div className="mb-3">
                <div className="mb-2">
                    <strong>{translator.trans('manifesto.proposal.metadata_modal.statusCtaText.label')}</strong>
                </div>

                <div className="mb-1">
                    <input
                        type="text"
                        className="form-control"
                        value={props.metadata.statusCtaText || ''}
                        disabled={!props.metadata.status}
                        onChange={(e) => handleInputChange(e, 'statusCtaText')}
                    />
                </div>

                <div className="text-muted">
                    {translator.trans('manifesto.proposal.metadata_modal.statusCtaText.help')}
                </div>
            </div>

            <div className="mb-3">
                <div className="mb-2">
                    <strong>{translator.trans('manifesto.proposal.metadata_modal.statusCtaUrl.label')}</strong>
                </div>

                <div className="mb-1">
                    <input
                        type="text"
                        className="form-control"
                        value={props.metadata.statusCtaUrl || ''}
                        disabled={!props.metadata.status}
                        onChange={(e) => handleInputChange(e, 'statusCtaUrl')}
                    />
                </div>

                <div className="text-muted">
                    {translator.trans('manifesto.proposal.metadata_modal.statusCtaUrl.help')}
                </div>
            </div>
        </div>
    );
}
