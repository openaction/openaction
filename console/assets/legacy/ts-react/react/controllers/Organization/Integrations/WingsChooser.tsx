import React, { useState } from 'react';
import { Select2 } from '@blueprintjs/select';
import { SlateButton } from '../../../components/Layout/SlateButton';
import { InputGroup, MenuItem } from '@blueprintjs/core';

interface Project {
    uuid: string;
    name: string;
    endpoint: string;
}

interface Props {
    projects: Project[];
    labels: {
        project: string;
        help: string;
        name: string;
        endpoint: string;
        events: string;
        http_method: string;
        save_help: string;
    };
}

const ProjectSelect = Select2.ofType<Project>();

export default function (props: Props) {
    const projects = props.projects;
    const [selectedProject, setSelectedProject] = useState<Project>(projects.length > 0 ? projects[0] : null);

    return (
        <div>
            <div className="mb-1">{props.labels.project}</div>

            <div className="mb-4">
                <ProjectSelect
                    items={projects}
                    itemRenderer={(project, { handleClick, handleFocus, modifiers }) => (
                        <MenuItem
                            key={project.uuid}
                            text={project.name}
                            selected={project.uuid === selectedProject.uuid}
                            onClick={handleClick}
                            onFocus={handleFocus}
                            active={modifiers.active}
                            disabled={modifiers.disabled}
                        />
                    )}
                    onItemSelect={(project) => {
                        setSelectedProject(project);
                    }}
                    popoverProps={{
                        minimal: true,
                        placement: 'bottom-start',
                    }}
                >
                    <SlateButton text={selectedProject.name} rightIcon="double-caret-vertical" />
                </ProjectSelect>
            </div>

            <div className="mb-3">{props.labels.help}</div>

            <ul>
                <li className="mb-2">{props.labels.name} ;</li>
                <li className="mb-2">
                    {props.labels.endpoint} :
                    <br />
                    <InputGroup defaultValue={selectedProject.endpoint} fill={true} />
                </li>
                <li className="mb-2">{props.labels.events} ;</li>
                <li className="mb-2">{props.labels.http_method} ;</li>
            </ul>

            <div className="mt-4">{props.labels.save_help}</div>
        </div>
    );
}
