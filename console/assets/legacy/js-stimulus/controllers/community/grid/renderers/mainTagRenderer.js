import { httpClient } from '../../../../services/http-client';

let inputsByContact = {};

export function createReadOnlyMainTagRenderer() {
    return (params) => {
        if (params.value === undefined) {
            return '<div class="community-contact-loader"><i class="fal fa-circle-notch fa-spin"></i></div>';
        }

        const id = params.column.colId + '-' + params.data.id;

        let html = '';
        html += '<div class="custom-control custom-checkbox">';
        html += '   <input type="checkbox" id="' + id + '" ' + (params.value ? 'checked="checked" ' : '');
        html += '          class="custom-control-input" disabled="disabled" />';
        html += '   <label class="custom-control-label" for="' + id + '"></label>';
        html += '</div>';

        const div = document.createElement('div');
        div.classList.add('community-contact-tag');
        div.innerHTML = html;

        return div;
    };
}

export function createMainTagRenderer(endpoint, isProgress) {
    const persistMainTags = (contactId, inputs) => {
        httpClient.post(
            endpoint.replace('-uuid-', contactId) +
                '?tags=' +
                Object.keys(inputs)
                    .map((k) => (inputs[k].checked ? '1' : '0'))
                    .join(',')
        );
    };

    const syncMainTags = (contactId, tagKey) => {
        const contactInputs = inputsByContact[contactId];

        if (isProgress) {
            const wasChecked = !contactInputs[0].checked;

            if (parseInt(tagKey) === 0 && wasChecked) {
                Object.keys(contactInputs).map((k) => (contactInputs[k].checked = false));
            } else if (parseInt(tagKey) !== 0) {
                Object.keys(contactInputs).map((k) => (contactInputs[k].checked = parseInt(k) <= parseInt(tagKey)));
            }
        }

        persistMainTags(contactId, contactInputs);
    };

    return (params) => {
        if (params.value === undefined) {
            return '<div class="community-contact-loader"><i class="fal fa-circle-notch fa-spin"></i></div>';
        }

        const id = params.column.colId + '-' + params.data.id;

        let html = '';
        html += '<div class="custom-control custom-checkbox">';
        html += '   <input type="checkbox" id="' + id + '" ' + (params.value ? 'checked="checked" ' : '');
        html += '          class="custom-control-input" />';
        html += '   <label class="custom-control-label" for="' + id + '"></label>';
        html += '</div>';

        const div = document.createElement('div');
        div.classList.add('community-contact-tag');
        div.innerHTML = html;

        if (typeof inputsByContact[params.data.id] === 'undefined') {
            inputsByContact[params.data.id] = {};
        }

        const key = params.column.colId.replace('tag', '');

        inputsByContact[params.data.id][key] = div.querySelector('input');
        inputsByContact[params.data.id][key].addEventListener('change', () => {
            syncMainTags(params.data.id, key);
        });

        return div;
    };
}
