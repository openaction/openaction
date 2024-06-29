import React, { useEffect, useState } from 'react';
import { httpClient, createUrlEncoded } from '../../../services/http-client';
import { translator } from '../../../services/translator';
import { exposedDataReader } from '../../../services/exposed-data-reader';
import { createDate } from '../../../services/create-date';
import { Modal } from '../../../components/Modal';
import { StatusView } from '../../../components/StatusView';
import { PublicationEditor } from '../../../components/PublicationEditor';
import { MetadataEditor } from './MetadataEditor';
import { SocialSharer } from '../../../components/SocialSharer/SocialSharer';

let contentSaveTimeout = null;

// Data not handled by React, manually watched
const input = document.querySelector('#petition-localized-title');
let mainTitle = input ? input.value : null;
let mainContent = null;

export function PetitionLocalizedEdit(props) {
    const [metadata, setMetadata] = useState(exposedDataReader.read('petition_localized_metadata'));
    const [metadataModalOpened, setMetadataModalOpened] = useState(false);
    const [status, setStatus] = useState('saved');
    const [uploadStatus, setUploadStatus] = useState('saved');
    const [shareModalOpened, setShareModalOpened] = useState(false);
    const [shareUrl, setShareUrl] = useState(exposedDataReader.read('endpoints')['shareUrl']);

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
                    createUrlEncoded({
                        [props.titleInput]: mainTitle ? mainTitle : '',
                        [props.contentInput]: mainContent ? mainContent : '',
                    })
                )
                .then((response) => {
                    setStatus('saved');
                    setShareUrl(response.data.share_url);
                })
                .catch(() => setStatus('error'));
        }, 700);
    }

    function saveMetadata(metadata) {
        setMetadata(metadata);
        setStatus('saving');

        httpClient
            .post(
                exposedDataReader.read('endpoints')['metadata'],
                createUrlEncoded({
                    [props.descriptionInput]: metadata.description ? metadata.description : '',
                    [props.addressedToInput]: metadata.addressedTo ? metadata.addressedTo : '',
                    [props.legalitiesInput]: metadata.legalities ? metadata.legalities : '',
                    [props.submitButtonLabelInput]: metadata.submitButtonLabel ? metadata.submitButtonLabel : '',
                    [props.optinLabelInput]: metadata.optinLabel ? metadata.optinLabel : '',
                    [props.categoriesInput]: JSON.stringify(metadata.categoryIds ? metadata.categoryIds : []),
                })
            )
            .then((response) => {
                setStatus('saved');
                setShareUrl(response.data.share_url);
            })
            .catch(() => setStatus('error'));
    }

    function uploadImage(file) {
        setUploadStatus('saving');

        let formData = new FormData();
        formData.append(props.imageInput, file);

        httpClient
            .post(exposedDataReader.read('endpoints')['image'], formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            })
            .then((res) => {
                setUploadStatus('saved');
                setMetadata({ ...metadata, image: res.data.image });
            })
            .catch(() => setUploadStatus('error'));
    }

    // Register listeners on content and title to save on change
    useEffect(() => {
        // Listen on title
        const input = document.querySelector('#petition-localized-title');
        mainTitle = input.value;

        input.addEventListener('input', (e) => {
            mainTitle = e.target.value;
            saveContent();
        });

        // Listen on content
        mainContent = props.editor.getHtml();

        props.editor.onChange((saveImages) => {
            setStatus('saving');

            saveImages((html) => {
                mainContent = html;
                saveContent();
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
                        {translator.trans('petition_localized.edit.update_metadata')}
                    </button>

                    {props.features.sharer ? (
                        <button
                            type="button"
                            className="btn btn-primary btn-sm ml-2"
                            onClick={() => setShareModalOpened(true)}
                        >
                            <i className="fad fa-share-alt mr-2"></i>
                            {translator.trans('petition_localized.edit.social_share')}
                        </button>
                    ) : (
                        ''
                    )}
                </div>
            </div>

            <Modal
                opened={metadataModalOpened}
                large={true}
                onClose={() => setMetadataModalOpened(false)}
                title={translator.trans('petition_localized.edit.metadata_modal.title')}
                footer={
                    <div className="modal-footer">
                        <StatusView status={status} />

                        <button
                            type="button"
                            className="btn btn-secondary ml-3"
                            onClick={() => setMetadataModalOpened(false)}
                        >
                            {translator.trans('petition_localized.edit.metadata_modal.close')}
                        </button>
                    </div>
                }
            >
                <MetadataEditor
                    metadata={metadata}
                    onMetadataChange={(metadata) => saveMetadata(metadata)}
                    uploadStatus={uploadStatus}
                    fieldCategory="id"
                    onImageChange={(e) => uploadImage(e)}
                />
            </Modal>

            <Modal
                opened={shareModalOpened}
                large={true}
                noPadding={true}
                onClose={() => setShareModalOpened(false)}
                title={translator.trans('petition_localized.edit.social_share_modal.title')}
                footer={
                    <div className="modal-footer">
                        <button
                            type="button"
                            className="btn btn-secondary ml-3"
                            onClick={() => setShareModalOpened(false)}
                        >
                            {translator.trans('petition_localized.edit.social_share_modal.close')}
                        </button>
                    </div>
                }
            >
                <SocialSharer
                    url={shareUrl}
                    title={mainTitle}
                    description={metadata.description}
                    imageUrl={metadata.image}
                />
            </Modal>
        </div>
    );
}
