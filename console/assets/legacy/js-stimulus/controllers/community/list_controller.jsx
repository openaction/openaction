import { Controller } from 'stimulus';
import React from 'react';
import { render } from 'react-dom';
import { CommunityList } from './grid/CommunityList';

export default class extends Controller {
    connect() {
        render(
            <CommunityList
                mainTags={JSON.parse(this.element.getAttribute('data-main-tags'))}
                isProgress={'1' === this.element.getAttribute('data-progress')}
                readOnly={'1' === this.element.getAttribute('data-read-only')}
                syncTagsEndpoint={this.element.getAttribute('data-sync-tags-endpoint')}
                endpoint={this.element.getAttribute('data-endpoint')}
            />,
            this.element
        );
    }
}
