// Entrypoint used to load Unlayer/ContentBuilder and wire it with global listeners
// We can't use Stimulus because editors creates too much DOM nodes, leading to performance problems

// Wrapper around Unlayer/ContentBuilder to allow for dynamic update of the change listener

import { CONTENT_EDITOR_EMAIL_OPTIONS, CONTENT_EDITOR_WEB_OPTIONS } from './editor/ContentBuilderOptions';
import { UNLAYER_EMAIL_OPTIONS } from './editor/UnlayerOptions';
import { Editor } from './editor/Editor';

// Expose the built list of editors to allow for changes listening
if (typeof window.editors === 'undefined') {
    window.editors = {};
}

const webEditorNodes = document.querySelectorAll('.editor-contentbuilder');
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
