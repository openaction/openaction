import React from 'react';
import { Controller } from 'stimulus';
import { render } from 'react-dom';
import { MembershipMainPageEdit } from './components/MembershipMainPageEdit';

export default class extends Controller {
    static targets = ['content'];

    connect() {
        render(<MembershipMainPageEdit contentInput={this.contentTarget.getAttribute('name')} />, this.element);
    }
}
