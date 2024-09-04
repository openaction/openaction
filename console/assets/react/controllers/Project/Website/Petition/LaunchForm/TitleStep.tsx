import React from 'react';
import {Button, FormGroup, InputGroup, ProgressBar} from "@blueprintjs/core";
import {Step} from "../LaunchForm";

export interface TitleStepLabels {
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
    onChange: (title: string) => void,
    setStep: (step: Step) => void,
    labels: TitleStepLabels,
    nextLabel: string,
}

export default function TitleStep(props: Props) {
    const goToNextStep = () => props.setStep(Step.Content);

    return (
        <>
            <ProgressBar intent="primary" stripes={false} value={0.33} className="mb-3"/>

            <h1 className="bp4-heading mb-4">
                {props.labels.title}
            </h1>

            <FormGroup
                label={props.labels.label}
                helperText={props.labels.help}
                labelFor="title"
            >
                <InputGroup
                    id="title"
                    large={true}
                    fill={true}
                    required={true}
                    defaultValue={props.defaultValue}
                    onChange={(e) => props.onChange(e.currentTarget.value)}
                    onKeyUp={(e) => {
                        if (e.key === 'Enter') {
                            goToNextStep();
                        }
                    }}
                />
            </FormGroup>

            <div className="bp4-callout p-3">
                <h5 className="bp4-heading">
                    {props.labels.tipsTitle}
                </h5>

                {[1, 2, 3].map(i => (
                    <div className="mt-3" key={'tip=' + i}>
                        <h6 className="mb-0">{props.labels['tip' + i].title}</h6>
                        <em>{props.labels['tip' + i].example}</em>
                    </div>
                ))}
            </div>

            <div className="text-right mt-4">
                <Button large={true} intent="primary" className="ml-2" onClick={goToNextStep}>
                    {props.nextLabel}
                </Button>
            </div>
        </>
    );
}
