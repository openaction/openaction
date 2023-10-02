import React, { useEffect, useState } from 'react';
import { httpClient, createUrlEncoded } from '../../../services/http-client';
import { translator } from '../../../services/translator';
import { exposedDataReader } from '../../../services/exposed-data-reader';
import { Modal } from '../../../components/Modal';
import { PublishedAtView } from '../../../components/PublishedAtView';
import { PublicationEditor } from '../../../components/PublicationEditor';
import { createDate } from '../../../services/create-date';
import { SocialSharer } from '../../../components/SocialSharer/SocialSharer';
import { StatusView } from '../../../components/StatusView';

// Data not handled by React, manually watched
const titleInput = document.querySelector('#manifesto_topic_title');
const descriptionInput = document.querySelector('#manifesto_topic_description');

let titleValue = titleInput ? titleInput.value : null;
let descriptionValue = descriptionInput ? descriptionInput.value : null;

export function TopicPublicationEditor(props) {
    const [publishedAt, setPublishedAt] = useState(props.publishedAt);
    const [publishModalOpened, setPublishModalOpened] = useState(false);
    const [shareModalOpened, setShareModalOpened] = useState(false);
    const [shareUrl, setShareUrl] = useState(exposedDataReader.read('endpoints')['shareUrl']);
    const [status, setStatus] = useState('saved');

    function savePublishedAt(publishedAt) {
        setPublishedAt(publishedAt);
        setStatus('saving');

        httpClient
            .post(
                exposedDataReader.read('endpoints')['metadata'],
                createUrlEncoded({
                    [props.input]: publishedAt ? publishedAt : '',
                })
            )
            .then((response) => {
                setStatus('saved');
                setShareUrl(response.data.share_url);
            })
            .catch(() => setStatus('error'));
    }

    // Register listener on title and description
    useEffect(() => {
        const titleInput = document.querySelector('#manifesto_topic_title');
        const descriptionInput = document.querySelector('#manifesto_topic_description');

        titleValue = titleInput.value;
        descriptionValue = descriptionInput.value;

        titleInput.addEventListener('input', (e) => {
            titleValue = e.target.value;
        });

        descriptionInput.addEventListener('input', (e) => {
            descriptionValue = e.target.value;
        });
    }, []);

    return (
        <div>
            <div className="row align-items-center justify-content-end">
                <div className="col-auto">
                    <StatusView status={status} />
                </div>
                <div className="col-auto font-italic">
                    <PublishedAtView date={publishedAt ? createDate(publishedAt) : null} />
                </div>
                <div className="col-auto">
                    <button
                        type="button"
                        className={'btn btn-sm ml-2 publish-button ' + (publishedAt ? 'btn-secondary' : 'btn-primary')}
                        onClick={() => setPublishModalOpened(true)}
                    >
                        <i className={'mr-2 far ' + (publishedAt ? 'fa-eye-slash' : 'fa-eye')}></i>
                        {publishedAt
                            ? translator.trans('manifesto.topic.unpublish')
                            : translator.trans('manifesto.topic.publish')}
                    </button>

                    {props.features.sharer && publishedAt ? (
                        <button
                            type="button"
                            className="btn btn-primary btn-sm ml-2"
                            onClick={() => setShareModalOpened(true)}
                        >
                            <i className="fad fa-share-alt mr-2"></i>
                            {translator.trans('manifesto.topic.social_share')}
                        </button>
                    ) : (
                        ''
                    )}
                </div>
            </div>

            <Modal
                opened={publishModalOpened}
                large={false}
                onClose={() => setPublishModalOpened(false)}
                title={translator.trans('manifesto.topic.publish_modal.title')}
            >
                <PublicationEditor
                    publishedAt={publishedAt ? createDate(publishedAt) : null}
                    onChange={(newDate) => {
                        savePublishedAt(newDate ? newDate.format() : '');
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
                title={translator.trans('manifesto.topic.social_share_modal.title')}
                footer={
                    <div className="modal-footer">
                        <button
                            type="button"
                            className="btn btn-secondary ml-3"
                            onClick={() => setShareModalOpened(false)}
                        >
                            {translator.trans('manifesto.topic.social_share_modal.close')}
                        </button>
                    </div>
                }
            >
                <SocialSharer
                    url={shareUrl}
                    title={titleValue}
                    description={descriptionValue}
                    imageUrl={window.topicImage}
                />
            </Modal>
        </div>
    );
}
