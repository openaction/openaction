import React, {useState} from 'react';
import TitleStep, {TitleStepLabels} from "./LaunchForm/TitleStep";
import ContentStep, {ContentStepLabels} from "./LaunchForm/ContentStep";

interface Props {
  fields: {
    title: { name: string, value: string },
    content: { name: string, value: string },
    mainImage: { name: string, value: string },
    _token: { name: string, value: string },
  },
  labels: {
    next: string,
    back: string,
    step1: TitleStepLabels,
    step2: ContentStepLabels,
  }
}

export enum Step {
  Title,
  Content,
  MainImage,
}

export default function LaunchForm(props: Props) {
  const [step, setStep] = useState<Step>(Step.Title);
  const [title, setTitle] = useState<string>('');
  const [content, setContent] = useState<string>('');

  return (
    <div className="row justify-content-center">
      <div className="col-12 col-md-10 col-lg-8 col-xl-6">
        {step === Step.Title ? (
          <TitleStep
            defaultValue={title}
            onChange={setTitle}
            setStep={setStep}
            labels={props.labels.step1}
            nextLabel={props.labels.next}
          />
        ) : ''}

        {step === Step.Content ? (
          <ContentStep
            defaultValue={title}
            onChange={setContent}
            setStep={setStep}
            labels={props.labels.step2}
            backLabel={props.labels.back}
            nextLabel={props.labels.next}
          />
        ) : ''}
      </div>
    </div>
  );
}
