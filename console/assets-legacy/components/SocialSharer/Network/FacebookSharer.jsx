import React from 'react';
import { translator } from '../../../services/translator';

export function FacebookSharer(props) {
    let link = 'http://www.facebook.com/sharer.php';
    link += '?u=' + encodeURIComponent(props.metadata.url);

    return (
        <div className="social-sharer-post social-sharer-facebook">
            <div className="social-sharer-facebook-preview">
                {props.metadata.imageUrl ? (
                    <div
                        className="social-sharer-facebook-preview-image"
                        style={{ backgroundImage: 'url(' + props.metadata.imageUrl + ')' }}
                    ></div>
                ) : (
                    ''
                )}

                <div className="p-2">
                    <div className="social-sharer-facebook-preview-url">{props.metadata.host}</div>

                    <div className="social-sharer-facebook-preview-title">{props.metadata.title}</div>

                    <div className="social-sharer-facebook-preview-description">{props.metadata.description}</div>
                </div>
            </div>

            <div className="pt-3 text-center">
                <a href={link} className="btn btn-primary btn-block" target="_blank" rel="noopener noreferrer">
                    {translator.trans('social_sharer.finalize.facebook')}
                </a>
            </div>
        </div>
    );
}
