import React from 'react';
import {Button, FormGroup, ProgressBar} from "@blueprintjs/core";
import {Step} from "../LaunchForm";
import {FileUploaderRegular} from "@uploadcare/react-uploader";
import * as FileUploaderEn from "@uploadcare/blocks/locales/file-uploader/en";
import * as FileUploaderFr from "@uploadcare/blocks/locales/file-uploader/fr";
import * as FileUploaderDe from "@uploadcare/blocks/locales/file-uploader/de";

const localeDefinitions: Record<string, Record<string, string>> = {
    en: FileUploaderEn.default,
    fr: FileUploaderFr.default,
    de: FileUploaderDe.default,
}

export interface MainImageStepLabels {
    title: string,
    label: string,
    help: string,
    tipsTitle: string,
    tip1: {
        title: string,
        example: string,
    },
    tip2: {
        title: string,
        example: string,
    },
    tip3: {
        title: string,
        example: string,
    }
}

interface Props {
    defaultValue: string,
    onChange: (mainImage: string) => void,
    setStep: (step: Step) => void,
    labels: MainImageStepLabels,
    backLabel: string,
    nextLabel: string,
}

export default function MainImageStep(props: Props) {
    return (
        <>
            <ProgressBar intent="primary" stripes={false} value={1} className="mb-3"/>

            <h1 className="bp4-heading mb-4">
                {props.labels.title}
            </h1>

            <FormGroup
                label={props.labels.label}
                helperText={props.labels.help}
                labelFor="title"
            >
                <FileUploaderRegular
                    localeDefinitionOverride={localeDefinitions}
                    localeName={window.Citipo.locale}
                    pubkey="fb22c411b0a34f349985"
                    maxLocalFileSizeBytes={10000000}
                    multiple={false}
                    imgOnly={true}
                    sourceList="local, url, camera"
                    secureSignature="43f7ea6fcac380e55d36c49e925a92c7824eaef5c20b882badf0e29f6ab45f87"
                    secureExpire="1722620826"
                    classNameUploader="citipo-uploadcare-theme uc-light"
                    cropPreset="1200:675"
                    onDoneClick={(e) => {
                        if (typeof e.allEntries[0] === 'undefined') {
                            return;
                        }

                        props.onChange(e.allEntries[0].uuid);
                    }}
                    onFileRemoved={() => {
                        props.onChange('');
                    }}
                />
            </FormGroup>

            <div className="bp4-callout p-3">
                <h5 className="bp4-heading">
                    {props.labels.tipsTitle}
                </h5>

                {[1, 2, 3].map(i => (
                    <div className="mt-3" key={'tip-' + i}>
                        <h6 className="mb-0">{props.labels['tip' + i].title}</h6>
                        <em>{props.labels['tip' + i].example}</em>
                    </div>
                ))}
            </div>

            <div className="text-right mt-4">
                <Button large={true} minimal={true} onClick={() => props.setStep(Step.Content)}>
                    {props.backLabel}
                </Button>

                <Button large={true} intent="primary" className="ml-2" onClick={() => props.setStep(Step.MainImage)}>
                    {props.nextLabel}
                </Button>
            </div>
        </>
    );
}
