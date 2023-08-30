import React, { useEffect } from 'react';

export function Modal(props) {
    useEffect(() => {
        if (props.opened) {
            document.body.classList.add('modal-open');
        } else {
            document.body.classList.remove('modal-open');
        }
    });

    return (
        <div>
            {props.opened ? <div className="modal-backdrop show"></div> : ''}

            <div
                className={'modal' + (props.opened ? ' show' : '')}
                id={props.id}
                aria-labelledby={props.id + '-title'}
                tabIndex="-1"
                role="dialog"
                aria-hidden="true"
                onClick={() => {
                    if (!props.disableBackdropClose) {
                        props.onClose();
                    }
                }}
            >
                <div className={'modal-dialog' + (props.large ? ' modal-lg' : '')} role="document">
                    <div className="modal-content" onClick={(e) => e.stopPropagation()}>
                        <div className="modal-header">
                            <h5 className="modal-title" id={props.id + '-title'}>
                                {props.title}
                            </h5>

                            <button
                                type="button"
                                className="close"
                                data-dismiss={props.id}
                                onClick={() => props.onClose()}
                            >
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div className={'modal-body' + (props.noPadding ? ' p-0' : '')}>{props.children}</div>

                        {props.footer ? props.footer : ''}
                    </div>
                </div>
            </div>
        </div>
    );
}
