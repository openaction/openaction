import {useMemo, useState, useEffect} from "react";
import type {Project} from "@/types.ts";
import {Checkbox} from "@/components/ui/checkbox.tsx";
import {Label} from "@/components/ui/label.tsx";
import {MultiSelect} from "@/components/multi-select.tsx";
import {ChevronsUpDown} from "lucide-react";
import {Collapsible, CollapsibleContent, CollapsibleTrigger} from "@/components/ui/collapsible.tsx";

interface Category {
    id: string;
    uuid: string;
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
    eventsCategories: Category[];
    labels: {
        is_admin_label: string;
        grant_all_permissions: string;
        apply_permissions_label: string;
        apply_permissions_all_posts: string;
        apply_permissions_all_pages: string;
        apply_permissions_all_trombinoscope: string;
        apply_permissions_all_events: string;
        apply_permissions_specific_posts: string;
        apply_permissions_specific_pages: string;
        apply_permissions_specific_trombinoscope: string;
        apply_permissions_specific_events: string;
        select_categories_placeholder: string;
    }
}

// Categories that support category-specific permissions
const CATEGORY_SPECIFIC_PERMISSIONS = ['posts', 'pages', 'trombinoscope', 'events'];

export default function (props: Props) {
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
            case 'events':
                return props.eventsCategories.filter(cat => cat.projectId === projectId);
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

    // Get category-specific label
    const getCategorySpecificLabel = (category: string): string => {
        switch (category) {
            case 'posts':
                return props.labels.apply_permissions_specific_posts;
            case 'pages':
                return props.labels.apply_permissions_specific_pages;
            case 'trombinoscope':
                return props.labels.apply_permissions_specific_trombinoscope;
            case 'events':
                return props.labels.apply_permissions_specific_events;
            default:
                return props.labels.apply_permissions_specific_posts;
        }
    };

    // Get category all label
    const getCategoryAllLabel = (category: string): string => {
        switch (category) {
            case 'posts':
                return props.labels.apply_permissions_all_posts;
            case 'pages':
                return props.labels.apply_permissions_all_pages;
            case 'trombinoscope':
                return props.labels.apply_permissions_all_trombinoscope;
            case 'events':
                return props.labels.apply_permissions_all_events;
            default:
                return props.labels.apply_permissions_all_posts;
        }
    };

    return (
        <div className="space-y-6">
            <div className="flex items-center gap-3">
                <Checkbox
                    id="isAdmin"
                    checked={isAdmin}
                    onCheckedChange={(checked) => setIsAdmin(true === checked)}
                />

                <Label htmlFor="isAdmin" className="mb-0!">
                    {props.labels.is_admin_label}
                </Label>

                {isAdmin && <input type="hidden" name={props.isAdminField} value="1" />}
            </div>

            <input type="hidden" name={props.projectsPermissionsField} value={JSON.stringify(projectPermissions)} />
            <input type="hidden" name={props.projectsPermissionsCategoriesField} value={JSON.stringify(projectsPermissionsCategories)} />

            {props.projects.map(project => {
                return (
                    <div key={project.uuid} className="world-block p-3 mb-3">
                        <Collapsible defaultOpen={props.projects.length <= 5}>
                            <CollapsibleTrigger className="w-full">
                                <div className="flex items-center gap-2 hover:underline">
                                    <div>
                                        <ChevronsUpDown className="size-4" />
                                    </div>
                                    <h5 className="mb-0!">
                                        {project.name}
                                    </h5>
                                </div>
                            </CollapsibleTrigger>
                            <CollapsibleContent>
                                <div className="space-y-4 mt-2">
                                    <div className="flex items-center gap-1">
                                        <Checkbox
                                            id={`${project.uuid}-all`}
                                            disabled={isAdmin}
                                            checked={isAdmin || allAccess[project.uuid] || false}
                                            onCheckedChange={(checked) => handleAllAccessChange(project.uuid, true === checked)}
                                        />

                                        <Label
                                            htmlFor={`${project.uuid}-all`}
                                            className="mb-0! font-normal text-slate-600 text-xs">
                                            {props.labels.grant_all_permissions}
                                        </Label>
                                    </div>

                                    {!isAdmin && (
                                        <div className="grid grid-cols-3 gap-3">
                                            {Object.keys(props.definitions).map(section => (
                                                <div key={`${project.uuid}-${section}`}>
                                                    <div className="uppercase text-slate-500 font-medium text-xs mb-2">
                                                        {props.translations[section]}
                                                    </div>

                                                    <div className="space-y-6">
                                                        {Object.keys(props.definitions[section]).map(category => (
                                                            <div className="space-y-1" key={`${project.uuid}-${category}`}>
                                                                <div className="text-slate-400 text-xs">
                                                                    {props.translations[category]}
                                                                </div>

                                                                {props.definitions[section][category].map(permission => (
                                                                    <div className="flex items-center gap-1" key={`${project.uuid}-${permission}`}>
                                                                        <Checkbox
                                                                            id={`${project.uuid}-${permission}`}
                                                                            checked={projectPermissions[project.uuid]?.[permission] || false}
                                                                            onCheckedChange={(checked) => handlePermissionChange(project.uuid, permission, true === checked)}
                                                                        />
                                                                        <Label
                                                                            htmlFor={`${project.uuid}-${permission}`}
                                                                            className="mb-0! font-normal text-slate-600 text-xs">
                                                                            {props.translations[permission]}
                                                                        </Label>
                                                                    </div>
                                                                ))}

                                                                {/* Category-specific permissions block */}
                                                                {CATEGORY_SPECIFIC_PERMISSIONS.includes(category) && (
                                                                    <Collapsible
                                                                        defaultOpen={categoryScope[project.uuid]?.[category] === 'specific'}
                                                                        className="mt-3 p-2 border border-slate-200 rounded-sm bg-slate-50"
                                                                    >
                                                                        <CollapsibleTrigger>
                                                                            <div className="text-xs flex items-center gap-1">
                                                                                <ChevronsUpDown className="size-3" />
                                                                                <span>{props.labels.apply_permissions_label}</span>
                                                                            </div>
                                                                        </CollapsibleTrigger>

                                                                        <CollapsibleContent className="space-y-2 mt-1">
                                                                            <div className={`flex gap-2 items-start ${categoryScope[project.uuid]?.[category] === 'specific' ? 'opacity-50' : ''}`}>
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
                                                                                    className="text-xs font-normal m-0! -mt-0.5!"
                                                                                >
                                                                                    {getCategoryAllLabel(category)}
                                                                                </Label>
                                                                            </div>
                                                                            <div className={`flex gap-2 items-start ${categoryScope[project.uuid]?.[category] !== 'specific' ? 'opacity-50' : ''}`}>
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
                                                                                    className="text-xs font-normal m-0! -mt-0.5!"
                                                                                >
                                                                                    <div className="mb-1">
                                                                                        {getCategorySpecificLabel(category)}
                                                                                    </div>

                                                                                    {categoryScope[project.uuid]?.[category] === 'specific' && (
                                                                                        <MultiSelect
                                                                                            options={getCategoriesForProject(project.id, category).map(cat => ({
                                                                                                value: cat.uuid,
                                                                                                label: cat.name
                                                                                            }))}
                                                                                            defaultValue={selectedCategories[project.uuid]?.[category] || []}
                                                                                            onValueChange={(selectedValues) => handleCategorySelectionChange(project.uuid, category, selectedValues)}
                                                                                            placeholder={props.labels.select_categories_placeholder}
                                                                                            variant="inverted"
                                                                                            maxCount={100}
                                                                                            className="min-h-8! bg-white hover:bg-white!"
                                                                                        />
                                                                                    )}
                                                                                </Label>
                                                                            </div>
                                                                        </CollapsibleContent>
                                                                    </Collapsible>
                                                                )}
                                                            </div>
                                                        ))}
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </div>
                            </CollapsibleContent>
                        </Collapsible>
                    </div>
                );
            })}
        </div>
    );
}
