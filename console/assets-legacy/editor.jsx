// Entrypoint used to load Unlayer/ContentBuilder and wire it with global listeners
// We can't use Stimulus because editors creates too much DOM nodes, leading to performance problems

// Wrapper around Unlayer/ContentBuilder to allow for dynamic update of the change listener

import { CONTENT_EDITOR_EMAIL_OPTIONS, CONTENT_EDITOR_WEB_OPTIONS } from './editor/ContentBuilderOptions';
import { UNLAYER_EMAIL_OPTIONS } from './editor/UnlayerOptions';

class Editor {
    constructor(editorType, nodeId, options, uploadUrl) {
        this._changeListener = () => {};

        if ('unlayer' === editorType) {
            options['id'] = nodeId;

            unlayer.init(options);

            if (options.design || null) {
                unlayer.loadDesign(options.design);
            }

            unlayer.addEventListener('design:updated', () => this._handleUnlayerChange());
            this._handleUnlayerChange();
        } else {
            options['container'] = '#' + nodeId;
            options['onChange'] = () => this._handleCodeBuildChange();

            this._instance = new ContentBuilder(options);
            this._uploadUrl = uploadUrl;
        }
    }

    onChange(listener) {
        this._changeListener = listener;
    }

    getHtml() {
        return this._instance.html();
    }

    getInstance() {
        return this._instance;
    }

    _handleCodeBuildChange() {
        // Strings that should be removed from the content as they are added unexpectedly by the editor
        const extraStrings = ['background-color: rgba(200, 200, 201, 0.11);', 'font-size: 1.07rem;'];

        this._changeListener((cb) => {
            this._instance.saveImages(this._uploadUrl, () => {
                let html = this._instance.html();
                for (let i in extraStrings) {
                    html = html.replace(extraStrings[i], '');
                }

                cb(html);
            });
        });
    }

    _handleUnlayerChange() {
        unlayer.exportHtml((data) => {
            this._changeListener(data.html, data.design);
        });
    }
}

// Expose the built list of editors to allow for changes listening
window.editors = {};

const webEditorNodes = document.querySelectorAll('.editor-content');
for (let i in webEditorNodes) {
    if (!webEditorNodes.hasOwnProperty(i)) {
        continue;
    }

    const id = webEditorNodes[i].getAttribute('id');
    const uploadUrl = webEditorNodes[i].getAttribute('data-upload-url');
    window.editors[id] = new Editor('contentbuilder', id, CONTENT_EDITOR_WEB_OPTIONS, uploadUrl);
}

const emailEditorNodes = document.querySelectorAll('.email-editor-content');
for (let i in emailEditorNodes) {
    if (!emailEditorNodes.hasOwnProperty(i)) {
        continue;
    }

    const node = emailEditorNodes[i];

    const id = node.getAttribute('id');
    const uploadUrl = node.getAttribute('data-upload-url');

    if (node.getAttribute('data-editor-type') === 'unlayer') {
        let options = UNLAYER_EMAIL_OPTIONS;
        options['design'] = JSON.parse(node.getAttribute('data-design'));

        window.editors[id] = new Editor('unlayer', id, options, uploadUrl);
    } else {
        window.editors[id] = new Editor('contentbuilder', id, CONTENT_EDITOR_EMAIL_OPTIONS, uploadUrl);
    }
}
