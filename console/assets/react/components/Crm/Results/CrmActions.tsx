import React, { ReactNode, useEffect, useState } from 'react';
import { numberFormat } from '../../../../utils/formatter';
import { CrmBatchPayload } from '../Model/crm';
import { SlateButton } from '../../Layout/SlateButton';
import { AnchorButton, Button, ButtonGroup, Callout, Dialog, Intent, ProgressBar } from '@blueprintjs/core';
import { request } from '../../../../utils/http';
import { SingleTagSelector } from '../../Layout/TagsSelector/SingleTagSelector';
import { Tag, TagsRegistry } from '../../Layout/TagsSelector/Model/tag';

export interface CrmActionsLabels {
    nbHits: string;
    clear: string;
    applyLabel: string;
    export: string;
    exportQuestion: string;
    exportConfirm: string;
    exportCancel: string;
    exportStarting: string;
    exportInProgress: string;
    exportSuccess: string;
    exportDownload: string;
    addTag: string;
    addTagQuestion: string;
    addTagLabel: string;
    addTagNoResults: string;
    addTagConfirm: string;
    addTagCancel: string;
    addTagStarting: string;
    addTagInProgress: string;
    addTagSuccess: string;
    removeTag: string;
    removeTagQuestion: string;
    removeTagLabel: string;
    removeTagNoResults: string;
    removeTagConfirm: string;
    removeTagCancel: string;
    removeTagStarting: string;
    removeTagInProgress: string;
    removeTagSuccess: string;
    remove: string;
    removeQuestion: string;
    removeConfirm: string;
    removeCancel: string;
    removeStarting: string;
    removeInProgress: string;
    removeSuccess: string;
}

export interface CrmActionsBatchEndpoints {
    export?: string;
    addTag?: string;
    removeTag?: string;
    remove?: string;
}

interface Props {
    nbHits: number;
    batch?: CrmActionsBatchEndpoints;
    batchPayload: CrmBatchPayload;
    tagsRegistry: TagsRegistry;
    onResetClick: () => void;
    onActionFinished: () => void;
    actionsLabels: CrmActionsLabels;
}

interface BatchRequest extends CrmBatchPayload {
    params: object;
}

interface Job {
    finished: boolean;
    progress: number;
    payload: object;
}

export function CrmActions(props: Props) {
    const [exportOpen, setExportOpen] = useState<boolean>(false);
    const [addTagOpen, setAddTagOpen] = useState<boolean>(false);
    const [removeTagOpen, setRemoveTagOpen] = useState<boolean>(false);
    const [removeOpen, setRemoveOpen] = useState<boolean>(false);

    return (
        <div className="crm-search-results-row">
            <div className="crm-search-results-nbhits">
                <div className="mb-1">
                    {numberFormat(props.nbHits)} {' ' + props.actionsLabels.nbHits}
                </div>

                <SlateButton
                    text={props.actionsLabels.clear}
                    icon={<i className="far fa-undo" />}
                    onClick={props.onResetClick}
                    small={true}
                    minimal={true}
                />
            </div>

            <div className="crm-search-results-actions-buttons">
                <div className="crm-search-results-actions-buttons-label">{props.actionsLabels.applyLabel}</div>

                <ButtonGroup minimal={false} vertical={true} fill={true}>
                    {props.batch.export ? (
                        <SlateButton
                            text={props.actionsLabels.export}
                            icon={<i className="far fa-cloud-download" />}
                            small={true}
                            fill={true}
                            onClick={() => setExportOpen(true)}
                        />
                    ) : (
                        ''
                    )}

                    {props.batch.addTag ? (
                        <SlateButton
                            text={props.actionsLabels.addTag}
                            icon={<i className="far fa-tag" />}
                            small={true}
                            fill={true}
                            onClick={() => setAddTagOpen(true)}
                        />
                    ) : (
                        ''
                    )}

                    {props.batch.removeTag ? (
                        <SlateButton
                            text={props.actionsLabels.removeTag}
                            icon={<i className="far fa-times" />}
                            small={true}
                            fill={true}
                            onClick={() => setRemoveTagOpen(true)}
                        />
                    ) : (
                        ''
                    )}

                    {props.batch.remove ? (
                        <SlateButton
                            text={props.actionsLabels.remove}
                            icon={<i className="far fa-times" />}
                            small={true}
                            fill={true}
                            onClick={() => setRemoveOpen(true)}
                        />
                    ) : (
                        ''
                    )}
                </ButtonGroup>
            </div>

            {props.batch.export ? (
                <CrmExportAction
                    key={'action-' + exportOpen}
                    open={exportOpen}
                    onClose={() => setExportOpen(false)}
                    {...props}
                />
            ) : (
                ''
            )}

            {props.batch.addTag ? (
                <CrmAddTagAction
                    key={'add-tag-' + addTagOpen}
                    open={addTagOpen}
                    onClose={() => setAddTagOpen(false)}
                    {...props}
                />
            ) : (
                ''
            )}

            {props.batch.removeTag ? (
                <CrmRemoveTagAction
                    key={'remove-tag-' + removeTagOpen}
                    open={removeTagOpen}
                    onClose={() => setRemoveTagOpen(false)}
                    {...props}
                />
            ) : (
                ''
            )}

            {props.batch.remove ? (
                <CrmRemoveAction
                    key={'remove-' + removeOpen}
                    open={removeOpen}
                    onClose={() => setRemoveOpen(false)}
                    {...props}
                />
            ) : (
                ''
            )}
        </div>
    );
}

