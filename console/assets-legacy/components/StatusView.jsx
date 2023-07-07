import React from 'react';
import { translator } from '../services/translator';

const statuses = {
    saving: {
        class: 'text-muted',
        icon: 'fal fa-circle-notch fa-spin',
        text: 'status_view.saving',
    },
    saved: {
        class: 'text-success',
        icon: 'fal fa-check',
        text: 'status_view.saved',
    },
    error: {
        class: 'text-danger',
        icon: 'fal fa-times',
        text: 'status_view.error',
    },
};

export function StatusView(props) {
    return (
        <span className={statuses[props.status].class}>
            <i className={'mr-2 ' + statuses[props.status].icon}></i>
            {translator.trans(statuses[props.status].text)}
        </span>
    );
}
