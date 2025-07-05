import {useMemo, useState, useEffect} from "react";
import type {Project} from "@/types.ts";
import {Checkbox} from "@/components/ui/checkbox.tsx";
import {Label} from "@/components/ui/label.tsx";

interface Props {
    definitions: Record<string, Record<string, string[]>>;
    translations: Record<string, string>;
    isAdminField: string;
    isAdminValue: string;
    projectsPermissionsField: string;
    projectsPermissionsValue: string;
    projects: Project[];
    labels: {
        is_admin_label: string;
        grant_all_permissions: string;
    }
}

export default function (props: Props) {
    const initialProjectPermissions = useMemo(
        () => JSON.parse(props.projectsPermissionsValue),
        [props.projectsPermissionsValue],
    );

    const [isAdmin, setIsAdmin] = useState<boolean>(props.isAdminValue === '1');
    const [allAccess, setAllAccess] = useState<Record<string, boolean>>({});
    const [projectPermissions, setProjectPermissions] = useState<Record<string, Record<string, boolean>>>(initialProjectPermissions);

    // Update the hidden input when isAdmin changes
    useEffect(() => {
        const hiddenInput = document.querySelector(`input[name="${props.isAdminField}"]`) as HTMLInputElement;
        if (hiddenInput) {
            hiddenInput.value = isAdmin ? '1' : '0';
        }
    }, [isAdmin, props.isAdminField]);

    // Update the hidden input when projectPermissions changes
    useEffect(() => {
        const hiddenInput = document.querySelector(`input[name="${props.projectsPermissionsField}"]`) as HTMLInputElement;
        if (hiddenInput) {
            hiddenInput.value = JSON.stringify(projectPermissions);
        }
    }, [projectPermissions, props.projectsPermissionsField]);

    // Handle allAccess change for a project
    const handleAllAccessChange = (projectId: string, checked: boolean) => {
        setAllAccess(prev => ({ ...prev, [projectId]: checked }));
        
        if (checked) {
            // Set all permissions to true for this project
            const allPermissions: Record<string, boolean> = {};
            Object.keys(props.definitions).forEach(section => {
                Object.keys(props.definitions[section]).forEach(category => {
                    props.definitions[section][category].forEach(permission => {
                        allPermissions[permission] = true;
                    });
                });
            });
            
            setProjectPermissions(prev => ({
                ...prev,
                [projectId]: allPermissions
            }));
        } else {
            // Set all permissions to false for this project
            const allPermissions: Record<string, boolean> = {};
            Object.keys(props.definitions).forEach(section => {
                Object.keys(props.definitions[section]).forEach(category => {
                    props.definitions[section][category].forEach(permission => {
                        allPermissions[permission] = false;
                    });
                });
            });
            
            setProjectPermissions(prev => ({
                ...prev,
                [projectId]: allPermissions
            }));
        }
    };

    // Handle individual permission change
    const handlePermissionChange = (projectId: string, permission: string, checked: boolean) => {
        setProjectPermissions(prev => ({
            ...prev,
            [projectId]: {
                ...prev[projectId],
                [permission]: checked
            }
        }));
    };

    return (
        <div className="tw:space-y-6">
            <div className="tw:flex tw:items-center tw:gap-3">
                <Checkbox
                    id="isAdmin"
                    checked={isAdmin}
                    onCheckedChange={(checked) => setIsAdmin(true === checked)}
                />

                <Label htmlFor="isAdmin" className="tw:mb-0!">
                    {props.labels.is_admin_label}
                </Label>

                {isAdmin && <input type="hidden" name={props.isAdminField} value="1" />}
            </div>

            <input type="hidden" name={props.projectsPermissionsField} value={JSON.stringify(projectPermissions)} />

            {props.projects.map(project => {
                return (
                    <div key={project.uuid} className="world-block p-3 mb-3 tw:space-y-4">
                        <h5>
                            {project.name}
                        </h5>

                        <div className="tw:flex tw:items-center tw:gap-1">
                            <Checkbox
                                id={`${project.uuid}-all`}
                                disabled={isAdmin}
                                checked={isAdmin || allAccess[project.uuid] || false}
                                onCheckedChange={(checked) => handleAllAccessChange(project.uuid, true === checked)}
                            />

                            <Label
                                htmlFor={`${project.uuid}-all`}
                                className="tw:mb-0! tw:font-normal tw:text-slate-600 tw:text-xs">
                                {props.labels.grant_all_permissions}
                            </Label>
                        </div>

                        {!isAdmin && !allAccess[project.uuid] && (
                            <div className="tw:grid tw:grid-cols-3 tw:gap-3">
                                {Object.keys(props.definitions).map(section => (
                                    <div key={`${project.uuid}-${section}`} className="tw:space-y-3">
                                        <div className="tw:uppercase tw:text-slate-500 tw:font-medium tw:text-xs">
                                            {props.translations[section]}
                                        </div>

                                        {Object.keys(props.definitions[section]).map(category => (
                                            <div className="tw:space-y-1" key={`${project.uuid}-${category}`}>
                                                <div className="tw:text-slate-400 tw:text-xs">
                                                    {props.translations[category]}
                                                </div>

                                                {props.definitions[section][category].map(permission => (
                                                    <div className="tw:flex tw:items-center tw:gap-1" key={`${project.uuid}-${permission}`}>
                                                        <Checkbox 
                                                            id={`${project.uuid}-${permission}`}
                                                            checked={projectPermissions[project.uuid]?.[permission] || false}
                                                            onCheckedChange={(checked) => handlePermissionChange(project.uuid, permission, true === checked)}
                                                        />
                                                        <Label 
                                                            htmlFor={`${project.uuid}-${permission}`}
                                                            className="tw:mb-0! tw:font-normal tw:text-slate-600 tw:text-xs">
                                                            {props.translations[permission]}
                                                        </Label>
                                                    </div>
                                                ))}
                                            </div>
                                        ))}
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                );
            })}
        </div>
    );
}
