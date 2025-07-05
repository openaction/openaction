import React from 'react';
import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['input'];

    connect() {
        const input = this.inputTarget;

        window.editors['content-editor'].onChange((saveImages) => {
            saveImages((html) => {
                input.value = html;
            });
        });
    }
}