interface CrmExportActionProps extends Props {
    open: boolean;
    onClose: () => void;
}

function CrmExportAction(props: CrmExportActionProps) {
    const [fileUrl, setFileUrl] = useState<string | null>(null);

    if (fileUrl) {
        return (
            <Dialog isOpen={props.open} canOutsideClickClose={false} canEscapeKeyClose={false}>
                <div className="crm-search-results-actions-dialog">
                    <Callout intent={Intent.SUCCESS}>{props.actionsLabels.exportSuccess}</Callout>

                    <br />

                    <ButtonGroup>
                        <AnchorButton intent={Intent.PRIMARY} href={fileUrl} target="_blank">
                            {props.actionsLabels.exportDownload}
                        </AnchorButton>

                        <SlateButton onClick={() => props.onClose()}>{props.actionsLabels.exportCancel}</SlateButton>
                    </ButtonGroup>
                </div>
            </Dialog>
        );
    }

    return (
        <CrmJobDialog
            nbHits={props.nbHits}
            open={props.open}
            confirmButtonText={props.actionsLabels.exportConfirm}
            cancelButtonText={props.actionsLabels.exportCancel}
            startingText={props.actionsLabels.exportStarting}
            inProgressText={props.actionsLabels.exportInProgress}
            title={props.actionsLabels.exportQuestion}
            onClose={props.onClose}
            onFinish={(payload) => {
                setFileUrl(payload.fileUrl);
                props.onActionFinished();
            }}
            startJob={() =>
                new Promise<string>((resolve) => {
                    request('POST', props.batch.export, { data: { ...props.batchPayload, params: {} } }).then((res) => {
                        resolve(res.data.statusUrl);
                    });
                })
            }
        />
    );
}

interface CrmAddTagActionProps extends Props {
    open: boolean;
    onClose: () => void;
}

