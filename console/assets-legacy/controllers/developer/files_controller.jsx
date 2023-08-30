import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['tabs', 'editors', 'saveButton', 'form'];

    connect() {
        this.files = {};
        this.timeout = null;

        let firstFile = null;

        // Map tabs and editors
        for (let i in this.tabsTargets) {
            const id = this.tabsTargets[i].getAttribute('data-id');

            if (!firstFile) {
                firstFile = id;
            }

            this.files[id] = {
                tab: this.tabsTargets[i],
                view: null,
                textarea: null,
                editor: null,
            };
        }

        for (let i in this.editorsTargets) {
            const id = this.editorsTargets[i].getAttribute('data-id');

            this.files[id].view = this.editorsTargets[i];
            this.files[id].textarea = this.files[id].view.querySelector('textarea');
            this.files[id].editor = CodeMirror.fromTextArea(this.files[id].textarea, {
                mode: this.files[id].textarea.getAttribute('data-mode'),
                lineNumbers: true,
                smartIndent: false,
                indentUnit: 4,
                dragDrop: false,
            });
        }

        // Active
        this.open(localStorage.getItem('citipo_developer_active_tab') || firstFile);

        // Save button
        this.saveButtonTarget.addEventListener('click', () => {
            this.formTarget.submit();
        });
    }

    onTabClick(event) {
        const id = event.target.getAttribute('data-id');
        this.open(id);
        localStorage.setItem('citipo_developer_active_tab', id);
    }

    open(id) {
        for (let i in this.files) {
            if (id === i) {
                this.files[i].tab.classList.add('developer-editor-files-file-active');
                this.files[i].view.style.display = 'block';
                this.files[i].editor.refresh();
            } else {
                this.files[i].tab.classList.remove('developer-editor-files-file-active');
                this.files[i].view.style.display = 'none';
            }
        }
    }
}
