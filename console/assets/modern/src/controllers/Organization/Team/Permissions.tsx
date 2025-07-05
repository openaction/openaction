import {useMemo} from "react";
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
        permissions: string;
        grant_all_permissions: string;
    }
}

export default function (props: Props) {
    const initialProjectPermissions = useMemo(
        () => JSON.parse(props.projectsPermissionsValue),
        [props.projectsPermissionsValue],
    );

    return (
        <div className="tw:space-y-4">
            <div className="world-block p-3">
                <div className="tw:flex tw:items-center tw:gap-3">
                    <Checkbox id="isAdmin" />
                    <Label htmlFor="isAdmin" className="tw:mb-0!">{props.labels.is_admin_label}</Label>
                </div>
            </div>

            <div>
                <strong>
                    {props.labels.permissions}
                </strong>
            </div>

            <div className="world-block p-3">
                <div className="tw:flex tw:items-center tw:gap-1 tw:mb-6">
                    <Checkbox id="all" />
                    <Label htmlFor="all" className="tw:mb-0! tw:font-normal tw:text-slate-600 tw:text-xs">
                        {props.labels.grant_all_permissions}
                    </Label>
                </div>

                <div className="tw:grid tw:grid-cols-3 tw:gap-3">
                    {Object.keys(props.definitions).map(section => (
                        <div key={section} className="tw:space-y-3">
                            <div className="tw:uppercase tw:text-slate-500 tw:font-medium tw:text-xs">
                                {props.translations[section]}
                            </div>

                            {Object.keys(props.definitions[section]).map(category => (
                                <div className="tw:space-y-1">
                                    <div className="tw:text-slate-400 tw:text-xs">
                                        {props.translations[category]}
                                    </div>

                                    {props.definitions[section][category].map(permission => (
                                        <div className="tw:flex tw:items-center tw:gap-1">
                                            <Checkbox id={permission} />
                                            <Label htmlFor={permission} className="tw:mb-0! tw:font-normal tw:text-slate-600 tw:text-xs">
                                                {props.translations[permission]}
                                            </Label>
                                        </div>
                                    ))}
                                </div>
                            ))}
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
}
