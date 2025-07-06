import {useMemo, useState, useEffect} from "react";
import type {Project} from "@/types.ts";
import {Checkbox} from "@/components/ui/checkbox.tsx";
import {Label} from "@/components/ui/label.tsx";
import {MultiSelect} from "@/components/multi-select.tsx";

interface Category {
    id: string;
    projectId: string;
    name: string;
    slug: string;
}

interface Props {
    definitions: Record<string, Record<string, string[]>>;
    translations: Record<string, string>;
    isAdminField: string;
    isAdminValue: string;
    projectsPermissionsField: string;
    projectsPermissionsValue: string;
    projectsPermissionsCategoriesField: string;
    projectsPermissionsCategoriesValue: string;
    projects: Project[];
    pagesCategories: Category[];
    postsCategories: Category[];
    trombinoscopeCategories: Category[];
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
    // Log the category props for now
    console.log('Pages categories:', props.pagesCategories);
    console.log('Posts categories:', props.postsCategories);
    console.log('Trombinoscope categories:', props.trombinoscopeCategories);

    const initialProjectPermissions = useMemo(
        () => props.projectsPermissionsValue ? JSON.parse(props.projectsPermissionsValue) : {},
        [props.projectsPermissionsValue],
    );

    const initialProjectPermissionsCategories = useMemo(
        () => props.projectsPermissionsCategoriesValue ? JSON.parse(props.projectsPermissionsCategoriesValue) : {},
        [props.projectsPermissionsCategoriesValue],
    );

    const [isAdmin, setIsAdmin] = useState<boolean>(props.isAdminValue === '1');
    const [allAccess, setAllAccess] = useState<Record<string, boolean>>({});
    const [projectPermissions, setProjectPermissions] = useState<Record<string, Record<string, boolean>>>(initialProjectPermissions);
    
    // Initialize category scope based on existing data
    const initialCategoryScope = useMemo(() => {
        const scope: Record<string, Record<string, 'all' | 'specific'>> = {};
        
        props.projects.forEach(project => {
            scope[project.uuid] = {};
            CATEGORY_SPECIFIC_PERMISSIONS.forEach(categoryType => {
                const hasSpecificCategories = initialProjectPermissionsCategories[project.uuid]?.[categoryType];
                scope[project.uuid][categoryType] = hasSpecificCategories ? 'specific' : 'all';
            });
        });
        
        return scope;
    }, [initialProjectPermissionsCategories, props.projects]);

    // State for category-specific permissions
    const [categoryScope, setCategoryScope] = useState<Record<string, Record<string, 'all' | 'specific'>>>(initialCategoryScope);
    const [categoryFilters, setCategoryFilters] = useState<Record<string, Record<string, string>>>({});
    
    // State for selected categories for each project and permission
    const [selectedCategories, setSelectedCategories] = useState<Record<string, Record<string, string[]>>>(initialProjectPermissionsCategories);

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

    // Compute the projects permissions categories data
    const projectsPermissionsCategories = useMemo(() => {
        const categoriesData: Record<string, Record<string, string[] | null>> = {};
        
        props.projects.forEach(project => {
            categoriesData[project.uuid] = {};
            
            // Only include category-specific permissions
            CATEGORY_SPECIFIC_PERMISSIONS.forEach(categoryType => {
                const scope = categoryScope[project.uuid]?.[categoryType];
                const selectedCats = selectedCategories[project.uuid]?.[categoryType];
                
                if (scope === 'specific' && selectedCats) {
                    categoriesData[project.uuid][categoryType] = selectedCats;
                } else {
                    categoriesData[project.uuid][categoryType] = null;
                }
            });
        });
        
        return categoriesData;
    }, [selectedCategories, categoryScope, props.projects]);

    // Get categories for a specific project and category type
    const getCategoriesForProject = (projectId: string, categoryType: string): Category[] => {
        switch (categoryType) {
            case 'posts':
                return props.postsCategories.filter(cat => cat.projectId === projectId);
            case 'pages':
                return props.pagesCategories.filter(cat => cat.projectId === projectId);
            case 'trombinoscope':
                return props.trombinoscopeCategories.filter(cat => cat.projectId === projectId);
            default:
                return [];
        }
    };

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

    // Update the hidden input when projectsPermissionsCategories changes
    useEffect(() => {
        const hiddenInput = document.querySelector(`input[name="${props.projectsPermissionsCategoriesField}"]`) as HTMLInputElement;
        if (hiddenInput) {
            hiddenInput.value = JSON.stringify(projectsPermissionsCategories);
        }
    }, [projectsPermissionsCategories, props.projectsPermissionsCategoriesField]);


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

    // Handle category selection change
    const handleCategorySelectionChange = (projectId: string, categoryType: string, selectedValues: string[]) => {
        setSelectedCategories(prev => ({
            ...prev,
            [projectId]: {
                ...prev[projectId],
                [categoryType]: selectedValues
            }
        }));
    };

    // Get category display name
    const getCategoryDisplayName = (category: string): string => {
        switch (category) {
            case 'posts':
                return 'actualités';
            case 'pages':
                return 'pages';
            case 'trombinoscope':
                return 'fiches';
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
            <input type="hidden" name={props.projectsPermissionsCategoriesField} value={JSON.stringify(projectsPermissionsCategories)} />

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
                                    <div key={`${project.uuid}-${section}`}>
                                        <div className="tw:uppercase tw:text-slate-500 tw:font-medium tw:text-xs tw:mb-2">
                                            {props.translations[section]}
                                        </div>

                                        <div className="tw:space-y-6">
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
                                                        <div className="tw:mt-3 tw:p-2 tw:border tw:border-slate-200 tw:rounded-sm tw:bg-slate-50">
                                                            <div className="tw:space-y-2">
                                                                <div className="tw:text-xs">
                                                                    Appliquer ces permissions :
                                                                </div>

                                                                <div className={`tw:flex tw:gap-2 tw:items-start ${categoryScope[project.uuid]?.[category] === 'specific' ? 'tw:opacity-50' : ''}`}>
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
                                                                        à toutes les {getCategoryDisplayName(category)}
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
                                                                        <div className="tw:mb-1">
                                                                            uniquement aux pages des catégories suivantes:
                                                                        </div>

                                                                        {categoryScope[project.uuid]?.[category] === 'specific' && (
                                                                            <MultiSelect
                                                                                options={getCategoriesForProject(project.id, category).map(cat => ({
                                                                                    value: cat.id,
                                                                                    label: cat.name
                                                                                }))}
                                                                                onValueChange={(selectedValues) => handleCategorySelectionChange(project.uuid, category, selectedValues)}
                                                                                placeholder="Sélectionner"
                                                                                variant="inverted"
                                                                                maxCount={100}
                                                                                className="tw:min-h-8! tw:bg-white tw:hover:bg-white!"
                                                                            />
                                                                        )}

                                                                        <input
                                                                            type="text"
                                                                            placeholder="Ex: Actualités, Événements, Communiqués..."
                                                                            value={categoryFilters[project.uuid]?.[category] || ''}
                                                                            onChange={(e: React.ChangeEvent<HTMLInputElement>) => handleCategoryFilterChange(project.uuid, category, e.target.value)}
                                                                            disabled={categoryScope[project.uuid]?.[category] !== 'specific'}
                                                                            className="tw:hidden tw:text-xs tw:h-6 tw:w-full tw:p-1 tw:border tw:border-slate-300 tw:rounded tw:bg-white tw:mt-1"
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
