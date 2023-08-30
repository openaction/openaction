import React, { useState } from 'react';
import { translator } from '../../../services/translator';

const platform = {
    website: [
        'website_pages',
        'website_newsletter',
        'website_posts',
        'website_documents',
        'website_trombinoscope',
        'website_manifesto',
        'website_events',
        'website_forms',
    ],
    community: ['community_contacts', 'community_emailing', 'community_texting', 'community_phoning'],
    members_area: [
        'members_area_account',
        'members_area_resources',
        'members_area_posts',
        'members_area_events',
        'members_area_forms',
    ],
};

const enableTool = (tools, name) => {
    if (tools.indexOf(name) === -1) {
        tools.push(name);
    }

    return tools;
};

const disableTool = (tools, name) => {
    return tools.filter((t) => t !== name);
};

const enableModule = (modules, tools, name) => {
    if (modules.indexOf(name) === -1) {
        modules.push(name);
    }

    for (let i in platform[name]) {
        tools = enableTool(tools, platform[name][i]);
    }

    return [modules, tools];
};

const disableModule = (modules, tools, name) => {
    modules = modules.filter((m) => m !== name);

    for (let i in platform[name]) {
        tools = disableTool(tools, platform[name][i]);
    }

    return [modules, tools];
};

export function ModuleChooser(props) {
    const [state, setState] = useState({ modules: props.modules, tools: props.tools });

    const onChange = (modules, tools) => setState({ modules: modules, tools: tools });

    return (
        <div>
            <div className="font-weight-bold">
                {translator.trans('organization.create-project.modules-chooser.label')}
            </div>

            <div className="text-muted mb-3">
                {translator.trans('organization.create-project.modules-chooser.help')}
            </div>

            <div className="row">
                <div className="col-12 col-lg-6">
                    <Module name="website" modules={state.modules} tools={state.tools} onChange={onChange} />
                </div>
                <div className="col-12 col-lg-6">
                    <Module name="community" modules={state.modules} tools={state.tools} onChange={onChange} />
                    <Module name="members_area" modules={state.modules} tools={state.tools} onChange={onChange} />
                </div>
            </div>

            <div className="d-none">
                {state.modules.map((module) => (
                    <input type="hidden" key={'field_' + module} name={props.modulesInput} value={module} />
                ))}

                {state.tools.map((tool) => (
                    <input type="hidden" key={'field_' + tool} name={props.toolsInput} value={tool} />
                ))}
            </div>
        </div>
    );
}

function Module(props) {
    const toggleModule = () => {
        const applyChange = props.modules.indexOf(props.name) > -1 ? disableModule : enableModule;
        const [modules, tools] = applyChange(props.modules, props.tools, props.name);

        props.onChange(modules, tools);
    };

    let toolsView = [];
    for (let i in platform[props.name]) {
        const tool = platform[props.name][i];
        toolsView.push(
            <Tool name={tool} modules={props.modules} tools={props.tools} onChange={props.onChange} key={tool} />
        );
    }

    const checked = props.modules.indexOf(props.name) > -1;

    return (
        <div className="world-block p-3 mb-3">
            <div className="custom-control custom-switch">
                <input
                    type="checkbox"
                    className="custom-control-input"
                    id={'module-' + props.name}
                    checked={checked}
                    onChange={toggleModule}
                />

                <label className="custom-control-label h5 font-weight-light mb-0" htmlFor={'module-' + props.name}>
                    {translator.trans('organization.create-project.modules-chooser.modules.' + props.name)}
                </label>
            </div>

            <div className="mt-3 px-3 px-md-4 px-lg-5 organization-manage-project-details-tools">
                <div className={'organization-manage-project-details-overlay ' + (!checked ? 'd-block' : '')} />

                {toolsView}
            </div>
        </div>
    );
}

function Tool(props) {
    const toggleTool = () => {
        const applyChange = props.tools.indexOf(props.name) > -1 ? disableTool : enableTool;
        const tools = applyChange(props.tools, props.name);

        props.onChange(props.modules, tools);
    };

    return (
        <div className="organization-manage-project-details-tools mb-3">
            <div className="custom-control custom-switch">
                <input
                    type="checkbox"
                    className="custom-control-input"
                    id={'tool-' + props.name}
                    checked={props.tools.indexOf(props.name) > -1}
                    onChange={toggleTool}
                />

                <label className="custom-control-label mb-0" htmlFor={'tool-' + props.name}>
                    {translator.trans('organization.create-project.modules-chooser.tools.' + props.name + '.label')}
                </label>
            </div>

            <div className="text-muted">
                {translator.trans('organization.create-project.modules-chooser.tools.' + props.name + '.help')}
            </div>
        </div>
    );
}
