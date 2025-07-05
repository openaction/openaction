import React from "react";

interface Props {
    definitions: Record<string, Record<string, string[]>>;
    translations: Record<string, string>;
    isAdminField: string;
    isAdminValue: string;
    projectsPermissionsField: string;
    projectsPermissionsValue: string;
}

export default function (props: Props) {
    console.log(props);

    return <div className="text-3xl font-bold underline">Hello</div>;
}
