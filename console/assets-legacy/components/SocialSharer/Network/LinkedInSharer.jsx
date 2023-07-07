import React from 'react';
import { translator } from '../../../services/translator';

export function LinkedInSharer(props) {
    let link = 'https://www.linkedin.com/sharing/share-offsite/';
    link += '?url=' + encodeURIComponent(props.metadata.url);

    return (
        <div className="social-sharer-post social-sharer-linkedin">
            <div className="social-sharer-linkedin-preview">
                {props.metadata.imageUrl ? (
                    <div
                        className="social-sharer-linkedin-preview-image"
                        style={{ backgroundImage: 'url(' + props.metadata.imageUrl + ')' }}
                    ></div>
                ) : (
                    ''
                )}

                <div className="p-2">
                    <div className="social-sharer-linkedin-preview-title">{props.metadata.title}</div>

                    <div className="social-sharer-linkedin-preview-url">{props.metadata.host}</div>
                </div>
            </div>

            <div className="pt-3 text-center">
                <a href={link} className="btn btn-primary btn-block" target="_blank" rel="noopener noreferrer">
                    {translator.trans('social_sharer.finalize.linkedin')}
                </a>
            </div>
        </div>
    );
}