function CrmAddTagAction(props: CrmAddTagActionProps) {
    const [selectedTag, setSelectedTag] = useState<Tag | null>(null);
    const [isAdded, setIsAdded] = useState<boolean>(false);

    if (isAdded) {
        return (
            <Dialog isOpen={props.open} canOutsideClickClose={false} canEscapeKeyClose={false}>
                <div className="crm-search-results-actions-dialog">
                    <Callout intent={Intent.SUCCESS}>{props.actionsLabels.addTagSuccess}</Callout>

                    <br />

                    <SlateButton onClick={() => props.onClose()}>{props.actionsLabels.addTagCancel}</SlateButton>
                </div>
            </Dialog>
        );
    }

    return (
        <CrmJobDialog
            nbHits={props.nbHits}
            open={props.open}
            confirmButtonText={props.actionsLabels.addTagConfirm}
            confirmButtonDisabled={selectedTag === null}
            cancelButtonText={props.actionsLabels.addTagCancel}
            startingText={props.actionsLabels.addTagStarting}
            inProgressText={props.actionsLabels.addTagInProgress}
            title={props.actionsLabels.addTagQuestion}
            content={
                <>
                    <SingleTagSelector
                        tagsRegistry={props.tagsRegistry}
                        placeholder={props.actionsLabels.addTagLabel}
                        noResultsLabel={props.actionsLabels.addTagNoResults}
                        onChange={(tag) => setSelectedTag(tag)}
                    />

                    <br />
                    <br />
                </>
            }
            onClose={props.onClose}
            onFinish={() => {
                setIsAdded(true);
                props.onActionFinished();
            }}
            startJob={() =>
                new Promise<string>((resolve) => {
                    const batchRequest: BatchRequest = {
                        ...props.batchPayload,
                        params: { tagId: selectedTag.id },
                    };

                    request('POST', props.batch.addTag, { data: batchRequest }).then((res) => {
                        resolve(res.data.statusUrl);
                    });
                })
            }
        />
    );
}

interface CrmRemoveTagActionProps extends Props {
    open: boolean;
    onClose: () => void;
}

function CrmRemoveTagAction(props: CrmRemoveTagActionProps) {
    const [selectedTag, setSelectedTag] = useState<Tag | null>(null);
    const [isRemoved, setIsRemoved] = useState<boolean>(false);

    if (isRemoved) {
        return (
            <Dialog isOpen={props.open} canOutsideClickClose={false} canEscapeKeyClose={false}>
                <div className="crm-search-results-actions-dialog">
                    <Callout intent={Intent.SUCCESS}>{props.actionsLabels.removeTagSuccess}</Callout>

                    <br />

                    <SlateButton onClick={() => props.onClose()}>{props.actionsLabels.removeTagCancel}</SlateButton>
                </div>
            </Dialog>
        );
    }

    return (
        <CrmJobDialog
            nbHits={props.nbHits}
            open={props.open}
            confirmButtonText={props.actionsLabels.removeTagConfirm}
            confirmButtonDisabled={selectedTag === null}
            cancelButtonText={props.actionsLabels.removeTagCancel}
            startingText={props.actionsLabels.removeTagStarting}
            inProgressText={props.actionsLabels.removeTagInProgress}
            title={props.actionsLabels.removeTagQuestion}
            content={
                <>
                    <SingleTagSelector
                        tagsRegistry={props.tagsRegistry}
                        placeholder={props.actionsLabels.removeTagLabel}
                        noResultsLabel={props.actionsLabels.removeTagNoResults}
                        onChange={(tag) => setSelectedTag(tag)}
                    />

                    <br />
                    <br />
                </>
            }
            onClose={props.onClose}
            onFinish={() => {
                setIsRemoved(true);
                props.onActionFinished();
            }}
            startJob={() =>
                new Promise<string>((resolve) => {
                    const batchRequest: BatchRequest = {
                        ...props.batchPayload,
                        params: { tagId: selectedTag.id },
                    };

                    request('POST', props.batch.removeTag, { data: batchRequest }).then((res) => {
                        resolve(res.data.statusUrl);
                    });
                })
            }
        />
    );
}

interface CrmRemoveActionProps extends Props {
    open: boolean;
    onClose: () => void;
}

