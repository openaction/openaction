import React, { useState } from 'react';
import TextareaAutosize from 'react-textarea-autosize';
import { translator } from '../../../services/translator';

export function WhatsappSharer(props) {
    const [shareText, setShareText] = useState(
        (props.metadata.title || '') + '\n\n' + (props.metadata.description || '')
    );

    let link = 'https://api.whatsapp.com/send/?phone';
    link += '&text=' + encodeURIComponent(props.metadata.url + '\n\n' + shareText);

    return (
        <div className="social-sharer-post social-sharer-whatsapp">
            <div className="social-sharer-whatsapp-preview">
                <div className="d-flex align-items-center">
                    {props.metadata.imageUrl ? (
                        <div
                            className="social-sharer-whatsapp-preview-image"
                            style={{ backgroundImage: 'url(' + props.metadata.imageUrl + ')' }}
                        ></div>
                    ) : (
                        ''
                    )}

                    <div className="social-sharer-whatsapp-preview-text">
                        <div className="social-sharer-whatsapp-preview-title">{props.metadata.title}</div>

                        <div className="social-sharer-whatsapp-preview-description">{props.metadata.description}</div>

                        <div className="social-sharer-whatsapp-preview-url">{props.metadata.host}</div>
                    </div>
                </div>
            </div>

            <TextareaAutosize
                minRows={2}
                value={shareText}
                onChange={(e) => setShareText(e.target.value)}
                className="social-sharer-whatsapp-text"
            />

            <div className="pt-3 text-center">
                <a href={link} className="btn btn-primary btn-block" target="_blank" rel="noopener noreferrer">
                    {translator.trans('social_sharer.finalize.whatsapp')}
                </a>
            </div>
        </div>
    );
}
