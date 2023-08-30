import React from 'react';
import { CategoriesCheckbox } from '../../../components/CategoriesCheckbox';
import { translator } from '../../../services/translator';

let saveTimeout = null;

export function MetadataEditor(props) {
    const handleImageChange = (e) => {
        if (typeof e.target.files[0] !== 'undefined') {
            props.onImageChange(e.target.files[0]);
        }
    };

    const handleInputChange = (e, field) => {
        handleMetadataChange(field, e.target.value);
    };

    const handleMetadataChange = (field, value) => {
        if (saveTimeout) {
            clearTimeout(saveTimeout);
        }

        saveTimeout = setTimeout(
            () =>
                props.onMetadataChange({
                    ...props.metadata,
                    [field]: value,
                }),
            700
        );
    };

    let uploaderViewStyle = { cursor: props.uploadStatus === 'saving' ? 'default' : 'pointer' };
    if (props.metadata.image && props.uploadStatus !== 'saving') {
        uploaderViewStyle.backgroundImage = 'url(' + props.metadata.image + ')';
    }

    return (
        <div>
            <div className="row">
                <div className="col-12 col-lg-6">
                    <div className="p-3">
                        <div className="mb-2">
                            <strong>{translator.trans('post.edit.metadata_modal.image.label')}</strong>
                        </div>

                        <div className="content-metadata-image mb-1">
                            <div className="content-metadata-image-view text-center" style={uploaderViewStyle}>
                                {!props.metadata.image ? (
                                    props.uploadStatus === 'saving' ? (
                                        <div>
                                            <div className="mb-2">
                                                <i className="fal fa-circle-notch fa-spin"></i>
                                            </div>
                                            {translator.trans('post.edit.metadata_modal.image.uploading')}
                                        </div>
                                    ) : (
                                        <div>
                                            <div className="mb-2">
                                                <i className="fal fa-cloud-upload"></i>
                                            </div>
                                            {translator.trans('post.edit.metadata_modal.image.placeholder')}
                                        </div>
                                    )
                                ) : (
                                    ''
                                )}
                            </div>

                            <input type="file" className="content-metadata-image-input" onChange={handleImageChange} />
                        </div>

                        <small className="text-muted">{translator.trans('post.edit.metadata_modal.image.help')}</small>
                    </div>

                    <div className="p-3">
                        <div className="mb-2">
                            <strong>{translator.trans('post.edit.metadata_modal.video.label')}</strong>
                        </div>

                        <div className="mb-1">
                            <input
                                type="text"
                                className="form-control"
                                placeholder={translator.trans('post.edit.metadata_modal.video.placeholder')}
                                defaultValue={props.videoValue}
                                onChange={(e) => handleInputChange(e, 'video')}
                            />
                        </div>

                        <small className="text-muted">{translator.trans('post.edit.metadata_modal.video.help')}</small>
                    </div>

                    <div className="p-3">
                        <div className="mb-2">
                            <strong>{translator.trans('post.edit.metadata_modal.categories.label')}</strong>
                        </div>

                        <CategoriesCheckbox
                            items={props.metadata.categories}
                            ids={props.metadata.categoryIds}
                            field={props.fieldCategory}
                            handleCategories={(categories) => handleMetadataChange('categoryIds', categories)}
                        />

                        <small className="text-muted">
                            {translator.trans('post.edit.metadata_modal.categories.help')}
                        </small>
                    </div>

                    <div className="p-3">
                        <div className="custom-control custom-checkbox">
                            <input
                                type="checkbox"
                                className="custom-control-input"
                                defaultChecked={props.metadata.onlyForMembers}
                                onChange={(ev) => handleMetadataChange('onlyForMembers', ev.target.checked)}
                                id="only-for-members"
                            />

                            <label className="custom-control-label" htmlFor="only-for-members">
                                <strong>{translator.trans('post.edit.metadata_modal.onlyForMembers.label')}</strong>
                            </label>
                        </div>

                        <small className="text-muted">
                            {translator.trans('post.edit.metadata_modal.onlyForMembers.help')}
                        </small>
                    </div>
                </div>
                <div className="col-12 col-lg-6">
                    <div className="p-3">
                        <div className="mb-2">
                            <strong>{translator.trans('post.edit.metadata_modal.description.label')}</strong>
                        </div>

                        <div className="mb-1">
                            <textarea
                                className="form-control"
                                rows={5}
                                defaultValue={props.metadata.description}
                                onChange={(e) => handleInputChange(e, 'description')}
                            />
                        </div>

                        <small className="text-muted">
                            {translator.trans('post.edit.metadata_modal.description.help')}
                        </small>
                    </div>

                    <div className="p-3">
                        <div className="mb-2">
                            <strong>{translator.trans('post.edit.metadata_modal.quote.label')}</strong>
                        </div>

                        <div className="mb-1">
                            <textarea
                                className="form-control"
                                rows={3}
                                defaultValue={props.metadata.quote}
                                onChange={(e) => handleInputChange(e, 'quote')}
                            />
                        </div>

                        <small className="text-muted">{translator.trans('post.edit.metadata_modal.quote.help')}</small>
                    </div>
                </div>
            </div>
        </div>
    );
}
