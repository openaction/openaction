import React, { useState } from 'react';
import { translator } from '../../../services/translator';
import { Modal } from '../../../components/Modal';
import { AreaSelector } from '../../../components/AreaSelector';
import { isAreaAlreadySelected } from '../../../services/areas';
import { TagSelector } from '../../../components/TagSelector';

function encodeAreas(areas) {
    const keys = Object.keys(areas);

    let payload = {};
    for (let i in keys) {
        payload[keys[i]] = true;
    }

    return JSON.stringify(payload);
}

export function AreaChooser(props) {
    const [state, setState] = useState({
        type: props.type,
        areas: props.areas ? JSON.parse(props.areas) : {},
        tags: props.tags ? JSON.parse(props.tags) : [],
    });

    const [areasModalOpened, setAreasModalOpened] = useState(false);
    const [tagsModalOpened, setTagsModalOpened] = useState(false);
    const [error, setError] = useState(null);

    const onAreaSelected = (area) => {
        // If this area or one of its parent is already present, do not add it
        if (isAreaAlreadySelected(state.areas, area)) {
            setError(translator.trans('area_selector.add.already_included'));

            return;
        }

        let areas = state.areas;
        areas[area.id] = area;
        setState({ type: state.type, areas: areas, tags: state.tags });
    };

    const onAreaRemoved = (area) => {
        if (typeof state.areas[area.id + ''] == 'undefined') {
            return;
        }

        let areas = state.areas;
        delete areas[area.id];
        setState({ type: state.type, areas: areas, tags: state.tags });
    };

    return (
        <div>
            <div className="font-weight-bold">{translator.trans('organization.create-project.area.label')}</div>

            <div className="text-muted mb-3">{translator.trans('organization.create-project.area.help')}</div>

            <div className="row">
                <div className={'col-12 col-lg-4 ' + (state.type !== 'global' ? 'world-block-disabled' : '')}>
                    <div className="world-block p-3 mb-3">
                        <div className="custom-control custom-radio">
                            <input
                                type="radio"
                                id="type-global"
                                checked={state.type === 'global'}
                                onChange={() => setState({ type: 'global', areas: state.areas, tags: state.tags })}
                                className="custom-control-input"
                            />

                            <label className="custom-control-label h5 font-weight-light mb-0" htmlFor="type-global">
                                {translator.trans('organization.create-project.area.global.label')}
                            </label>
                        </div>

                        <div className="text-muted mt-2">
                            {translator.trans('organization.create-project.area.global.help')}
                        </div>
                    </div>
                </div>

                <div className={'col-12 col-lg-4 ' + (state.type !== 'local' ? 'world-block-disabled' : '')}>
                    <div className="world-block p-3 mb-3">
                        <div className="custom-control custom-radio">
                            <input
                                type="radio"
                                id="type-local"
                                checked={state.type === 'local'}
                                onChange={() => setState({ type: 'local', areas: state.areas, tags: state.tags })}
                                className="custom-control-input"
                            />

                            <label className="custom-control-label h5 font-weight-light mb-0" htmlFor="type-local">
                                {translator.trans('organization.create-project.area.local.label')}
                            </label>
                        </div>

                        <div className="text-muted mt-2">
                            {translator.trans('organization.create-project.area.local.help')}
                        </div>

                        {Object.keys(state.areas).length === 0 ? (
                            ''
                        ) : (
                            <div className="mt-3">
                                {Object.keys(state.areas).map((id) => (
                                    <div className="p-1" key={id}>
                                        <i className="fad fa-map-marked-alt mr-2"></i>
                                        <span>{state.areas[id].name}</span>
                                    </div>
                                ))}
                            </div>
                        )}

                        <button
                            type="button"
                            className="btn btn-secondary mt-2"
                            disabled={state.type !== 'local'}
                            onClick={() => setAreasModalOpened(true)}
                        >
                            <i className="fad fa-map-marked-alt mr-2" />
                            {translator.trans('organization.create-project.area.local.configure')}
                        </button>
                    </div>
                </div>

                <div className={'col-12 col-lg-4 ' + (state.type !== 'thematic' ? 'world-block-disabled' : '')}>
                    <div className="world-block p-3 mb-3">
                        <div className="custom-control custom-radio">
                            <input
                                type="radio"
                                id="type-thematic"
                                checked={state.type === 'thematic'}
                                onChange={() => setState({ type: 'thematic', areas: state.areas, tags: state.tags })}
                                className="custom-control-input"
                            />

                            <label className="custom-control-label h5 font-weight-light mb-0" htmlFor="type-thematic">
                                {translator.trans('organization.create-project.area.thematic.label')}
                            </label>
                        </div>

                        <div className="text-muted mt-2">
                            {translator.trans('organization.create-project.area.thematic.help')}
                        </div>

                        {Object.keys(state.tags).length === 0 ? (
                            ''
                        ) : (
                            <div className="mt-3">
                                {Object.keys(state.tags).map((id) => (
                                    <div className="p-1" key={id}>
                                        <i className="fad fa-flag mr-2"></i>
                                        <span>{state.tags[id].name}</span>
                                    </div>
                                ))}
                            </div>
                        )}

                        <button
                            type="button"
                            className="btn btn-secondary mt-2"
                            disabled={state.type !== 'thematic'}
                            onClick={() => setTagsModalOpened(true)}
                        >
                            <i className="fad fa-map-marked-alt mr-2" />
                            {translator.trans('organization.create-project.area.thematic.configure')}
                        </button>
                    </div>
                </div>
            </div>

            <div className="d-none">
                <input type="hidden" name={props.typeInput} value={state.type} />
                <input type="hidden" name={props.areasInput} value={encodeAreas(state.areas)} />
                <input type="hidden" name={props.tagsInput} value={JSON.stringify(state.tags)} />
            </div>

            <Modal
                opened={areasModalOpened}
                large={true}
                onClose={() => setAreasModalOpened(false)}
                title={translator.trans('organization.create-project.area.local.modal.title')}
                footer={
                    <div className="modal-footer">
                        <button type="button" className="btn btn-secondary" onClick={() => setAreasModalOpened(false)}>
                            {translator.trans('organization.create-project.area.local.modal.save')}
                        </button>
                    </div>
                }
            >
                <div className="text-muted mb-3">
                    {translator.trans('organization.create-project.area.local.modal.description')}
                </div>

                <div className="p-2">
                    <AreaSelector
                        areas={state.areas}
                        onAreaSelected={onAreaSelected}
                        onAreaRemoved={onAreaRemoved}
                        onChange={() => setError(null)}
                    />

                    {error ? <div className="text-error mt-4">{error}</div> : ''}
                </div>
            </Modal>

            <Modal
                opened={tagsModalOpened}
                large={true}
                onClose={() => setTagsModalOpened(false)}
                title={translator.trans('organization.create-project.area.thematic.modal.title')}
                footer={
                    <div className="modal-footer">
                        <button type="button" className="btn btn-secondary" onClick={() => setTagsModalOpened(false)}>
                            {translator.trans('organization.create-project.area.thematic.modal.save')}
                        </button>
                    </div>
                }
            >
                <div className="text-muted mb-3">
                    {translator.trans('organization.create-project.area.thematic.modal.description')}
                </div>

                <div className="p-2">
                    <TagSelector
                        tags={state.tags}
                        onChange={(tags) => {
                            setError(null);
                            setState({ type: state.type, tags: tags, areas: state.areas });
                        }}
                    />

                    {error ? <div className="text-error mt-4">{error}</div> : ''}
                </div>
            </Modal>
        </div>
    );
}
