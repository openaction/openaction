import React, { useState } from 'react';
import { translator } from '../services/translator';
import { createDate } from '../services/create-date';
import { IMaskInput } from 'react-imask';

export function PublicationEditor(props) {
    const [scheduledDate, setScheduledDate] = useState(createDate().format('DD/MM/YYYY HH:mm'));

    let view = '';

    if (!props.publishedAt) {
        view = (
            <div>
                <div className="world-block p-3 mb-3">
                    <div className="mb-1">
                        <strong>{translator.trans('publication_editor.publish.title')}</strong>
                    </div>

                    <div className="text-muted mb-3">{translator.trans('publication_editor.publish.description')}</div>

                    <button
                        type="button"
                        className="btn btn-secondary border-0"
                        onClick={() => props.onChange(createDate())}
                    >
                        {translator.trans('publication_editor.publish.button')}
                    </button>
                </div>

                <div className="world-block p-3 mb-3">
                    <div className="mb-1">
                        <strong>{translator.trans('publication_editor.schedule.title')}</strong>
                    </div>

                    <div className="text-muted mb-3">{translator.trans('publication_editor.schedule.description')}</div>

                    <div className="mb-3">
                        <IMaskInput
                            lazy={false}
                            mask={Date}
                            pattern="DD/MM/YYYY HH:mm"
                            format={(date) => createDate(date).format('DD/MM/YYYY HH:mm')}
                            parse={(str) => createDate(str, 'DD/MM/YYYY HH:mm')}
                            className="form-control"
                            placeholder={translator.trans('publication_editor.schedule.date_placeholder')}
                            value={scheduledDate}
                            onKeyDown={(e) => {
                                if (e.key === 'Enter' && e.target.value.indexOf('_') === -1) {
                                    setScheduledDate(e.target.value);
                                    props.onChange(createDate(e.target.value, 'DD/MM/YYYY HH:mm'));
                                }
                            }}
                            onInput={(e) => setScheduledDate(e.target.value)}
                            blocks={{
                                YYYY: {
                                    mask: IMask.MaskedRange,
                                    from: 1970,
                                    to: 2030,
                                },
                                MM: {
                                    mask: IMask.MaskedRange,
                                    from: 1,
                                    to: 12,
                                },
                                DD: {
                                    mask: IMask.MaskedRange,
                                    from: 1,
                                    to: 31,
                                },
                                HH: {
                                    mask: IMask.MaskedRange,
                                    from: 0,
                                    to: 23,
                                },
                                mm: {
                                    mask: IMask.MaskedRange,
                                    from: 0,
                                    to: 59,
                                },
                            }}
                        />

                        <div className="mt-1">
                            <em className="text-muted">{translator.trans('publication_editor.schedule.date_help')}</em>
                        </div>
                    </div>

                    <button
                        type="button"
                        className="btn btn-secondary border-0"
                        onClick={() => props.onChange(createDate(scheduledDate, 'DD/MM/YYYY HH:mm'))}
                    >
                        {translator.trans('publication_editor.schedule.button')}
                    </button>
                </div>
            </div>
        );
    } else if (props.publishedAt.isBefore(createDate())) {
        view = (
            <div className="world-block p-3 mb-3">
                <div className="mb-1">
                    <strong>{translator.trans('publication_editor.unpublish.title')}</strong>
                </div>

                <div className="text-muted mb-2">{translator.trans('publication_editor.unpublish.description')}</div>

                <button type="button" className="btn btn-secondary border-0" onClick={() => props.onChange(null)}>
                    <i className="far fa-eye-slash mr-2"></i>
                    {translator.trans('publication_editor.unpublish.button')}
                </button>
            </div>
        );
    } else {
        view = (
            <div className="world-block p-3 mb-3">
                <div className="mb-1">
                    <strong>{translator.trans('publication_editor.cancel_schedule.title')}</strong>
                </div>

                <div className="text-muted mb-2">
                    {translator.trans('publication_editor.cancel_schedule.description')}
                </div>

                <button type="button" className="btn btn-secondary border-0" onClick={() => props.onChange(null)}>
                    <i className="far fa-eye-slash mr-2"></i>
                    {translator.trans('publication_editor.cancel_schedule.button')}
                </button>
            </div>
        );
    }

    return (
        <div>
            <div className="text-muted h6 text-center mt-2 mb-4">
                <PublicationEditorStatus date={props.publishedAt} />
            </div>

            {view}
        </div>
    );
}

function PublicationEditorStatus(props) {
    if (!props.date) {
        return translator.trans('publication_editor.document_status.draft');
    }

    if (props.date.isBefore(createDate())) {
        return (
            <span>
                {translator.trans('publication_editor.document_status.published')}
                {' ' + props.date.fromNow()}
            </span>
        );
    }

    return (
        <span>
            {translator.trans('publication_editor.document_status.scheduled')}
            {' ' + props.date.fromNow()}
        </span>
    );
}
