import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['rows'];

    addRow() {
        const index = this.element.getAttribute('data-index');
        const widgetPrototype = this.element.getAttribute('data-prototype');

        const html =
            '<div class="row no-gutters align-items-center mb-2">' +
            '    <div class="col-10">' +
            '        ' +
            widgetPrototype.replace(/__name__/g, index) +
            '    </div>' +
            '    <div class="col-2 pl-3">' +
            '        <button type="button" class="btn btn-secondary btn-block text-danger"' +
            '                data-action="community--additional-emails#removeRow">' +
            '            <i class="fal fa-times"></i>' +
            '        </button>' +
            '    </div>' +
            '</div>';

        const row = document.createElement('div');
        row.innerHTML = html;
        this.rowsTarget.appendChild(row);

        this.element.setAttribute('data-index', parseInt(index) + 1);
    }

    removeRow(e) {
        this.rowsTarget.removeChild(e.currentTarget.parentNode.parentNode.parentNode);
    }
}
