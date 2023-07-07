import React, { useEffect, useState } from 'react';
import { httpClient, createUrlEncoded } from '../../../services/http-client';
import { exposedDataReader } from '../../../services/exposed-data-reader';
import { StatusView } from '../../../components/StatusView';

let contentSaveTimeout = null;

// Data not handled by React, manually watched
let pageContent = null;

export function MembershipMainPageEdit(props) {
    const [status, setStatus] = useState('saved');

    // Save helpers
    function saveContent() {
        if (contentSaveTimeout) {
            clearTimeout(contentSaveTimeout);
        }

        contentSaveTimeout = setTimeout(() => {
            setStatus('saving');

            httpClient
                .post(
                    exposedDataReader.read('endpoints')['content'],
                    createUrlEncoded({ [props.contentInput]: pageContent ? pageContent : '' })
                )
                .then(() => setStatus('saved'))
                .catch(() => setStatus('error'));
        }, 700);
    }

    // Register listeners on content and title to save on change
    useEffect(() => {
        // Listen on content
        pageContent = window.editors['page-editor'].getHtml();

        window.editors['page-editor'].onChange((saveImages) => {
            setStatus('saving');

            saveImages((html) => {
                pageContent = html;
                saveContent();
            });
        });
    }, []);

    return (
        <div className="row align-items-center justify-content-end">
            <div className="col-auto">
                <StatusView status={status} />
            </div>
        </div>
    );
}
