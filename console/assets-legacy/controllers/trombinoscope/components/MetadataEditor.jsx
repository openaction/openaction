import React from 'react';
import { translator } from '../../../services/translator';
import { CategoriesCheckbox } from '../../../components/CategoriesCheckbox';

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
                            <strong>{translator.trans('trombinoscope.edit.metadata_modal.image.label')}</strong>
                        </div>

                        <div className="content-metadata-image mb-1">
                            <div
                                className="content-metadata-image-view content-metadata-image-view-square text-center"
                                style={uploaderViewStyle}
                            >
                                {!props.metadata.image ? (
                                    props.uploadStatus === 'saving' ? (
                                        <div>
                                            <div className="mb-2">
                                                <i className="fal fa-circle-notch fa-spin"></i>
                                            </div>
                                            {translator.trans('trombinoscope.edit.metadata_modal.image.uploading')}
                                        </div>
                                    ) : (
                                        <div>
                                            <div className="mb-2">
                                                <i className="fal fa-cloud-upload"></i>
                                            </div>
                                            {translator.trans('trombinoscope.edit.metadata_modal.image.placeholder')}
                                        </div>
                                    )
                                ) : (
                                    ''
                                )}
                            </div>

                            <input type="file" className="content-metadata-image-input" onChange={handleImageChange} />
                        </div>

                        <div className="mb-4 text-muted">
                            {translator.trans('trombinoscope.edit.metadata_modal.image.help')}
                        </div>

                        <div className="mb-2">
                            <strong>{translator.trans('trombinoscope.edit.metadata_modal.role.label')}</strong>
                        </div>

                        <div className="mb-1">
                            <input
                                type="text"
                                className="form-control"
                                defaultValue={props.metadata.role}
                                onChange={(e) => handleInputChange(e, 'role')}
                            />
                        </div>

                        <div className="mb-4 text-muted">
                            {translator.trans('trombinoscope.edit.metadata_modal.role.help')}
                        </div>

                        <div className="mb-2">
                            <strong>{translator.trans('trombinoscope.edit.metadata_modal.categories.label')}</strong>
                        </div>

                        <CategoriesCheckbox
                            items={props.metadata.categories}
                            ids={props.metadata.categoryIds}
                            field={props.fieldCategory}
                            handleCategories={(categories) => handleMetadataChange('categoryIds', categories)}
                        />

                        <div className="mb-4 text-muted">
                            {translator.trans('trombinoscope.edit.metadata_modal.categories.help')}
                        </div>
                    </div>
                </div>
                <div className="col-12 col-lg-6">
                    <div className="p-3">
                        <div className="mb-1">
                            <strong>{translator.trans('trombinoscope.edit.metadata_modal.socials.label')}</strong>
                        </div>

                        <div className="mb-2 text-muted">
                            {translator.trans('trombinoscope.edit.metadata_modal.socials.help')}
                        </div>

                        <div className="mb-1">
                            <div>
                                <small className="text-muted">
                                    {translator.trans('trombinoscope.edit.metadata_modal.socials.socialEmail')}
                                </small>
                            </div>

                            <input
                                type="email"
                                className="form-control"
                                defaultValue={props.metadata.socialEmail}
                                onChange={(e) => handleInputChange(e, 'socialEmail')}
                            />
                        </div>

                        <div className="mb-1">
                            <div>
                                <small className="text-muted">
                                    {translator.trans('trombinoscope.edit.metadata_modal.socials.socialFacebook')}
                                </small>
                            </div>

                            <input
                                type="url"
                                className="form-control"
                                defaultValue={props.metadata.socialFacebook}
                                onChange={(e) => handleInputChange(e, 'socialFacebook')}
                            />
                        </div>

                        <div className="mb-1">
                            <div>
                                <small className="text-muted">
                                    {translator.trans('trombinoscope.edit.metadata_modal.socials.socialTwitter')}
                                </small>
                            </div>

                            <input
                                type="url"
                                className="form-control"
                                defaultValue={props.metadata.socialTwitter}
                                onChange={(e) => handleInputChange(e, 'socialTwitter')}
                            />
                        </div>

                        <div className="mb-1">
                            <div>
                                <small className="text-muted">
                                    {translator.trans('trombinoscope.edit.metadata_modal.socials.socialInstagram')}
                                </small>
                            </div>

                            <input
                                type="url"
                                className="form-control"
                                defaultValue={props.metadata.socialInstagram}
                                onChange={(e) => handleInputChange(e, 'socialInstagram')}
                            />
                        </div>

                        <div className="mb-1">
                            <div>
                                <small className="text-muted">
                                    {translator.trans('trombinoscope.edit.metadata_modal.socials.socialLinkedIn')}
                                </small>
                            </div>

                            <input
                                type="url"
                                className="form-control"
                                defaultValue={props.metadata.socialLinkedIn}
                                onChange={(e) => handleInputChange(e, 'socialLinkedIn')}
                            />
                        </div>

                        <div className="mb-1">
                            <div>
                                <small className="text-muted">
                                    {translator.trans('trombinoscope.edit.metadata_modal.socials.socialYoutube')}
                                </small>
                            </div>

                            <input
                                type="url"
                                className="form-control"
                                defaultValue={props.metadata.socialYoutube}
                                onChange={(e) => handleInputChange(e, 'socialYoutube')}
                            />
                        </div>

                        <div className="mb-1">
                            <div>
                                <small className="text-muted">
                                    {translator.trans('trombinoscope.edit.metadata_modal.socials.socialMedium')}
                                </small>
                            </div>

                            <input
                                type="url"
                                className="form-control"
                                defaultValue={props.metadata.socialMedium}
                                onChange={(e) => handleInputChange(e, 'socialMedium')}
                            />
                        </div>

                        <div className="mb-1">
                            <div>
                                <small className="text-muted">
                                    {translator.trans('trombinoscope.edit.metadata_modal.socials.socialTelegram')}
                                </small>
                            </div>

                            <input
                                type="text"
                                className="form-control"
                                defaultValue={props.metadata.socialTelegram}
                                onChange={(e) => handleInputChange(e, 'socialTelegram')}
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
