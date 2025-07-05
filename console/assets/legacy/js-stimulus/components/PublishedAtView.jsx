import React from 'react';
import { translator } from '../services/translator';
import { createDate } from '../services/create-date';

export function PublishedAtView(props) {
    if (!props.date) {
        return translator.trans('published_at_view.draft');
    }

    if (props.date.isBefore(createDate())) {
        return (
            <span>
                {translator.trans('published_at_view.published')}
                {' ' + props.date.fromNow()}
            </span>
        );
    }

    return (
        <span>
            {translator.trans('published_at_view.scheduled')}
            {' ' + props.date.fromNow()}
        </span>
    );
}
