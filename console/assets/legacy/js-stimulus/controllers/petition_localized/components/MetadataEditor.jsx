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
                            <strong>{translator.trans('petition_localized.edit.metadata_modal.image.label')}</strong>
                        </div>

                        <div className="content-metadata-image mb-1">
                            <div className="content-metadata-image-view text-center" style={uploaderViewStyle}>
                                {!props.metadata.image ? (
                                    props.uploadStatus === 'saving' ? (
                                        <div>
                                            <div className="mb-2">
                                                <i className="fal fa-circle-notch fa-spin"></i>
                                            </div>
                                            {translator.trans('petition_localized.edit.metadata_modal.image.uploading')}
                                        </div>
                                    ) : (
                                        <div>
                                            <div className="mb-2">
                                                <i className="fal fa-cloud-upload"></i>
                                            </div>
                                            {translator.trans(
                                                'petition_localized.edit.metadata_modal.image.placeholder'
                                            )}
                                        </div>
                                    )
                                ) : (
                                    ''
                                )}
                            </div>

                            <input type="file" className="content-metadata-image-input" onChange={handleImageChange} />
                        </div>

                        <small className="text-muted">
                            {translator.trans('petition_localized.edit.metadata_modal.image.help')}
                        </small>
                    </div>

                    <div className="p-3">
                        <div className="mb-2">
                            <strong>
                                {translator.trans('petition_localized.edit.metadata_modal.description.label')}
                            </strong>
                        </div>

                        <div className="mb-1">
                            <textarea
                                className="bp4-input bp4-fill"
                                rows={5}
                                defaultValue={props.metadata.description}
                                onChange={(e) => handleInputChange(e, 'description')}
                            />
                        </div>

                        <small className="text-muted">
                            {translator.trans('petition_localized.edit.metadata_modal.description.help')}
                        </small>
                    </div>

                    <div className="p-3">
                        <div className="mb-2">
                            <strong>
                                {translator.trans('petition_localized.edit.metadata_modal.categories.label')}
                            </strong>
                        </div>

                        <CategoriesCheckbox
                            items={props.metadata.categories}
                            ids={props.metadata.categoryIds}
                            field={props.fieldCategory}
                            handleCategories={(categories) => handleMetadataChange('categoryIds', categories)}
                        />

                        <small className="text-muted">
                            {translator.trans('petition_localized.edit.metadata_modal.categories.help')}
                        </small>
                    </div>
                </div>
                <div className="col-12 col-lg-6">
                    <div className="p-3">
                        <div className="mb-2">
                            <strong>
                                {translator.trans('petition_localized.edit.metadata_modal.addressed_to.label')}
                            </strong>
                        </div>

                        <div className="mb-1">
                            <input
                                className="bp4-input bp4-fill"
                                defaultValue={props.metadata.addressedTo}
                                onChange={(e) => handleInputChange(e, 'addressedTo')}
                            />
                        </div>

                        <small className="text-muted">
                            {translator.trans('petition_localized.edit.metadata_modal.addressed_to.help')}
                        </small>
                    </div>

                    <div className="p-3">
                        <div className="mb-2">
                            <strong>
                                {translator.trans('petition_localized.edit.metadata_modal.submit_button_label.label')}
                            </strong>
                        </div>

                        <div className="mb-1">
                            <input
                                type="text"
                                className="bp4-input bp4-fill"
                                defaultValue={props.metadata.submitButtonLabel}
                                onChange={(e) => handleInputChange(e, 'submitButtonLabel')}
                            />
                        </div>

                        <small className="text-muted">
                            {translator.trans('petition_localized.edit.metadata_modal.submit_button_label.help')}
                        </small>
                    </div>

                    <div className="p-3">
                        <div className="mb-2">
                            <strong>
                                {translator.trans('petition_localized.edit.metadata_modal.optin_label.label')}
                            </strong>
                        </div>

                        <div className="mb-1">
                            <input
                                type="text"
                                className="bp4-input bp4-fill"
                                defaultValue={props.metadata.optinLabel}
                                onChange={(e) => handleInputChange(e, 'optinLabel')}
                            />
                        </div>

                        <small className="text-muted">
                            {translator.trans('petition_localized.edit.metadata_modal.optin_label.help')}
                        </small>
                    </div>

                    <div className="p-3">
                        <div className="mb-2">
                            <strong>
                                {translator.trans('petition_localized.edit.metadata_modal.legalities.label')}
                            </strong>
                        </div>

                        <div className="mb-1">
                            <textarea
                                className="bp4-input bp4-fill"
                                defaultValue={props.metadata.legalities}
                                rows={5}
                                onChange={(e) => handleInputChange(e, 'legalities')}
                            />
                        </div>

                        <small className="text-muted">
                            {translator.trans('petition_localized.edit.metadata_modal.legalities.help')}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    );
}
