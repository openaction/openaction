import React from 'react';
import { Controller } from 'stimulus';
import { render } from 'react-dom';
import { StatsView } from './components/StatsView';
import dayjs from 'dayjs';

export default class extends Controller {
    connect() {
        const sentAt = dayjs(this.element.getAttribute('data-sent-at'));

        render(
            <StatsView
                sentAt={sentAt}
                url={this.element.getAttribute('data-url')}
                link={this.element.getAttribute('data-link')}
            />,
            this.element
        );
    }
}
