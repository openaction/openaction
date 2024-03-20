import React, {useMemo} from 'react';
import {render} from 'react-dom';
import {SaveStatus, SaveStatusLabels} from '../../../components/Layout/SaveStatus';
import {createUrlEncoded, request} from '../../../../utils/http';
import {Appearance, Design, MergeTag, Labels, Editor} from '../../../components/Editor/Editor';

interface Props {
    projectId: number | null;
    design: Design;
    mergeTags: MergeTag[];
    appearance: Appearance;
    labels: Labels;
    saveUrl: string;
    uploadUrl: string;
    statusNode: string;
    statusLabels: SaveStatusLabels;
}

export default function EmailCampaignEditor(props: Props) {
    const statusNode = useMemo(() => document.querySelector(props.statusNode), [props.statusNode]);

    return (
        <Editor
            displayMode="email"
            projectId={props.projectId}
            design={props.design}
            mergeTags={props.mergeTags}
            appearance={props.appearance}
            labels={props.labels}
            onSave={(editor) => {
                render(<SaveStatus status="saving" labels={props.statusLabels}/>, statusNode);

                editor.exportHtml((data) => {
                    const {design, html} = data;

                    const response = request('POST', props.saveUrl, {
                        data: createUrlEncoded({
                            content: html ? html : '',
                            design: JSON.stringify(design),
                        }),
                    });

                    response.then(() => render(<SaveStatus status="saved" labels={props.statusLabels}/>, statusNode));
                    response.catch(() => render(<SaveStatus status="error" labels={props.statusLabels}/>, statusNode));
                });
            }}
            onUpload={(editor, file, done) => {
                render(<SaveStatus status="saving" labels={props.statusLabels}/>, statusNode);

                const data = new FormData();
                data.append('file', file);

                const response = request('POST', props.uploadUrl, {data: data});
                response.catch(() => render(<SaveStatus status="error" labels={props.statusLabels}/>, statusNode));

                response.then((res) => {
                    render(<SaveStatus status="saved" labels={props.statusLabels}/>, statusNode);
                    done({progress: 100, url: res.data.url});
                });
            }}
        />
    );
}
