import React, { useState } from 'react';
import { blockBuilder } from '../blocks/BlockBuilder';
import { translator } from '../../../services/translator';

export function BlockView(props) {
    const [displayDeleteConfirm, setDisplayDeleteConfirm] = useState(false);

    return (
        <div>
            {props.dragHandle}
            <div className="form-block-type">{translator.trans('form.types.' + props.block.type)}</div>
            <div className="form-block-view">
                {props.focused
                    ? blockBuilder.createFocusedView(props.block, props.onChange)
                    : blockBuilder.createDefaultView(props.block)}
            </div>
            <div className={'form-block-footer p-2 pt-0 text-right ' + (!props.focused ? 'd-none' : '')}>
                {displayDeleteConfirm ? (
                    <span>
                        <button
                            type="button"
                            onClick={() => props.onDelete(props.block)}
                            className="btn btn-sm btn-secondary text-danger ml-2"
                        >
                            {translator.trans('form.confirm')}
                        </button>

                        <button
                            type="button"
                            onClick={() => setDisplayDeleteConfirm(false)}
                            className="btn btn-sm btn-secondary ml-2"
                        >
                            {translator.trans('form.cancel')}
                        </button>
                    </span>
                ) : (
                    <div>
                        <button
                            type="button"
                            onClick={() => props.onDuplicate(props.block)}
                            className="btn btn-sm btn-secondary ml-2"
                        >
                            {translator.trans('form.duplicate')}
                        </button>

                        <button
                            type="button"
                            onClick={() => setDisplayDeleteConfirm(true)}
                            className="btn btn-sm btn-secondary text-danger ml-2"
                        >
                            {translator.trans('form.delete')}
                        </button>
                    </div>
                )}
            </div>
        </div>
    );
}
