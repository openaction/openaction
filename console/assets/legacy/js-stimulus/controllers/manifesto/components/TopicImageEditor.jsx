import React, { useEffect, useState } from 'react';
import { translator } from '../../../services/translator';
import { exposedDataReader } from '../../../services/exposed-data-reader';
import { httpClient } from '../../../services/http-client';

export function TopicImageEditor(props) {
    const [image, setImage] = useState(props.image);
    const [status, setStatus] = useState('saved');

    useEffect(() => {
        window.topicImage = image;
    }, []);

    const uploadImage = (file) => {
        setStatus('saving');

        let formData = new FormData();
        formData.append(props.input, file);

        httpClient
            .post(exposedDataReader.read('endpoints')['image'], formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            })
            .then((res) => {
                setStatus('saved');
                setImage(res.data.image);
                window.topicImage = res.data.image;
            })
            .catch(() => setStatus('error'));
    };

    const handleImageChange = (e) => {
        if (typeof e.target.files[0] !== 'undefined') {
            uploadImage(e.target.files[0]);
        }
    };

    let uploaderViewStyle = { cursor: status === 'saving' ? 'default' : 'pointer' };
    if (image && status !== 'saving') {
        uploaderViewStyle.backgroundImage = 'url(' + image + ')';
    }

    return (
        <div className="content-metadata-image mb-1">
            <div
                className="content-metadata-image-view content-metadata-image-view-narrow text-center"
                style={uploaderViewStyle}
            >
                {!image ? (
                    status === 'saving' ? (
                        <div>
                            <div className="mb-2">
                                <i className="fal fa-circle-notch fa-spin"></i>
                            </div>
                            {translator.trans('manifesto.topic.image.uploading')}
                        </div>
                    ) : (
                        <div>
                            <div className="mb-2">
                                <i className="fal fa-cloud-upload"></i>
                            </div>
                            {translator.trans('manifesto.topic.image.placeholder')}
                        </div>
                    )
                ) : (
                    ''
                )}
            </div>

            <input type="file" className="content-metadata-image-input" onChange={handleImageChange} />
        </div>
    );
}
