import React from 'react';
import { Controller } from 'stimulus';
import { render } from 'react-dom';
import { ProposalEditor } from './components/ProposalEditor';

export default class extends Controller {
    static targets = ['title', 'content', 'status', 'statusDescription', 'statusCtaText', 'statusCtaUrl'];

    connect() {
        render(
            <ProposalEditor
                endpoint={this.element.getAttribute('data-endpoint')}
                titleInput={this.titleTarget.getAttribute('name')}
                contentInput={this.contentTarget.getAttribute('name')}
                statusInput={this.statusTarget.getAttribute('name')}
                statusDescriptionInput={this.statusDescriptionTarget.getAttribute('name')}
                statusCtaTextInput={this.statusCtaTextTarget.getAttribute('name')}
                statusCtaUrlInput={this.statusCtaUrlTarget.getAttribute('name')}
            />,
            this.element
        );
    }
}
