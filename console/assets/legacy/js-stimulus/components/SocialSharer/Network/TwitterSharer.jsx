import React, { useState } from 'react';
import TextareaAutosize from 'react-textarea-autosize';
import { translator } from '../../../services/translator';

export function TwitterSharer(props) {
    const [shareText, setShareText] = useState(
        (props.metadata.title || '') + '\n\n' + (props.metadata.description || '')
    );

    let link = 'https://twitter.com/intent/tweet';
    link += '?url=' + encodeURIComponent(props.metadata.url);
    link += '&text=' + encodeURIComponent(shareText + '\n\n');

    return (
        <div className="social-sharer-post social-sharer-twitter">
            <TextareaAutosize
                minRows={2}
                value={shareText}
                onChange={(e) => setShareText(e.target.value)}
                className="social-sharer-twitter-text"
            />

            <div className="social-sharer-twitter-length">{shareText.length} / 240</div>

            <div className="social-sharer-twitter-preview">
                {props.metadata.imageUrl ? (
                    <div
                        className="social-sharer-twitter-preview-image"
                        style={{ backgroundImage: 'url(' + props.metadata.imageUrl + ')' }}
                    ></div>
                ) : (
                    ''
                )}

                <div className="p-3">
                    <div className="social-sharer-twitter-preview-title">{props.metadata.title}</div>

                    <div className="social-sharer-twitter-preview-description">{props.metadata.description}</div>

                    <div className="social-sharer-twitter-preview-url">{props.metadata.host}</div>
                </div>
            </div>

            <div className="pt-3 text-center">
                <a href={link} className="btn btn-primary btn-block" target="_blank" rel="noopener noreferrer">
                    {translator.trans('social_sharer.finalize.twitter')}
                </a>
            </div>
        </div>
    );
}
