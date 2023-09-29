import React from 'react';
import { CategoriesCheckbox } from '../../../components/CategoriesCheckbox';
import { translator } from '../../../services/translator';
import { exposedDataReader } from '../../../services/exposed-data-reader';

let saveTimeout = null;

export function MetadataEditor(props) {
    const handleImageChange = (e) => {
        if (typeof e.target.files[0] !== 'undefined') {
            props.onImageChange(e.target.files[0]);
        }
    };

    const handleDescriptionChange = (e) => {
        if (saveTimeout) {
            clearTimeout(saveTimeout);
        }

        const description = e.target.value;
        saveTimeout = setTimeout(() => props.onMetadataChange({ ...props.metadata, description: description }), 700);
    };

    const handleCategoriesChange = (categories) => {
        if (saveTimeout) {
            clearTimeout(saveTimeout);
        }

        saveTimeout = setTimeout(() => props.onMetadataChange({ ...props.metadata, categoryIds: categories }), 700);
    };

    const handleParentPageChange = (parentId) => {
        if (saveTimeout) {
            clearTimeout(saveTimeout);
        }

        saveTimeout = setTimeout(() => props.onMetadataChange({ ...props.metadata, parentId: parentId || '' }), 700);
    };

    const handleOnlyForMembersChange = (onlyForMembers) => {
        if (saveTimeout) {
            clearTimeout(saveTimeout);
        }

        saveTimeout = setTimeout(
            () => props.onMetadataChange({ ...props.metadata, onlyForMembers: onlyForMembers }),
            700
        );
    };

    let uploaderViewStyle = { cursor: props.uploadStatus === 'saving' ? 'default' : 'pointer' };
    if (props.metadata.image && props.uploadStatus !== 'saving') {
        uploaderViewStyle.backgroundImage = 'url(' + props.metadata.image + ')';
    }

    const availableParentPages = exposedDataReader.read('available_parent_pages');
    console.log(availableParentPages);

    return (
        <div>
            <div className="row">
                <div className="col-12 col-lg-6">
                    <div className="p-3">
                        <div className="mb-2">
                            <strong>{translator.trans('page.edit.metadata_modal.image.label')}</strong>
                        </div>

                        <div className="content-metadata-image mb-1">
                            <div className="content-metadata-image-view text-center" style={uploaderViewStyle}>
                                {!props.metadata.image ? (
                                    props.uploadStatus === 'saving' ? (
                                        <div>
                                            <div className="mb-2">
                                                <i className="fal fa-circle-notch fa-spin"></i>
                                            </div>
                                            {translator.trans('page.edit.metadata_modal.image.uploading')}
                                        </div>
                                    ) : (
                                        <div>
                                            <div className="mb-2">
                                                <i className="fal fa-cloud-upload"></i>
                                            </div>
                                            {translator.trans('page.edit.metadata_modal.image.placeholder')}
                                        </div>
                                    )
                                ) : (
                                    ''
                                )}
                            </div>

                            <input type="file" className="content-metadata-image-input" onChange={handleImageChange} />
                        </div>

                        <small className="text-muted">{translator.trans('page.edit.metadata_modal.image.help')}</small>
                    </div>

                    <div className="p-3">
                        <div className="mb-2">
                            <strong>{translator.trans('page.edit.metadata_modal.categories.label')}</strong>
                        </div>

                        <CategoriesCheckbox
                            items={props.metadata.categories}
                            ids={props.metadata.categoryIds}
                            field={props.fieldCategory}
                            handleCategories={(categories) => handleCategoriesChange(categories)}
                        />

                        <small className="text-muted">
                            {translator.trans('page.edit.metadata_modal.categories.help')}
                        </small>
                    </div>

                    <div className="p-3">
                        <div className="custom-control custom-checkbox">
                            <input
                                type="checkbox"
                                className="custom-control-input"
                                defaultChecked={props.metadata.onlyForMembers}
                                onChange={(ev) => handleOnlyForMembersChange(ev.target.checked)}
                                id="only-for-members"
                            />

                            <label className="custom-control-label" htmlFor="only-for-members">
                                <strong>{translator.trans('page.edit.metadata_modal.onlyForMembers.label')}</strong>
                            </label>
                        </div>

                        <small className="text-muted">
                            {translator.trans('page.edit.metadata_modal.onlyForMembers.help')}
                        </small>
                    </div>
                </div>
                <div className="col-12 col-lg-6">
                    <div className="p-3">
                        <div className="mb-2">
                            <strong>{translator.trans('page.edit.metadata_modal.parentId.label')}</strong>
                        </div>

                        <div className="mb-1">
                            <select
                                className="form-control"
                                defaultValue={props.metadata.parentId}
                                onChange={(e) => handleParentPageChange(e.currentTarget.value)}
                            >
                                <option value={null}></option>

                                {availableParentPages.map((page) => (
                                    <option key={page.id} value={parseInt(page.id)}>
                                        {page.title}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <small className="text-muted">
                            {translator.trans('page.edit.metadata_modal.parentId.help')}
                        </small>
                    </div>

                    <div className="p-3">
                        <div className="mb-2">
                            <strong>{translator.trans('page.edit.metadata_modal.description.label')}</strong>
                        </div>

                        <div className="mb-1">
                            <textarea
                                className="form-control"
                                rows={5}
                                defaultValue={props.metadata.description}
                                onChange={(e) => handleDescriptionChange(e)}
                            />
                        </div>

                        <small className="text-muted">
                            {translator.trans('page.edit.metadata_modal.description.help')}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    );
}
