import React from 'react';
import { Controller } from 'stimulus';
import { render } from 'react-dom';
import { TopicImageEditor } from './components/TopicImageEditor';

export default class extends Controller {
    static targets = ['image'];

    connect() {
        render(
            <TopicImageEditor
                image={this.element.getAttribute('data-image')}
                input={this.imageTarget.getAttribute('name')}
            />,
            this.element
        );
    }
}
