import React from 'react';
import {Button, FormGroup, InputGroup} from "@blueprintjs/core";
import {Step} from "../LaunchForm";

export interface ContentStepLabels {
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
  labels: ContentStepLabels,
  backLabel: string,
  nextLabel: string,
}

export default function ContentStep(props: Props) {
  return (
    <>
      <h1 className="bp4-heading mb-4">
        {props.labels.title}
      </h1>

      <div className="bp4-callout p-3">
        <h5 className="bp4-heading">
          {props.labels.tipsTitle}
        </h5>

        {[1, 2, 3].map(i => (
          <div className="mt-3" key={'tip-' + i}>
            <h6 className="mb-0">{props.labels['tip'+i].title}</h6>
            <em>{props.labels['tip'+i].example}</em>
          </div>
        ))}
      </div>

      <div className="text-right mt-4">
        <Button large={true} minimal={true} onClick={() => props.setStep(Step.Title)}>
          {props.backLabel}
        </Button>

        <Button large={true} intent="primary" className="ml-2" onClick={() => props.setStep(Step.MainImage)}>
          {props.nextLabel}
        </Button>
      </div>
    </>
  );
}
