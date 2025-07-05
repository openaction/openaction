import React, { useState } from 'react';
import { translator } from '../../../services/translator';
import { Modal } from '../../../components/Modal';
import { AreaSelector } from '../../../components/AreaSelector';
import { isAreaAlreadySelected } from '../../../services/areas';

function encodeAreas(areas) {
    const keys = Object.keys(areas);

    let payload = {};
    for (let i in keys) {
        payload[keys[i]] = true;
    }

    return JSON.stringify(payload);
}

export function SmallAreaChooser(props) {
    const [state, setState] = useState({
        type: props.type,
        areas: props.areas ? JSON.parse(props.areas) : {},
    });

    const [modalOpened, setModalOpened] = useState(false);
    const [error, setError] = useState(null);

    const onAreaSelected = (area) => {
        // If this area or one of its parent is already present, do not add it
        if (isAreaAlreadySelected(state.areas, area)) {
            setError(translator.trans('area_selector.add.already_included'));

            return;
        }

        let areas = state.areas;
        areas[area.id] = area;
        setState({ type: state.type, areas: areas });
    };

    const onAreaRemoved = (area) => {
        if (typeof state.areas[area.id + ''] == 'undefined') {
            return;
        }

        let areas = state.areas;
        delete areas[area.id];
        setState({ type: state.type, areas: areas });
    };

    return (
        <div>
            <div className="row">
                <div className={'col-12 col-lg-4 ' + (state.type !== 'global' ? 'world-block-disabled' : '')}>
                    <div className="custom-control custom-radio">
                        <input
                            type="radio"
                            id={'type-global-' + props.itemKey}
                            checked={state.type === 'global'}
                            onChange={() => setState({ areas: state.areas, type: 'global' })}
                            className="custom-control-input"
                        />

                        <label className="custom-control-label mb-0" htmlFor={'type-global-' + props.itemKey}>
                            {translator.trans('organization.create-project.area.global.label')}
                        </label>
                    </div>
                </div>

                <div className={'col-12 col-lg-8 ' + (state.type !== 'local' ? 'world-block-disabled' : '')}>
                    <div className="custom-control custom-radio">
                        <input
                            type="radio"
                            id={'type-local-' + props.itemKey}
                            checked={state.type === 'local'}
                            onChange={() => setState({ areas: state.areas, type: 'local' })}
                            className="custom-control-input"
                        />

                        <label className="custom-control-label mb-0" htmlFor={'type-local-' + props.itemKey}>
                            {translator.trans('organization.create-project.area.local.label') + ' '}(
                            {Object.keys(state.areas).length})
                        </label>
                    </div>

                    <button
                        type="button"
                        className="btn btn-sm btn-secondary mt-2"
                        disabled={state.type !== 'local'}
                        onClick={() => setModalOpened(true)}
                    >
                        <i className="fad fa-map-marked-alt mr-2" />
                        {translator.trans('organization.create-project.area.local.configure')}
                    </button>
                </div>
            </div>

            <div className="d-none">
                <input type="hidden" name={props.typeInput} value={state.type} />
                <input type="hidden" name={props.areasInput} value={encodeAreas(state.areas)} />
            </div>

            <Modal
                opened={modalOpened}
                large={true}
                onClose={() => setModalOpened(false)}
                title={translator.trans('organization.create-project.area.local.modal.title')}
                footer={
                    <div className="modal-footer">
                        <button type="button" className="btn btn-secondary" onClick={() => setModalOpened(false)}>
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
        </div>
    );
}
