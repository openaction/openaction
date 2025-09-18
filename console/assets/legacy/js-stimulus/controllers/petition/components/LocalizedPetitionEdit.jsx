import React, { useEffect, useState } from 'react';
import { httpClient, createUrlEncoded } from '../../../services/http-client';
import { translator } from '../../../services/translator';
import { exposedDataReader } from '../../../services/exposed-data-reader';
import { createDate } from '../../../services/create-date';
import { Modal } from '../../../components/Modal';
import { StatusView } from '../../../components/StatusView';
import { PublishedAtView } from '../../../components/PublishedAtView';
import { PublicationEditor } from '../../../components/PublicationEditor';
import { CategoriesCheckbox } from '../../../components/CategoriesCheckbox';
import { AuthorsSelector } from '../../post/components/AuthorsSelector';
import { SocialSharer } from '../../../components/SocialSharer/SocialSharer';
import { IMaskInput } from 'react-imask';

let contentSaveTimeout = null;

const titleInputNode = document.querySelector('#petition-title');
let currentTitle = titleInputNode ? titleInputNode.value : null;
let currentContent = null;

export function LocalizedPetitionEdit(props) {
    const [metadata, setMetadata] = useState(exposedDataReader.read('petition_localized_metadata'));
    const [parent, setParent] = useState(exposedDataReader.read('petition_parent_metadata'));
    const [status, setStatus] = useState('saved');
    const [parentErrors, setParentErrors] = useState({});
    const [uploadStatus, setUploadStatus] = useState('saved');
    const [detailsModalOpened, setDetailsModalOpened] = useState(false);
    const [publishModalOpened, setPublishModalOpened] = useState(false);
    const [shareModalOpened, setShareModalOpened] = useState(false);
    const [shareUrl, setShareUrl] = useState(exposedDataReader.read('endpoints')['shareUrl']);

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
                        [props.titleInput]: currentTitle ? currentTitle : '',
                        [props.contentInput]: currentContent ? currentContent : '',
                    })
                )
                .then(() => setStatus('saved'))
                .catch(() => setStatus('error'));
        }, 700);
    }

    function saveLocalizedMetadata(data) {
        setMetadata(data);
        setStatus('saving');

        httpClient
            .post(
                exposedDataReader.read('endpoints')['metadata'],
                createUrlEncoded({
                    [props.descriptionInput]: data.description ? data.description : '',
                    [props.submitButtonLabelInput]: data.submitButtonLabel ? data.submitButtonLabel : '',
                    [props.optinLabelInput]: data.optinLabel ? data.optinLabel : '',
                    [props.legalitiesInput]: data.legalities ? data.legalities : '',
                    [props.addressedToInput]: data.addressedTo ? data.addressedTo : '',
                    [props.categoriesInput]: JSON.stringify(data.categoryIds ? data.categoryIds : []),
                })
            )
            .then(() => setStatus('saved'))
            .catch(() => setStatus('error'));
    }

    function saveParentMetadata(data) {
        setParent(data);
        setStatus('saving');

        httpClient
            .post(
                exposedDataReader.read('endpoints')['parent'],
                createUrlEncoded({
                    [props.parentSlugInput]: data.slug ? data.slug : '',
                    [props.parentStartAtInput]: data.startAt ? data.startAt : '',
                    [props.parentEndAtInput]: data.endAt ? data.endAt : '',
                    [props.parentSignaturesGoalInput]: data.signaturesGoal ? data.signaturesGoal : '',
                    [props.parentAuthorsInput]: JSON.stringify(data.authorsIds ? data.authorsIds : []),
                    [props.parentPublishedAtInput]: data.publishedAt ? data.publishedAt : '',
                })
            )
            .then(() => {
                setParentErrors({});
                setStatus('saved');
            })
            .catch((err) => {
                try {
                    const details = err?.response?.data?.details || {};
                    setParentErrors(details);
                } catch (e) {
                    setParentErrors({});
                }
                setStatus('error');
            });
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

    useEffect(() => {
        const input = document.querySelector('#petition-title');
        currentTitle = input.value;
        input.addEventListener('input', (e) => {
            currentTitle = e.target.value;
            saveContent();
        });

        currentContent = props.editor.getHtml();
        props.editor.onChange((saveImages) => {
            setStatus('saving');
            saveImages((html) => {
                currentContent = html;
                saveContent();
            });
        });
    }, []);

    let uploaderViewStyle = { cursor: uploadStatus === 'saving' ? 'default' : 'pointer' };
    if (metadata.image && uploadStatus !== 'saving') {
        uploaderViewStyle.backgroundImage = 'url(' + metadata.image + ')';
    }

    return (
        <div>
            <div className="row align-items-center justify-content-end">
                <div className="col-auto">
                    <StatusView status={status} />
                </div>
                <div className="col-auto font-italic">
                    <PublishedAtView date={parent.publishedAt ? createDate(parent.publishedAt) : null} />
                </div>
                <div className="col-auto">
                    <button
                        type="button"
                        className="btn btn-secondary btn-sm"
                        onClick={() => setDetailsModalOpened(true)}
                    >
                        <i className="fad fa-cogs mr-2"></i>
                        {translator.trans('post.edit.update_metadata')}
                    </button>

                    <button
                        type="button"
                        className={
                            'btn btn-sm ml-2 publish-button ' + (parent.publishedAt ? 'btn-secondary' : 'btn-primary')
                        }
                        onClick={() => setPublishModalOpened(true)}
                    >
                        <i className={'mr-2 far ' + (parent.publishedAt ? 'fa-eye-slash' : 'fa-eye')}></i>
                        {parent.publishedAt
                            ? translator.trans('post.edit.unpublish')
                            : translator.trans('post.edit.publish')}
                    </button>

                    {props.features.sharer && parent.publishedAt ? (
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
                opened={detailsModalOpened}
                large={true}
                onClose={() => setDetailsModalOpened(false)}
                title={translator.trans('post.edit.metadata_modal.title')}
                footer={
                    <div className="modal-footer">
                        <StatusView status={status} />
                        <button
                            type="button"
                            className="btn btn-secondary ml-3"
                            onClick={() => setDetailsModalOpened(false)}
                        >
                            {translator.trans('post.edit.metadata_modal.close')}
                        </button>
                    </div>
                }
            >
                <h6 className="mb-0">Configuration de la pétition</h6>

                <hr className="my-2" />

                <div className="row">
                    <div className="col-12 col-lg-6">
                        <div className="p-2">
                            <div className="mb-1">{translator.trans('petition.edit.metadata_modal.parent.slug')}</div>
                            <input
                                type="text"
                                className="bp4-input bp4-fill"
                                defaultValue={parent.slug}
                                onChange={(e) => saveParentMetadata({ ...parent, slug: e.target.value })}
                            />
                            {(() => {
                                const slugErrors = parentErrors['slug'] || parentErrors[props.parentSlugInput];
                                return slugErrors ? (
                                    <div className="text-danger mt-2">{slugErrors.join(' ')}</div>
                                ) : null;
                            })()}
                        </div>

                        <div className="p-2">
                            <div className="mb-1">
                                {translator.trans('petition.edit.metadata_modal.parent.signaturesGoal')}
                            </div>
                            <input
                                type="number"
                                className="bp4-input bp4-fill"
                                min="0"
                                defaultValue={parent.signaturesGoal || ''}
                                onChange={(e) => saveParentMetadata({ ...parent, signaturesGoal: e.target.value })}
                            />
                        </div>

                        <div className="p-2">
                            <div className="mb-1">
                                {translator.trans('petition.edit.metadata_modal.parent.authors')}
                            </div>
                            <AuthorsSelector
                                choices={parent.availableAuthors}
                                selectedIds={parent.authorsIds}
                                onChange={(authors) => saveParentMetadata({ ...parent, authorsIds: authors })}
                            />
                        </div>
                    </div>

                    <div className="col-12 col-lg-6">
                        <div className="p-2">
                            <div className="mb-1">
                                {translator.trans('petition.edit.metadata_modal.parent.startAt')}
                            </div>

                            <IMaskInput
                                lazy={false}
                                mask={Date}
                                pattern="DD/MM/YYYY HH:mm"
                                format={(date) => createDate(date).format('DD/MM/YYYY HH:mm')}
                                parse={(str) => createDate(str, 'DD/MM/YYYY HH:mm')}
                                className="form-control"
                                value={parent.startAt ? createDate(parent.startAt).format('DD/MM/YYYY HH:mm') : ''}
                                onInput={(e) => {
                                    if (e.target.value.indexOf('_') === -1) {
                                        saveParentMetadata({
                                            ...parent,
                                            startAt: createDate(e.target.value, 'DD/MM/YYYY HH:mm').format(),
                                        });
                                    }
                                }}
                                blocks={{
                                    YYYY: {
                                        mask: IMask.MaskedRange,
                                        from: 1970,
                                        to: 2030,
                                    },
                                    MM: {
                                        mask: IMask.MaskedRange,
                                        from: 1,
                                        to: 12,
                                    },
                                    DD: {
                                        mask: IMask.MaskedRange,
                                        from: 1,
                                        to: 31,
                                    },
                                    HH: {
                                        mask: IMask.MaskedRange,
                                        from: 0,
                                        to: 23,
                                    },
                                    mm: {
                                        mask: IMask.MaskedRange,
                                        from: 0,
                                        to: 59,
                                    },
                                }}
                            />
                        </div>

                        <div className="p-2">
                            <div className="mb-1">{translator.trans('petition.edit.metadata_modal.parent.endAt')}</div>

                            <IMaskInput
                                lazy={false}
                                mask={Date}
                                pattern="DD/MM/YYYY HH:mm"
                                format={(date) => createDate(date).format('DD/MM/YYYY HH:mm')}
                                parse={(str) => createDate(str, 'DD/MM/YYYY HH:mm')}
                                className="form-control"
                                value={parent.endAt ? createDate(parent.endAt).format('DD/MM/YYYY HH:mm') : ''}
                                onInput={(e) => {
                                    if (e.target.value.indexOf('_') === -1) {
                                        saveParentMetadata({
                                            ...parent,
                                            endAt: createDate(e.target.value, 'DD/MM/YYYY HH:mm').format(),
                                        });
                                    }
                                }}
                                blocks={{
                                    YYYY: {
                                        mask: IMask.MaskedRange,
                                        from: 1970,
                                        to: 2030,
                                    },
                                    MM: {
                                        mask: IMask.MaskedRange,
                                        from: 1,
                                        to: 12,
                                    },
                                    DD: {
                                        mask: IMask.MaskedRange,
                                        from: 1,
                                        to: 31,
                                    },
                                    HH: {
                                        mask: IMask.MaskedRange,
                                        from: 0,
                                        to: 23,
                                    },
                                    mm: {
                                        mask: IMask.MaskedRange,
                                        from: 0,
                                        to: 59,
                                    },
                                }}
                            />
                        </div>
                    </div>
                </div>

                <div className="mt-5">
                    <h6 className="flex items-center gap-2 mb-1">
                        Paramètres de la version
                        <span className={`h-3 fi fi-${metadata['locale'] === 'en' ? 'gb' : metadata['locale']}`}></span>
                    </h6>
                </div>

                <hr className="my-2" />

                <div className="row">
                    <div className="col-12 col-lg-6">
                        <div className="p-1 mb-2">
                            <div className="mb-1">{translator.trans('petition.edit.metadata_modal.image.label')}</div>
                            <div className="content-metadata-image mb-1">
                                <div className="content-metadata-image-view text-center" style={uploaderViewStyle}>
                                    {!metadata.image ? (
                                        uploadStatus === 'saving' ? (
                                            <div>
                                                <div className="mb-2">
                                                    <i className="fal fa-circle-notch fa-spin"></i>
                                                </div>
                                                {translator.trans('petition.edit.metadata_modal.image.uploading')}
                                            </div>
                                        ) : (
                                            <div>
                                                <div className="mb-2">
                                                    <i className="fal fa-cloud-upload"></i>
                                                </div>
                                                {translator.trans('petition.edit.metadata_modal.image.placeholder')}
                                            </div>
                                        )
                                    ) : (
                                        ''
                                    )}
                                </div>
                                <input
                                    type="file"
                                    className="content-metadata-image-input"
                                    onChange={(e) => uploadImage(e.target.files[0])}
                                />
                            </div>
                        </div>

                        <div className="p-1 mb-2">
                            <div className="mb-1">
                                {translator.trans('petition.edit.metadata_modal.categories.label')}
                            </div>
                            <CategoriesCheckbox
                                items={metadata.categories}
                                ids={metadata.categoryIds}
                                field="id"
                                handleCategories={(categories) =>
                                    saveLocalizedMetadata({ ...metadata, categoryIds: categories })
                                }
                            />
                        </div>
                        <div className="p-1">
                            <div className="mb-1">
                                {translator.trans('petition.edit.metadata_modal.addressedTo.label')}
                            </div>
                            <input
                                type="text"
                                className="bp4-input bp4-fill"
                                defaultValue={metadata.addressedTo}
                                onChange={(e) => saveLocalizedMetadata({ ...metadata, addressedTo: e.target.value })}
                            />
                        </div>
                    </div>
                    <div className="col-12 col-lg-6">
                        <div className="p-1 mb-2">
                            <div className="mb-1">
                                {translator.trans('petition.edit.metadata_modal.description.label')}
                            </div>
                            <textarea
                                className="bp4-input bp4-fill"
                                rows={5}
                                defaultValue={metadata.description}
                                onChange={(e) => saveLocalizedMetadata({ ...metadata, description: e.target.value })}
                            />
                        </div>
                        <div className="p-1 mb-2">
                            <div className="mb-1">
                                {translator.trans('petition.edit.metadata_modal.submitButtonLabel.label')}
                            </div>
                            <input
                                type="text"
                                className="bp4-input bp4-fill"
                                placeholder="Je soutiens cette pétition"
                                defaultValue={metadata.submitButtonLabel}
                                onChange={(e) =>
                                    saveLocalizedMetadata({ ...metadata, submitButtonLabel: e.target.value })
                                }
                            />
                        </div>
                        <div className="p-1">
                            <div className="mb-1">
                                {translator.trans('petition.edit.metadata_modal.optinLabel.label')}
                            </div>
                            <textarea
                                className="bp4-input bp4-fill"
                                rows={3}
                                placeholder="J’accepte que mes données soient utilisées par ..."
                                defaultValue={metadata.optinLabel}
                                onChange={(e) => saveLocalizedMetadata({ ...metadata, optinLabel: e.target.value })}
                            />
                        </div>
                        <div className="p-1">
                            <div className="mb-1">
                                {translator.trans('petition.edit.metadata_modal.legalities.label')}
                            </div>
                            <textarea
                                className="bp4-input bp4-fill"
                                rows={3}
                                placeholder="Conditions légales..."
                                defaultValue={metadata.legalities}
                                onChange={(e) => saveLocalizedMetadata({ ...metadata, legalities: e.target.value })}
                            />
                        </div>
                    </div>
                </div>
            </Modal>

            <Modal
                opened={publishModalOpened}
                large={false}
                onClose={() => setPublishModalOpened(false)}
                title={translator.trans('post.edit.publish_modal.title')}
            >
                <PublicationEditor
                    publishedAt={parent.publishedAt ? createDate(parent.publishedAt) : null}
                    onChange={(newDate) => {
                        const next = { ...parent, publishedAt: newDate ? newDate.format() : '' };
                        saveParentMetadata(next);
                        setParent(next);
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
                    title={currentTitle}
                    description={metadata.description}
                    imageUrl={metadata.image}
                />
            </Modal>
        </div>
    );
}
