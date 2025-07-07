import React, { useState, useEffect } from 'react';
import { translator } from '../../../services/translator';
import { Modal } from '../../../components/Modal';
import { AreaSelector } from '../../../components/AreaSelector';
import { isAreaAlreadySelected } from '../../../services/areas';
import { createUrlEncoded, httpClient } from '../../../services/http-client';
import { exposedDataReader } from '../../../services/exposed-data-reader';
import { TagSelector } from '../../../components/TagSelector';
import { cache } from '../../../services/cache';

export function FilterConfigurator(props) {
    /*
     * Filter main state
     */
    const [state, setState] = useState({
        type: props.type,
        areas: props.areas ? JSON.parse(props.areas) : {},
        tags: props.tags ? JSON.parse(props.tags) : [],
        tagsType: props.tagsType,
        contacts: props.contacts ? JSON.parse(props.contacts) : [],
    });

    const addArea = (area) => {
        // If this area or one of its parent is already present, do not add it
        if (isAreaAlreadySelected(state.areas, area)) {
            return false;
        }

        let areas = state.areas;
        areas[area.id] = area;
        setState({
            type: state.type,
            areas: areas,
            tags: state.tags,
            tagsType: state.tagsType,
            contacts: state.contacts,
        });

        return true;
    };

    const removeArea = (area) => {
        if (typeof state.areas[area.id + ''] == 'undefined') {
            return false;
        }

        let areas = state.areas;
        delete areas[area.id];
        setState({
            type: state.type,
            areas: areas,
            tags: state.tags,
            tagsType: state.tagsType,
            contacts: state.contacts,
        });

        return true;
    };

    /*
     * Contacts count preview for the currently configured filter
     */
    const [countPreview, setCountPreview] = useState(null);
    const [countPreviewLoading, setCountPreviewLoading] = useState(true);

    const refreshCount = (state) => {
        const query = createUrlEncoded({
            areas: Object.keys(state.areas).join(' '),
            tags: state.tags.map((t) => t.id).join(' '),
            tagsType: state.tagsType,
            contacts: state.contacts.join(' '),
            member: state.type === 'member' ? '1' : '0',
        });

        if (cache.has('filter-' + query)) {
            setCountPreview(cache.get('filter-' + query));

            return;
        }

        setCountPreviewLoading(true);
        httpClient.get(exposedDataReader.read('filter_preview_url') + '?' + query).then((res) => {
            setCountPreviewLoading(false);
            setCountPreview(typeof res.data.count !== 'undefined' ? res.data.count : '-');
            cache.set('filter-' + query, res.data.count);
        });
    };

    // Refresh count on mount and on filter state update
    useEffect(() => refreshCount(state), [state]);

    /*
     * Area search state
     */
    const [areaModalOpened, setAreaModalOpened] = useState(false);
    const [areaError, setAreaError] = useState(null);

    /*
     * Tag search state
     */
    const [tagModalOpened, setTagModalOpened] = useState(false);
    const [tagError, setTagError] = useState(null);

    /*
     * Contacts input state
     */
    const [contactsModalOpened, setContactsModalOpened] = useState(false);
    let filterTimeout = null;

    return (
        <div>
            <div className="d-flex mb-3">
                <div className="text-muted">{translator.trans('phoning.filter_preview')}</div>

                <div className="ml-2">
                    {countPreviewLoading ? (
                        <i className="fal fa-circle-notch fa-spin"></i>
                    ) : (
                        <span>{countPreview}</span>
                    )}
                </div>
            </div>

            <div className="px-2">
                <div className={'world-block p-2 mb-2 ' + (state.type !== 'none' ? 'world-block-disabled' : '')}>
                    <div className="row align-items-center">
                        <div className="col-lg-4">
                            <div className="custom-control custom-radio">
                                <input
                                    type="radio"
                                    id="filter-none"
                                    checked={state.type === 'none'}
                                    onChange={() =>
                                        setState({ type: 'none', areas: {}, tags: [], tagsType: 'or', contacts: [] })
                                    }
                                    className="custom-control-input"
                                />

                                <label
                                    className="custom-control-label h6 font-weight-light mb-0"
                                    htmlFor="filter-none"
                                    style={{ paddingTop: 3 }}
                                >
                                    {translator.trans('phoning.none.label')}
                                </label>
                            </div>
                        </div>

                        <div className="col-lg-4">
                            <div className="text-muted">
                                <small>{translator.trans('phoning.none.help')}</small>
                            </div>
                        </div>
                    </div>
                </div>

                {props.features.members ? (
                    <div className={'world-block p-2 mb-2 ' + (state.type !== 'member' ? 'world-block-disabled' : '')}>
                        <div className="row align-items-center">
                            <div className="col-lg-4">
                                <div className="custom-control custom-radio">
                                    <input
                                        type="radio"
                                        id="filter-member"
                                        checked={state.type === 'member'}
                                        onChange={() =>
                                            setState({
                                                type: 'member',
                                                areas: {},
                                                tags: [],
                                                tagsType: 'or',
                                                contacts: [],
                                            })
                                        }
                                        className="custom-control-input"
                                    />

                                    <label
                                        className="custom-control-label h6 font-weight-light mb-0"
                                        htmlFor="filter-member"
                                        style={{ paddingTop: 3 }}
                                    >
                                        {translator.trans('phoning.member.label')}
                                    </label>
                                </div>
                            </div>

                            <div className="col-lg-4">
                                <div className="text-muted">
                                    <small>{translator.trans('phoning.member.help')}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                ) : (
                    ''
                )}

                {props.features.areas ? (
                    <div className={'world-block p-2 mb-2 ' + (state.type !== 'area' ? 'world-block-disabled' : '')}>
                        <div className="row align-items-center">
                            <div className="col-lg-4">
                                <div className="custom-control custom-radio">
                                    <input
                                        type="radio"
                                        id="filter-areas"
                                        checked={state.type === 'area'}
                                        onChange={() =>
                                            setState({
                                                type: 'area',
                                                areas: {},
                                                tags: [],
                                                tagsType: 'or',
                                                contacts: [],
                                            })
                                        }
                                        className="custom-control-input"
                                    />

                                    <label
                                        className="custom-control-label h6 font-weight-light mb-0"
                                        htmlFor="filter-areas"
                                        style={{ paddingTop: 3 }}
                                    >
                                        {translator.trans('phoning.area.label')}
                                    </label>
                                </div>
                            </div>

                            <div className="col-lg-4">
                                <div className="text-muted">
                                    <small>{translator.trans('phoning.area.help')}</small>
                                </div>
                            </div>

                            <div className="col-lg-4">
                                <div className="d-flex justify-content-lg-end">
                                    {Object.keys(state.areas).length === 0 ? (
                                        ''
                                    ) : (
                                        <div>
                                            {Object.keys(state.areas).map((id) => (
                                                <div className="p-2 d-inline-block mr-4" key={id}>
                                                    <i className="fad fa-map-marked-alt mr-2"></i>
                                                    <span>{state.areas[id].name}</span>
                                                </div>
                                            ))}
                                        </div>
                                    )}

                                    <button
                                        type="button"
                                        className="btn btn-sm btn-secondary"
                                        disabled={state.type !== 'area'}
                                        onClick={() => setAreaModalOpened(true)}
                                    >
                                        <i className="fad fa-map-marked-alt mr-2" />
                                        {translator.trans('phoning.area.configure')}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                ) : (
                    ''
                )}

                {props.features.tags ? (
                    <div className={'world-block p-2 mb-2 ' + (state.type !== 'tag' ? 'world-block-disabled' : '')}>
                        <div className="row align-items-center">
                            <div className="col-lg-4">
                                <div className="custom-control custom-radio">
                                    <input
                                        type="radio"
                                        id="filter-tags"
                                        checked={state.type === 'tag'}
                                        onChange={() =>
                                            setState({ type: 'tag', areas: {}, tags: [], tagsType: 'or', contacts: [] })
                                        }
                                        className="custom-control-input"
                                    />

                                    <label
                                        className="custom-control-label h6 font-weight-light mb-0"
                                        htmlFor="filter-tags"
                                        style={{ paddingTop: 3 }}
                                    >
                                        {translator.trans('phoning.tag.label')}
                                    </label>
                                </div>
                            </div>

                            <div className="col-lg-4">
                                <div className="text-muted">
                                    <small>{translator.trans('phoning.tag.help')}</small>
                                </div>
                            </div>

                            <div className="col-lg-4">
                                <div className="d-flex justify-content-lg-end">
                                    {state.tags.length !== 0 ? (
                                        <div>
                                            {state.tags.map((tag) => (
                                                <div className="p-2 d-inline-block mr-4" key={tag.id}>
                                                    <i className="fad fa-tag mr-2" />
                                                    <span>{tag.name}</span>
                                                </div>
                                            ))}
                                        </div>
                                    ) : (
                                        ''
                                    )}

                                    <button
                                        type="button"
                                        className="btn btn-sm btn-secondary"
                                        disabled={state.type !== 'tag'}
                                        onClick={() => setTagModalOpened(true)}
                                    >
                                        <i className="fad fa-tags mr-2" />
                                        {translator.trans('phoning.tag.configure')}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                ) : (
                    ''
                )}

                {props.features.specific ? (
                    <div className={'world-block p-2 mb-2 ' + (state.type !== 'contact' ? 'world-block-disabled' : '')}>
                        <div className="row align-items-center">
                            <div className="col-lg-4">
                                <div className="custom-control custom-radio">
                                    <input
                                        type="radio"
                                        id="filter-contacts"
                                        checked={state.type === 'contact'}
                                        onChange={() =>
                                            setState({
                                                type: 'contact',
                                                areas: {},
                                                tags: [],
                                                tagsType: 'or',
                                                contacts: [],
                                            })
                                        }
                                        className="custom-control-input"
                                    />

                                    <label
                                        className="custom-control-label h6 font-weight-light mb-0"
                                        htmlFor="filter-contacts"
                                        style={{ paddingTop: 3 }}
                                    >
                                        {translator.trans('phoning.contact.label')}
                                    </label>
                                </div>
                            </div>

                            <div className="col-lg-4">
                                <div className="text-muted">
                                    <small>{translator.trans('phoning.contact.help')}</small>
                                </div>
                            </div>

                            <div className="col-lg-4 text-lg-right">
                                <button
                                    type="button"
                                    className="btn btn-sm btn-secondary"
                                    disabled={state.type !== 'contact'}
                                    onClick={() => setContactsModalOpened(true)}
                                >
                                    <i className="fad fa-address-book mr-2" />
                                    {translator.trans('phoning.contact.configure')}
                                </button>
                            </div>
                        </div>
                    </div>
                ) : (
                    ''
                )}
            </div>

            <div className="d-none">
                <input
                    type="hidden"
                    key="field_onlyForMembers"
                    name={props.onlyForMembersInput}
                    value={state.type === 'member'}
                />
                <input type="hidden" key="field_areas" name={props.areasInput} value={JSON.stringify(state.areas)} />
                <input type="hidden" key="field_tags" name={props.tagsInput} value={JSON.stringify(state.tags)} />
                <input type="hidden" key="field_tags_type" name={props.tagsTypeInput} value={state.tagsType} />
                <input
                    type="hidden"
                    key="field_contacts"
                    name={props.contactsInput}
                    value={JSON.stringify(state.contacts)}
                />
            </div>

            <Modal
                opened={areaModalOpened}
                large={true}
                onClose={() => setAreaModalOpened(false)}
                title={translator.trans('phoning.area.modal.title')}
                footer={
                    <div className="modal-footer">
                        <button type="button" className="btn btn-secondary" onClick={() => setAreaModalOpened(false)}>
                            {translator.trans('phoning.area.modal.save')}
                        </button>
                    </div>
                }
            >
                <div className="text-muted mb-3">{translator.trans('phoning.area.modal.description')}</div>

                <div className="p-2">
                    <AreaSelector
                        areas={state.areas}
                        onAreaSelected={(area) => {
                            if (!addArea(area)) {
                                setAreaError(translator.trans('area_selector.add.already_included'));
                            }
                        }}
                        onAreaRemoved={(area) => removeArea(area)}
                        onChange={() => setAreaError(null)}
                    />

                    {areaError ? <div className="text-error mt-4">{areaError}</div> : ''}
                </div>
            </Modal>

            <Modal
                opened={tagModalOpened}
                disableBackdropClose={true}
                onClose={() => setTagModalOpened(false)}
                title={translator.trans('phoning.tag.modal.title')}
                footer={
                    <div className="modal-footer">
                        <button type="button" className="btn btn-secondary" onClick={() => setTagModalOpened(false)}>
                            {translator.trans('phoning.tag.modal.save')}
                        </button>
                    </div>
                }
            >
                <div className="text-muted mb-3">{translator.trans('phoning.tag.modal.description')}</div>

                <div className="p-2">
                    <TagSelector
                        tags={state.tags}
                        allowNew={false}
                        onChange={(tags) => {
                            setState({
                                type: state.type,
                                areas: state.areas,
                                tags: tags,
                                tagsType: state.tagsType,
                                contacts: state.contacts,
                            });

                            setTagError(null);
                        }}
                    />

                    {tagError ? <div className="text-error mt-4">{tagError}</div> : ''}

                    <div className="mt-3">
                        <div className="custom-control custom-radio mb-1">
                            <input
                                type="radio"
                                id="tags-type-or"
                                className="custom-control-input"
                                checked={state.tagsType === 'or'}
                                onChange={(e) => {
                                    setState({
                                        type: state.type,
                                        areas: state.areas,
                                        tags: state.tags,
                                        tagsType: e.target.checked ? 'or' : 'and',
                                        contacts: state.contacts,
                                    });
                                }}
                            />

                            <label className="custom-control-label mb-0" htmlFor="tags-type-or">
                                {translator.trans('phoning.tag.types.or')}
                            </label>
                        </div>

                        <div className="custom-control custom-radio mb-1">
                            <input
                                type="radio"
                                id="tags-type-and"
                                className="custom-control-input"
                                checked={state.tagsType === 'and'}
                                onChange={(e) => {
                                    setState({
                                        type: state.type,
                                        areas: state.areas,
                                        tags: state.tags,
                                        tagsType: e.target.checked ? 'and' : 'or',
                                        contacts: state.contacts,
                                    });
                                }}
                            />

                            <label className="custom-control-label mb-0" htmlFor="tags-type-and">
                                {translator.trans('phoning.tag.types.and')}
                            </label>
                        </div>
                    </div>
                </div>
            </Modal>

            <Modal
                opened={contactsModalOpened}
                onClose={() => setContactsModalOpened(false)}
                title={translator.trans('phoning.contact.modal.title')}
                footer={
                    <div className="modal-footer">
                        <button
                            type="button"
                            className="btn btn-secondary"
                            onClick={() => setContactsModalOpened(false)}
                        >
                            {translator.trans('phoning.contact.modal.save')}
                        </button>
                    </div>
                }
            >
                <div className="text-muted mb-3">{translator.trans('phoning.contact.modal.description')}</div>

                <div className="p-2">
                    <textarea
                        onChange={(contacts) => {
                            if (filterTimeout) {
                                clearTimeout(filterTimeout);
                            }
                            let emails = contacts.target.value.split('\n');
                            filterTimeout = setTimeout(
                                () =>
                                    setState({
                                        type: state.type,
                                        areas: state.areas,
                                        tags: state.tags,
                                        tagsType: state.tagsType,
                                        contacts: emails,
                                    }),
                                1500
                            );
                        }}
                        className="form-control"
                        rows="10"
                        defaultValue={state.contacts ? state.contacts.join('\n') : ''}
                        placeholder={translator.trans('phoning.contact.modal.placeholder')}
                    />
                </div>
            </Modal>
        </div>
    );
}
