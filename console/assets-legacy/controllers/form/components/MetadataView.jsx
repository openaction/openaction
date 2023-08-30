import React from 'react';
import { translator } from '../../../services/translator';

export function MetadataView(props) {
    if (!props.focused) {
        return (
            <div className="p-3">
                <h3 className="mb-0">{props.title}</h3>

                {!props.description ? '' : <p className="mt-2 mb-0">{props.description}</p>}

                <div className="form-check disabled mt-2">
                    <input
                        type="checkbox"
                        className="form-check-input"
                        disabled={true}
                        checked={props.proposeNewsletter}
                    />
                    <label className="form-check-label">{translator.trans('form.proposeNewsletter')}</label>
                </div>

                <div className="form-check disabled mt-2">
                    <input
                        type="checkbox"
                        className="form-check-input"
                        disabled={true}
                        checked={props.onlyForMembers}
                    />
                    <label className="form-check-label">{translator.trans('form.onlyForMembers')}</label>
                </div>

                {!props.redirectUrl ? (
                    ''
                ) : (
                    <p className="mt-2 mb-0 text-muted">
                        {translator.trans('form.redirectUrl')}
                        <br />
                        {props.redirectUrl}
                    </p>
                )}
            </div>
        );
    }

    return (
        <div className="form-block-view">
            <input
                type="text"
                className="form-control form-control-lg form-field mb-3"
                value={props.title || ''}
                autoFocus={true}
                onChange={(event) => props.setTitle(event.target.value)}
            />

            <textarea
                className="form-control form-field mb-3"
                rows={4}
                value={props.description || ''}
                onChange={(event) => props.setDescription(event.target.value)}
            />

            <div className="form-check disabled">
                <input
                    type="checkbox"
                    className="form-check-input"
                    id="propose-newsletter"
                    checked={props.proposeNewsletter}
                    onChange={(event) => props.setProposeNewsletter(event.target.checked)}
                />

                <label className="form-check-label" htmlFor="propose-newsletter">
                    {translator.trans('form.proposeNewsletter')}
                </label>
            </div>

            <div className="form-check disabled">
                <input
                    type="checkbox"
                    className="form-check-input"
                    id="only-for-members"
                    checked={props.onlyForMembers}
                    onChange={(event) => props.setOnlyForMembers(event.target.checked)}
                />

                <label className="form-check-label" htmlFor="only-for-members">
                    {translator.trans('form.onlyForMembers')}
                </label>
            </div>

            <div className="form-group mt-3">
                <label htmlFor="redirect-url">{translator.trans('form.redirectUrl')}</label>

                <input
                    type="text"
                    id="redirect-url"
                    className="form-control form-field"
                    value={props.redirectUrl || ''}
                    onChange={(event) => props.setRedirectUrl(event.target.value)}
                />

                <small className="text-muted">{translator.trans('form.redirectUrlHelp')}</small>
            </div>
        </div>
    );
}
