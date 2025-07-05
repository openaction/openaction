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
        apply_to_all_entities: string;
        apply_to_specific_categories: string;
    }
}

// Categories that support category-specific permissions
const CATEGORY_SPECIFIC_PERMISSIONS = ['posts', 'pages', 'trombinoscope'];

export default function (props: Props) {
    const initialProjectPermissions = useMemo(
        () => JSON.parse(props.projectsPermissionsValue),
        [props.projectsPermissionsValue],
    );

    const [isAdmin, setIsAdmin] = useState<boolean>(props.isAdminValue === '1');
    const [allAccess, setAllAccess] = useState<Record<string, boolean>>({});
    const [projectPermissions, setProjectPermissions] = useState<Record<string, Record<string, boolean>>>(initialProjectPermissions);
    
    // State for category-specific permissions
    const [categoryScope, setCategoryScope] = useState<Record<string, Record<string, 'all' | 'specific'>>>({});
    const [categoryFilters, setCategoryFilters] = useState<Record<string, Record<string, string>>>({});

    // Get all available permissions for checking if all are selected
    const getAllPermissions = useMemo(() => {
        const permissions: string[] = [];
        Object.keys(props.definitions).forEach(section => {
            Object.keys(props.definitions[section]).forEach(category => {
                props.definitions[section][category].forEach(permission => {
                    permissions.push(permission);
                });
            });
        });
        return permissions;
    }, [props.definitions]);

    // Check if all permissions for a project are enabled
    const areAllPermissionsEnabled = (projectId: string): boolean => {
        const projectPerms = projectPermissions[projectId];
        if (!projectPerms) return false;
        
        return getAllPermissions.every(permission => projectPerms[permission] === true);
    };

    // Update allAccess state when projectPermissions change
    useEffect(() => {
        const newAllAccess: Record<string, boolean> = {};
        props.projects.forEach(project => {
            newAllAccess[project.uuid] = areAllPermissionsEnabled(project.uuid);
        });
        setAllAccess(newAllAccess);
    }, [projectPermissions, getAllPermissions, props.projects]);

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
            getAllPermissions.forEach(permission => {
                allPermissions[permission] = true;
            });
            
            setProjectPermissions(prev => ({
                ...prev,
                [projectId]: allPermissions
            }));
        } else {
            // Set all permissions to false for this project
            const allPermissions: Record<string, boolean> = {};
            getAllPermissions.forEach(permission => {
                allPermissions[permission] = false;
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

    // Handle category scope change
    const handleCategoryScopeChange = (projectId: string, category: string, scope: 'all' | 'specific') => {
        setCategoryScope(prev => ({
            ...prev,
            [projectId]: {
                ...prev[projectId],
                [category]: scope
            }
        }));
    };

    // Handle category filter change
    const handleCategoryFilterChange = (projectId: string, category: string, value: string) => {
        setCategoryFilters(prev => ({
            ...prev,
            [projectId]: {
                ...prev[projectId],
                [category]: value
            }
        }));
    };

    // Get category display name
    const getCategoryDisplayName = (category: string): string => {
        switch (category) {
            case 'website_posts':
                return 'actualités';
            case 'website_pages':
                return 'pages';
            case 'website_trombinoscope':
                return 'fiches de trombinoscope';
            default:
                return props.translations[category] || category;
        }
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

                        {!isAdmin && (
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

                                                {/* Category-specific permissions block */}
                                                {CATEGORY_SPECIFIC_PERMISSIONS.includes(category) && (
                                                    <div className="tw:mt-3 tw:p-3 tw:border tw:border-slate-200 tw:rounded-md tw:bg-slate-50">
                                                        <div className="tw:space-y-2">
                                                            <div className="tw:flex tw:gap-2 tw:items-start">
                                                                <input
                                                                    type="radio"
                                                                    id={`${project.uuid}-${category}-all`}
                                                                    name={`${project.uuid}-${category}-scope`}
                                                                    value="all"
                                                                    checked={categoryScope[project.uuid]?.[category] !== 'specific'}
                                                                    onChange={() => handleCategoryScopeChange(project.uuid, category, 'all')}
                                                                />
                                                                <Label
                                                                    htmlFor={`${project.uuid}-${category}-all`}
                                                                    className="tw:text-xs tw:font-normal tw:m-0! tw:-mt-0.5!"
                                                                >
                                                                    Appliquer ces permissions à toutes les {getCategoryDisplayName(category)}
                                                                </Label>
                                                            </div>
                                                            <div className={`tw:flex tw:gap-2 tw:items-start ${categoryScope[project.uuid]?.[category] !== 'specific' ? 'tw:opacity-50' : ''}`}>
                                                                <input
                                                                    type="radio"
                                                                    id={`${project.uuid}-${category}-specific`}
                                                                    name={`${project.uuid}-${category}-scope`}
                                                                    value="specific"
                                                                    checked={categoryScope[project.uuid]?.[category] === 'specific'}
                                                                    onChange={() => handleCategoryScopeChange(project.uuid, category, 'specific')}
                                                                />
                                                                <Label
                                                                    htmlFor={`${project.uuid}-${category}-specific`}
                                                                    className="tw:text-xs tw:font-normal tw:m-0! tw:-mt-0.5!"
                                                                >
                                                                    <div>
                                                                        Appliquer ces permissions uniquement aux catégories suivantes:
                                                                    </div>

                                                                    <input
                                                                        type="text"
                                                                        placeholder="Ex: Actualités, Événements, Communiqués..."
                                                                        value={categoryFilters[project.uuid]?.[category] || ''}
                                                                        onChange={(e: React.ChangeEvent<HTMLInputElement>) => handleCategoryFilterChange(project.uuid, category, e.target.value)}
                                                                        disabled={categoryScope[project.uuid]?.[category] !== 'specific'}
                                                                        className="tw:text-xs tw:h-6 tw:w-full tw:p-1 tw:border tw:border-slate-300 tw:rounded tw:bg-white tw:mt-1"
                                                                    />
                                                                </Label>
                                                            </div>
                                                        </div>

                                                        <div>
                                                        </div>
                                                    </div>
                                                )}
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
