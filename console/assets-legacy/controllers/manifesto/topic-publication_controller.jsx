import React from 'react';
import { Controller } from 'stimulus';
import { render } from 'react-dom';
import { TopicPublicationEditor } from './components/TopicPublicationEditor';

export default class extends Controller {
    static targets = ['input'];

    connect() {
        render(
            <TopicPublicationEditor
                features={{
                    sharer: '1' === this.element.getAttribute('data-sharer'),
                }}
                publishedAt={this.element.getAttribute('data-published-at')}
                input={this.inputTarget.getAttribute('name')}
            />,
            this.element
        );
    }
}
