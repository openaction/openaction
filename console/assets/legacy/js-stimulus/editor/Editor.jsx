export class Editor {
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

            // Override default paste behavior to always handle new lines in paste
            if (!window.localStorage.getItem('_pasteresult')) {
                window.localStorage.setItem('_pasteresult', 'html-without-styles');
            }

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
