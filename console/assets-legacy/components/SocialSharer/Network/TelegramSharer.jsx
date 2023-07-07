import React, { useState } from 'react';
import TextareaAutosize from 'react-textarea-autosize';
import { translator } from '../../../services/translator';

export function TelegramSharer(props) {
    const [shareText, setShareText] = useState(
        (props.metadata.title || '') + '\n\n' + (props.metadata.description || '')
    );

    let link = 'https://telegram.me/share/url';
    link += '?url=' + encodeURIComponent(props.metadata.url);
    link += '&text=' + encodeURIComponent('\n' + shareText);

    return (
        <div className="social-sharer-post social-sharer-telegram">
            <TextareaAutosize
                minRows={2}
                value={shareText}
                onChange={(e) => setShareText(e.target.value)}
                className="social-sharer-telegram-text"
            />

            <div className="social-sharer-telegram-preview">
                <div className="mb-2">
                    <div className="social-sharer-telegram-preview-title">{props.metadata.title}</div>

                    <div className="social-sharer-telegram-preview-description">{props.metadata.description}</div>
                </div>

                {props.metadata.imageUrl ? (
                    <div
                        className="social-sharer-telegram-preview-image"
                        style={{ backgroundImage: 'url(' + props.metadata.imageUrl + ')' }}
                    ></div>
                ) : (
                    ''
                )}
            </div>

            <div className="pt-3 text-center">
                <a href={link} className="btn btn-primary btn-block" target="_blank" rel="noopener noreferrer">
                    {translator.trans('social_sharer.finalize.telegram')}
                </a>
            </div>
        </div>
    );
}
