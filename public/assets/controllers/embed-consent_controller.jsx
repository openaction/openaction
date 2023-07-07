import React from 'react';
import { render } from 'react-dom';
import { Controller } from '@hotwired/stimulus';
import {EmbedConsent} from './components/EmbedConsent';

export default class extends Controller {
    static values = {
        enableConsent: Boolean,
        type: String,
        url: String,
        titleLabel: String,
        descriptionLabel: String,
        acceptLabel: String,
        externalLabel: String,
        cancelLabel: String,
    };

    connect() {
        render(
            <EmbedConsent
                enableConsent={this.hasEnableConsentValue ? this.enableConsentValue : true}
                type={this.typeValue}
                url={this.urlValue}
                htmlContent={this.element.innerHTML}
                titleLabel={this.titleLabelValue}
                descriptionLabel={this.descriptionLabelValue}
                acceptLabel={this.acceptLabelValue}
                externalLabel={this.externalLabelValue}
                cancelLabel={this.cancelLabelValue}
                containerWidth={this.element.clientWidth}
            />,
            this.element
        )
    }
}