function CrmRemoveAction(props: CrmExportActionProps) {
    const [isRemoved, setIsRemoved] = useState<boolean>(false);

    if (isRemoved) {
        return (
            <Dialog isOpen={props.open} canOutsideClickClose={false} canEscapeKeyClose={false}>
                <div className="crm-search-results-actions-dialog">
                    <Callout intent={Intent.SUCCESS}>{props.actionsLabels.removeSuccess}</Callout>

                    <br />

                    <SlateButton onClick={() => props.onClose()}>{props.actionsLabels.removeCancel}</SlateButton>
                </div>
            </Dialog>
        );
    }

    return (
        <CrmJobDialog
            nbHits={props.nbHits}
            open={props.open}
            confirmButtonText={props.actionsLabels.removeConfirm}
            cancelButtonText={props.actionsLabels.removeCancel}
            startingText={props.actionsLabels.removeStarting}
            inProgressText={props.actionsLabels.removeInProgress}
            title={props.actionsLabels.removeQuestion}
            onClose={props.onClose}
            onFinish={() => {
                setIsRemoved(true);
                props.onActionFinished();
            }}
            startJob={() =>
                new Promise<string>((resolve) => {
                    request('POST', props.batch.remove, { data: { ...props.batchPayload, params: {} } }).then((res) => {
                        resolve(res.data.statusUrl);
                    });
                })
            }
        />
    );
}

interface CrmJobDialogProps {
    nbHits: number;
    open: boolean;
    title: string;
    content?: ReactNode;
    confirmButtonDisabled?: boolean;
    confirmButtonText: string;
    cancelButtonText: string;
    startingText: string;
    inProgressText: string;
    startJob: () => Promise<string>;
    onClose: () => void;
    onFinish: (payload: any) => void;
}

function CrmJobDialog(props: CrmJobDialogProps) {
    const [isLoading, setIsLoading] = useState<boolean>(false);
    const [job, setJob] = useState<Job | null>(null);

    let interval: number = 0;

    const refreshJob = (jobStatusUrl: string) => {
        request('GET', jobStatusUrl).then((res) => {
            setJob(res.data);

            if (res.data.finished) {
                props.onFinish(res.data.payload);
                interval && clearInterval(interval);
            }
        });
    };

    const handleStartJob = () => {
        setIsLoading(true);

        props.startJob().then((jobStatusUrl) => {
            setTimeout(() => {
                refreshJob(jobStatusUrl);
                interval = window.setInterval(() => refreshJob(jobStatusUrl), 3000);
            }, 1000);
        });
    };

    useEffect(() => {
        return () => {
            interval && clearInterval(interval);
        };
    }, []);

    if (isLoading) {
        return (
            <Dialog isOpen={props.open} canOutsideClickClose={false} canEscapeKeyClose={false}>
                <div className="crm-search-results-actions-dialog">
                    <h5 className="bp4-heading">{props.title.replace('%nbHits%', numberFormat(props.nbHits))}</h5>

                    <p className="bp4-text-muted">
                        {job && job.progress
                            ? props.inProgressText + ' (' + Math.round(job.progress * 100) + '%)'
                            : props.startingText}
                    </p>

                    <ProgressBar intent={Intent.PRIMARY} value={job && job.progress ? job.progress : undefined} />

                    <br />

                    <SlateButton onClick={() => props.onClose()}>{props.cancelButtonText}</SlateButton>
                </div>
            </Dialog>
        );
    }

    return (
        <Dialog isOpen={props.open} canOutsideClickClose={false} canEscapeKeyClose={false}>
            <div className="crm-search-results-actions-dialog">
                <h5 className="bp4-heading">{props.title.replace('%nbHits%', numberFormat(props.nbHits))}</h5>

                <div>{props.content ? props.content : ''}</div>

                <ButtonGroup>
                    <Button
                        intent={Intent.PRIMARY}
                        disabled={props.confirmButtonDisabled || false}
                        onClick={handleStartJob}
                    >
                        {props.confirmButtonText}
                    </Button>

                    <SlateButton onClick={() => props.onClose()}>{props.cancelButtonText}</SlateButton>
                </ButtonGroup>
            </div>
        </Dialog>
    );
}
