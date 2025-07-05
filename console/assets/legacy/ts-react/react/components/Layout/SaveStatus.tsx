import React from 'react';

export type SaveStatusName = 'saved' | 'saving' | 'error';

export interface SaveStatusLabels {
    saved: string;
    saving: string;
    error: string;
}

interface Props {
    status: SaveStatusName;
    labels: SaveStatusLabels;
}

const STATUSES = {
    saving: {
        class: 'text-muted',
        icon: 'fal fa-circle-notch fa-spin',
    },
    saved: {
        class: 'text-success',
        icon: 'fal fa-check',
    },
    error: {
        class: 'text-danger',
        icon: 'fal fa-times',
    },
};

export function SaveStatus(props: Props) {
    return (
        <span className={STATUSES[props.status].class}>
            <i className={'mr-2 ' + STATUSES[props.status].icon} />
            {props.labels[props.status]}
        </span>
    );
}
