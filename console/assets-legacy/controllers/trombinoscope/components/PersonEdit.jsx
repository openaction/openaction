import React, { useEffect, useState } from 'react';
import { httpClient, createUrlEncoded } from '../../../services/http-client';
import { translator } from '../../../services/translator';
import { exposedDataReader } from '../../../services/exposed-data-reader';
import { createDate } from '../../../services/create-date';
import { Modal } from '../../../components/Modal';
import { StatusView } from '../../../components/StatusView';
import { PublishedAtView } from '../../../components/PublishedAtView';
import { PublicationEditor } from '../../../components/PublicationEditor';
import { MetadataEditor } from './MetadataEditor';

let contentSaveTimeout = null;

// Data not handled by React, manually watched
let personFullName = null;
let personContent = null;

export function PersonEdit(props) {
    const [metadata, setMetadata] = useState(exposedDataReader.read('person_metadata'));
    const [publishModalOpened, setPublishModalOpened] = useState(false);
    const [metadataModalOpened, setMetadataModalOpened] = useState(false);
    const [status, setStatus] = useState('saved');
    const [uploadStatus, setUploadStatus] = useState('saved');

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
                        [props.fullNameInput]: personFullName ? personFullName : '',
                        [props.contentInput]: personContent ? personContent : '',
                    })
                )
                .then(() => setStatus('saved'))
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
                    [props.roleInput]: metadata.role ? metadata.role : '',
                    [props.descriptionInput]: metadata.description ? metadata.description : '',
                    [props.publishedAtInput]: metadata.publishedAt ? metadata.publishedAt : '',
                    [props.socialWebsiteInput]: metadata.socialWebsite ? metadata.socialWebsite : '',
                    [props.socialEmailInput]: metadata.socialEmail ? metadata.socialEmail : '',
                    [props.socialFacebookInput]: metadata.socialFacebook ? metadata.socialFacebook : '',
                    [props.socialTwitterInput]: metadata.socialTwitter ? metadata.socialTwitter : '',
                    [props.socialInstagramInput]: metadata.socialInstagram ? metadata.socialInstagram : '',
                    [props.socialLinkedInInput]: metadata.socialLinkedIn ? metadata.socialLinkedIn : '',
                    [props.socialYoutubeInput]: metadata.socialYoutube ? metadata.socialYoutube : '',
                    [props.socialMediumInput]: metadata.socialMedium ? metadata.socialMedium : '',
                    [props.socialTelegramInput]: metadata.socialTelegram ? metadata.socialTelegram : '',
                    [props.socialBlueskyInput]: metadata.socialBluesky ? metadata.socialBluesky : '',
                    [props.socialMastodonInput]: metadata.socialMastodon ? metadata.socialMastodon : '',
                    [props.categoriesInput]: JSON.stringify(metadata.categoryIds ? metadata.categoryIds : []),
                })
            )
            .then(() => setStatus('saved'))
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
        const input = document.querySelector('#person-title');
        personFullName = input.value;

        input.addEventListener('input', (e) => {
            personFullName = e.target.value;
            saveContent();
        });

        // Listen on content
        personContent = props.editor.getHtml();

        props.editor.onChange((saveImages) => {
            setStatus('saving');

            saveImages((html) => {
                personContent = html;
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
                <div className="col-auto font-italic">
                    <PublishedAtView date={metadata.publishedAt ? createDate(metadata.publishedAt) : null} />
                </div>
                <div className="col-auto">
                    <button
                        type="button"
                        className="btn btn-secondary btn-sm"
                        onClick={() => setMetadataModalOpened(true)}
                    >
                        <i className="fad fa-cogs mr-2"></i>
                        {translator.trans('trombinoscope.edit.update_metadata')}
                    </button>

                    <button
                        type="button"
                        className={
                            'btn btn-sm ml-2 publish-button ' + (metadata.publishedAt ? 'btn-secondary' : 'btn-primary')
                        }
                        onClick={() => setPublishModalOpened(true)}
                    >
                        <i className={'mr-2 far ' + (metadata.publishedAt ? 'fa-eye-slash' : 'fa-eye')}></i>
                        {metadata.publishedAt
                            ? translator.trans('trombinoscope.edit.unpublish')
                            : translator.trans('trombinoscope.edit.publish')}
                    </button>
                </div>
            </div>

            <Modal
                opened={metadataModalOpened}
                large={true}
                onClose={() => setMetadataModalOpened(false)}
                title={translator.trans('trombinoscope.edit.metadata_modal.title')}
                footer={
                    <div className="modal-footer">
                        <StatusView status={status} />

                        <button
                            type="button"
                            className="btn btn-secondary ml-3"
                            onClick={() => setMetadataModalOpened(false)}
                        >
                            {translator.trans('trombinoscope.edit.metadata_modal.close')}
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
                opened={publishModalOpened}
                large={false}
                onClose={() => setPublishModalOpened(false)}
                title={translator.trans('trombinoscope.edit.publish_modal.title')}
            >
                <PublicationEditor
                    publishedAt={metadata.publishedAt ? createDate(metadata.publishedAt) : null}
                    onChange={(newDate) => {
                        saveMetadata({ ...metadata, publishedAt: newDate ? newDate.format() : '' });
                        setPublishModalOpened(false);
                    }}
                />
            </Modal>
        </div>
    );
}
