import React, { useEffect, useState } from 'react';
import { request } from '../../../../utils/http';
import { AnchorButton, Intent, ProgressBar } from '@blueprintjs/core';

interface Props {
    statusUrl: string;
    postsUrl: string;
    labels: {
        processingTitle: string;
        processingDesc: string;
        finishedTitle: string;
        finishedDesc: string;
        finishedBack: string;
    };
}

interface Job {
    finished: boolean;
    step: number;
    payload: object;
}

export default function ImportProgress(props: Props) {
    const [job, setJob] = useState<Job | null>(null);

    let interval: number = 0;

    const refreshJob = (jobStatusUrl: string) => {
        request('GET', jobStatusUrl).then((res) => {
            setJob(res.data);

            if (res.data.finished) {
                interval && clearInterval(interval);
            }
        });
    };

    useEffect(() => {
        setTimeout(() => {
            refreshJob(props.statusUrl);
            interval = window.setInterval(() => refreshJob(props.statusUrl), 3000);
        }, 1000);
    }, []);

    if (job && job.finished) {
        return (
            <>
                <h5 className="bp4-heading">{props.labels.finishedTitle}</h5>

                <p className="bp4-text-muted">{props.labels.finishedDesc}</p>

                <ProgressBar intent={Intent.SUCCESS} value={1.0} animate={false} stripes={false} />

                <br />

                <AnchorButton
                    href={props.postsUrl}
                    text={props.labels.finishedBack}
                    outlined={true}
                    intent={'slate' as any}
                />
            </>
        );
    }

    return (
        <>
            <h5 className="bp4-heading">{props.labels.processingTitle}</h5>

            <p className="bp4-text-muted">{props.labels.processingDesc + (job ? ' (' + job.step + ')' : '')}</p>

            <ProgressBar intent={Intent.PRIMARY} value={undefined} />
        </>
    );
}
