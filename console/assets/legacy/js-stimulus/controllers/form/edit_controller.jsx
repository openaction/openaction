import React from 'react';
import { render } from 'react-dom';
import { Controller } from 'stimulus';
import { FormBlocksEditor } from './components/FormBlocksEditor';
import { StatusView } from '../../components/StatusView';

export default class extends Controller {
    static targets = ['statusBar', 'content'];

    connect() {
        const data = JSON.parse(this.element.getAttribute('data-form'));

        this.refreshStatus('saved');

        render(
            <FormBlocksEditor
                data={data}
                refreshStatus={(status) => this.refreshStatus(status)}
                updateUrl={this.contentTarget.getAttribute('data-update-url')}
                isPetition={this.contentTarget.getAttribute('data-is-petition') === '1'}
            />,
            this.contentTarget
        );
    }

    refreshStatus(status) {
        render(<StatusView status={status} />, this.statusBarTarget);
    }
}
