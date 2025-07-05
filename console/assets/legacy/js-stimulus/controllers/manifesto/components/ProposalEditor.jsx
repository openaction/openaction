import React, { useEffect, useState } from 'react';
import { httpClient, createUrlEncoded } from '../../../services/http-client';
import { translator } from '../../../services/translator';
import { exposedDataReader } from '../../../services/exposed-data-reader';
import { Modal } from '../../../components/Modal';
import { StatusView } from '../../../components/StatusView';
import { ProposalMetadataEditor } from './ProposalMetadataEditor';

let saveTimeout = null;

// Data not handled by React, manually watched
const input = document.querySelector('#proposal-title');
let proposalTitle = input ? input.value : null;
let proposalContent = null;

export function ProposalEditor(props) {
    const [metadata, setMetadata] = useState(exposedDataReader.read('proposal_metadata'));
    const [metadataModalOpened, setMetadataModalOpened] = useState(false);
    const [status, setStatus] = useState('saved');

    // Save helpers
    function save(metadata) {
        setMetadata(metadata);

        if (saveTimeout) {
            clearTimeout(saveTimeout);
        }

        saveTimeout = setTimeout(() => {
            setStatus('saving');

            httpClient
                .post(
                    props.endpoint,
                    createUrlEncoded({
                        [props.titleInput]: proposalTitle ? proposalTitle : '',
                        [props.contentInput]: proposalContent ? proposalContent : '',
                        [props.statusInput]: metadata.status ? metadata.status : '',
                        [props.statusDescriptionInput]: metadata.statusDescription ? metadata.statusDescription : '',
                        [props.statusCtaTextInput]: metadata.statusCtaText ? metadata.statusCtaText : '',
                        [props.statusCtaUrlInput]: metadata.statusCtaUrl ? metadata.statusCtaUrl : '',
                    })
                )
                .then(() => setStatus('saved'))
                .catch(() => setStatus('error'));
        }, 700);
    }

    // Register listeners on content and title to save on change
    useEffect(() => {
        // Listen on title
        const input = document.querySelector('#proposal-title');
        proposalTitle = input.value;

        input.addEventListener('input', (e) => {
            proposalTitle = e.target.value;
            save(metadata);
        });

        // Listen on content
        proposalContent = window.editors['proposal-editor'].getHtml();

        window.editors['proposal-editor'].onChange((saveImages) => {
            setStatus('saving');

            saveImages((html) => {
                proposalContent = html;
                save(metadata);
            });
        });
    }, []);

    return (
        <div>
            <div className="row align-items-center justify-content-end">
                <div className="col-auto">
                    <StatusView status={status} />
                </div>
                <div className="col-auto">
                    <button
                        type="button"
                        className="btn btn-secondary btn-sm"
                        onClick={() => setMetadataModalOpened(true)}
                    >
                        <i className="fad fa-cogs mr-2"></i>
                        {translator.trans('manifesto.proposal.update_metadata')}
                    </button>
                </div>
            </div>

            <Modal
                opened={metadataModalOpened}
                onClose={() => setMetadataModalOpened(false)}
                title={translator.trans('manifesto.proposal.metadata_modal.title')}
                footer={
                    <div className="modal-footer">
                        <StatusView status={status} />

                        <button
                            type="button"
                            className="btn btn-secondary ml-3"
                            onClick={() => setMetadataModalOpened(false)}
                        >
                            {translator.trans('manifesto.proposal.metadata_modal.close')}
                        </button>
                    </div>
                }
            >
                <ProposalMetadataEditor metadata={metadata} onMetadataChange={(metadata) => save(metadata)} />
            </Modal>
        </div>
    );
}
