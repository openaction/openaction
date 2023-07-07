import { Controller } from 'stimulus';
import React from 'react';
import { render } from 'react-dom';
import { ReportList } from './grid/ReportList';

export default class extends Controller {
    connect() {
        render(<ReportList endpoint={this.element.getAttribute('data-endpoint')} />, this.element);
    }
}
