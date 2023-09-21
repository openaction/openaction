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
import { SocialSharer } from '../../../components/SocialSharer/SocialSharer';

let contentSaveTimeout = null;

// Data not handled by React, manually watched
const input = document.querySelector('#post-title');
let postTitle = input ? input.value : null;
let postContent = null;

export function PostEdit(props) {
    const [metadata, setMetadata] = useState(exposedDataReader.read('post_metadata'));
    const [publishModalOpened, setPublishModalOpened] = useState(false);
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
                        [props.titleInput]: postTitle ? postTitle : '',
                        [props.contentInput]: postContent ? postContent : '',
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
                    [props.externalUrlInput]: metadata.externalUrl ? metadata.externalUrl : '',
                    [props.quoteInput]: metadata.quote ? metadata.quote : '',
                    [props.videoInput]: metadata.video ? metadata.video : '',
                    [props.publishedAtInput]: metadata.publishedAt ? metadata.publishedAt : '',
                    [props.categoriesInput]: JSON.stringify(metadata.categoryIds ? metadata.categoryIds : []),
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
        const input = document.querySelector('#post-title');
        postTitle = input.value;

        input.addEventListener('input', (e) => {
            postTitle = e.target.value;
            saveContent();
        });

        // Listen on content
        postContent = window.editors['post-editor'].getHtml();

        window.editors['post-editor'].onChange((saveImages) => {
            setStatus('saving');

            saveImages((html) => {
                postContent = html;
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
                        {translator.trans('post.edit.update_metadata')}
                    </button>

                    <button
                        type="button"
                        className={'btn btn-sm ml-2 ' + (metadata.publishedAt ? 'btn-secondary' : 'btn-primary')}
                        onClick={() => setPublishModalOpened(true)}
                    >
                        <i className={'mr-2 far ' + (metadata.publishedAt ? 'fa-eye-slash' : 'fa-eye')}></i>
                        {metadata.publishedAt
                            ? translator.trans('post.edit.unpublish')
                            : translator.trans('post.edit.publish')}
                    </button>

                    {props.features.sharer && metadata.publishedAt ? (
                        <button
                            type="button"
                            className="btn btn-primary btn-sm ml-2"
                            onClick={() => setShareModalOpened(true)}
                        >
                            <i className="fad fa-share-alt mr-2"></i>
                            {translator.trans('post.edit.social_share')}
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
                title={translator.trans('post.edit.metadata_modal.title')}
                footer={
                    <div className="modal-footer">
                        <StatusView status={status} />

                        <button
                            type="button"
                            className="btn btn-secondary ml-3"
                            onClick={() => setMetadataModalOpened(false)}
                        >
                            {translator.trans('post.edit.metadata_modal.close')}
                        </button>
                    </div>
                }
            >
                <MetadataEditor
                    metadata={metadata}
                    videoValue={props.videoValue}
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
                title={translator.trans('post.edit.social_share_modal.title')}
                footer={
                    <div className="modal-footer">
                        <button
                            type="button"
                            className="btn btn-secondary ml-3"
                            onClick={() => setShareModalOpened(false)}
                        >
                            {translator.trans('post.edit.social_share_modal.close')}
                        </button>
                    </div>
                }
            >
                <SocialSharer
                    url={shareUrl}
                    title={postTitle}
                    description={metadata.description}
                    imageUrl={metadata.image}
                />
            </Modal>
        </div>
    );
}
