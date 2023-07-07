import { Controller } from 'stimulus';
import * as Sticky from 'sticky-js';
import { exposedDataReader } from '../../services/exposed-data-reader';

export default class extends Controller {
    static targets = ['isAdmin', 'permissionsInput', 'permissionsContainer', 'permissionAll', 'permissionCheckbox'];

    initialize() {
        this.metadata = {
            permissions: exposedDataReader.read('team_metadata_permissions'),
            projects: exposedDataReader.read('team_metadata_projects'),
            currentPermissions: exposedDataReader.read('team_metadata_currentPermissions'),
        };
    }

    connect() {
        new Sticky('.sticky-top');

        // Populate current permissions
        let data = { ...(this.metadata.currentPermissions || {}) };

        for (const uuid of this.metadata.projects) {
            data[uuid] = data[uuid] || {};

            for (const section in this.metadata.permissions) {
                for (const permission of this.metadata.permissions[section]) {
                    if (!(permission in data[uuid])) {
                        data[uuid][permission] = false;
                    }
                }
            }
        }

        this.metadata.currentPermissions = data;
        this.permissionsInputTarget.value = JSON.stringify(data);

        // Synchronize admin checkbox with permissions display
        this._syncIsAdminWithPermissions();
        this.isAdminTarget.addEventListener('change', () => this._syncIsAdminWithPermissions());

        // Synchronize "check all" checkboxes
        this._syncCheckAllPermissions();

        // Persist permissions changes
        this.permissionCheckboxTargets.forEach((input) => {
            input.addEventListener('change', () => this._persistState());
        });
    }

    _syncIsAdminWithPermissions() {
        if (!this.isAdminTarget.checked) {
            this.permissionAllTargets.forEach((input) => input.removeAttribute('disabled'));
            this.permissionCheckboxTargets.forEach((input) => input.removeAttribute('disabled'));

            return;
        }

        this.permissionAllTargets.forEach((input) => {
            input.checked = false;
            input.setAttribute('disabled', 'disabled');
        });

        this.permissionCheckboxTargets.forEach((input) => {
            input.checked = false;
            input.setAttribute('disabled', 'disabled');
        });

        this._persistState();
    }

    _syncCheckAllPermissions() {
        // On click on a "check all" checkbox, enable all permissions for the project
        this.permissionAllTargets.forEach((inputAll) => {
            inputAll.addEventListener('change', () => {
                this.permissionCheckboxTargets.forEach((input) => {
                    if (input.getAttribute('data-project') === inputAll.getAttribute('data-project')) {
                        input.checked = inputAll.checked;
                    }
                });

                this._persistState();
            });
        });

        // On disabling of a permission checkbox, disable the "check all" for the project if it was enabled
        this.permissionCheckboxTargets.forEach((input) => {
            input.addEventListener('change', () => {
                if (!input.checked) {
                    this.permissionAllTargets.forEach((inputAll) => {
                        if (input.getAttribute('data-project') === inputAll.getAttribute('data-project')) {
                            inputAll.checked = false;
                        }
                    });

                    this._persistState();
                }
            });
        });
    }

    _persistState() {
        // Iterate over checkboxes to create the payload
        this.permissionCheckboxTargets.forEach((input) => {
            const project = input.getAttribute('data-project');
            const permission = input.getAttribute('data-permission');
            this.metadata.currentPermissions[project][permission] = input.checked;
        });

        this.permissionsInputTarget.value = JSON.stringify(this.metadata.currentPermissions);
    }
}
