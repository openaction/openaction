import { Controller } from 'stimulus';
import { Uppy } from '@uppy/core';
import * as Dashboard from '@uppy/dashboard';
import * as XHRUpload from '@uppy/xhr-upload';
import { translator } from '../../services/translator';

export default class extends Controller {
    static targets = ['drag'];

    initialize() {
        this.uppy = new Uppy({
            debug: false,
            autoProceed: false,
            allowMultipleUploads: false,
            restrictions: {
                allowedFileTypes: [
                    '.pdf',
                    '.doc',
                    '.docx',
                    '.xls',
                    '.xlsx',
                    '.odt',
                    '.ods',
                    '.png',
                    '.jpg',
                    '.jpeg',
                    '.svg',
                ],
                maxNumberOfFiles: 5,
                minNumberOfFiles: 1,
            },
            locale: {
                strings: translator.trans('uppy'),
            },
        });
    }

    connect() {
        const urlEndpoint = this.element.getAttribute('data-endpoint');
        const inputFile = this.element.getAttribute('data-input-file');

        this.uppy
            .use(Dashboard, {
                height: 350,
                target: this.dragTarget,
                inline: true,
                proudlyDisplayPoweredByUppy: false,
            })
            .use(XHRUpload, {
                endpoint: urlEndpoint,
                fieldName: inputFile,
                headers: {
                    'X-XSRF-TOKEN': window.Citipo.token,
                },
            })
            .on('complete', () => {
                this.element.classList.add('active');
                this.element.classList.remove('inactive');
                location.reload();
            });
    }
}
