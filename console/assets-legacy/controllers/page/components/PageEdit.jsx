import React, { useEffect, useState } from 'react';
import { httpClient, createUrlEncoded } from '../../../services/http-client';
import { translator } from '../../../services/translator';
import { exposedDataReader } from '../../../services/exposed-data-reader';
import { Modal } from '../../../components/Modal';
import { StatusView } from '../../../components/StatusView';
import { MetadataEditor } from './MetadataEditor';
import { SocialSharer } from '../../../components/SocialSharer/SocialSharer';

let contentSaveTimeout = null;

// Data not handled by React, manually watched
let postTitle = null;
let postContent = null;

export function PageEdit(props) {
    const [metadata, setMetadata] = useState(exposedDataReader.read('page_metadata'));
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
                    [props.categoriesInput]: JSON.stringify(metadata.categoryIds ? metadata.categoryIds : []),
                    [props.parentIdInput]: metadata.parentId ? metadata.parentId : null,
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
        const input = document.getElementById('page-title');
        postTitle = input.value;

        input.addEventListener('input', (e) => {
            postTitle = e.target.value;
            saveContent();
        });

        // Listen on content
        postContent = props.editor.getHtml();

        props.editor.onChange((saveImages) => {
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
                <div className="col-auto">
                    <button
                        type="button"
                        className="btn btn-secondary btn-sm"
                        onClick={() => setMetadataModalOpened(true)}
                    >
                        <i className="fad fa-cogs mr-2"></i>
                        {translator.trans('page.edit.update_metadata')}
                    </button>

                    {props.features.sharer ? (
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
                title={translator.trans('page.edit.metadata_modal.title')}
                footer={
                    <div className="modal-footer">
                        <StatusView status={status} />

                        <button
                            type="button"
                            className="btn btn-secondary ml-3"
                            onClick={() => setMetadataModalOpened(false)}
                        >
                            {translator.trans('page.edit.metadata_modal.close')}
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
                title={translator.trans('page.edit.social_share_modal.title')}
                footer={
                    <div className="modal-footer">
                        <button
                            type="button"
                            className="btn btn-secondary ml-3"
                            onClick={() => setShareModalOpened(false)}
                        >
                            {translator.trans('page.edit.social_share_modal.close')}
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
