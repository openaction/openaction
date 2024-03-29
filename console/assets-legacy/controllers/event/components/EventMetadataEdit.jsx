import React, { useEffect, useState } from 'react';
import { httpClient, createUrlEncoded } from '../../../services/http-client';
import { translator } from '../../../services/translator';
import { exposedDataReader } from '../../../services/exposed-data-reader';
import { Modal } from '../../../components/Modal';
import { StatusView } from '../../../components/StatusView';
import { MetadataEditor } from './MetadataEditor';
import { PublishedAtView } from '../../../components/PublishedAtView';
import { PublicationEditor } from '../../../components/PublicationEditor';
import { createDate } from '../../../services/create-date';
import { SocialSharer } from '../../../components/SocialSharer/SocialSharer';

// Data not handled by React, manually watched
const input = document.querySelector('#event_title');
let eventTitle = input ? input.value : null;

export function EventMetadataEdit(props) {
    const [metadata, setMetadata] = useState(exposedDataReader.read('event_metadata'));
    const [publishModalOpened, setPublishModalOpened] = useState(false);
    const [metadataModalOpened, setMetadataModalOpened] = useState(false);
    const [status, setStatus] = useState('saved');
    const [shareModalOpened, setShareModalOpened] = useState(false);
    const [shareUrl, setShareUrl] = useState(exposedDataReader.read('endpoints')['shareUrl']);

    function saveMetadata(metadata) {
        setMetadata(metadata);
        setStatus('saving');

        httpClient
            .post(
                exposedDataReader.read('endpoints')['metadata'],
                createUrlEncoded({
                    [props.externalUrlInput]: metadata.externalUrl ? metadata.externalUrl : '',
                    [props.publishedAtInput]: metadata.publishedAt ? metadata.publishedAt : '',
                    [props.categoriesInput]: JSON.stringify(metadata.categoryIds ? metadata.categoryIds : []),
                    [props.participantsInput]: JSON.stringify(metadata.participantsIds ? metadata.participantsIds : []),
                    [props.onlyForMembersInput]: metadata.onlyForMembers ? 1 : 0,
                })
            )
            .then((response) => {
                setStatus('saved');
                setShareUrl(response.data.share_url);
            })
            .catch(() => setStatus('error'));
    }

    function uploadImage(file) {
        setStatus('saving');

        let formData = new FormData();
        formData.append(props.imageInput, file);

        httpClient
            .post(exposedDataReader.read('endpoints')['image'], formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            })
            .then((res) => {
                setStatus('saved');
                setMetadata({ ...metadata, image: res.data.image });
            })
            .catch(() => setStatus('error'));
    }

    // Register listener on title
    useEffect(() => {
        const input = document.querySelector('#update_event_title');
        eventTitle = input.value;

        input.addEventListener('input', (e) => {
            eventTitle = e.target.value;
        });
    }, []);

    return (
        <div>
            <div className="row align-items-center justify-content-end">
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
                        {translator.trans('event.edit.update_metadata')}
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
                            ? translator.trans('event.edit.unpublish')
                            : translator.trans('event.edit.publish')}
                    </button>

                    {props.features.sharer && metadata.publishedAt ? (
                        <button
                            type="button"
                            className="btn btn-primary btn-sm ml-2"
                            onClick={() => setShareModalOpened(true)}
                        >
                            <i className="fad fa-share-alt mr-2"></i>
                            {translator.trans('event.edit.social_share')}
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
                title={translator.trans('event.edit.metadata_modal.title')}
                footer={
                    <div className="modal-footer">
                        <StatusView status={status} />

                        <button
                            type="button"
                            className="btn btn-secondary ml-3"
                            onClick={() => setMetadataModalOpened(false)}
                        >
                            {translator.trans('event.edit.metadata_modal.close')}
                        </button>
                    </div>
                }
            >
                <MetadataEditor
                    metadata={metadata}
                    onMetadataChange={(metadata) => saveMetadata(metadata)}
                    uploadStatus={status}
                    fieldCategory="id"
                    fieldParticipant="id"
                    onImageChange={(e) => uploadImage(e)}
                />
            </Modal>

            <Modal
                opened={publishModalOpened}
                large={false}
                onClose={() => setPublishModalOpened(false)}
                title={translator.trans('post.edit.publish_modal.title')}
            >
                <PublicationEditor
                    publishedAt={metadata.publishedAt ? createDate(metadata.publishedAt) : null}
                    onChange={(newDate) => {
                        saveMetadata({ ...metadata, publishedAt: newDate ? newDate.format() : '' });
                        setPublishModalOpened(false);

                        if (newDate) {
                            setShareModalOpened(true);
                        }
                    }}
                />
            </Modal>

            <Modal
                opened={shareModalOpened}
                large={true}
                noPadding={true}
                onClose={() => setShareModalOpened(false)}
                title={translator.trans('event.edit.social_share_modal.title')}
                footer={
                    <div className="modal-footer">
                        <button
                            type="button"
                            className="btn btn-secondary ml-3"
                            onClick={() => setShareModalOpened(false)}
                        >
                            {translator.trans('event.edit.social_share_modal.close')}
                        </button>
                    </div>
                }
            >
                <SocialSharer
                    url={shareUrl}
                    title={eventTitle}
                    description={metadata.description}
                    imageUrl={metadata.image}
                />
            </Modal>
        </div>
    );
}
