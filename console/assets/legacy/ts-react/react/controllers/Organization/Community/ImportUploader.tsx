import 'jquery';
import React, { useState } from 'react';
import { Widget } from '@uploadcare/react-widget';
import { request } from '../../../../utils/http';
import { Intent, ProgressBar } from '@blueprintjs/core';

interface UploadKey {
    publicKey: string;
    signature: string;
    expire: number;
}

interface Props {
    uploadKey: UploadKey;
    prepareUrl: string;
    errorLabel: string;
}

export default function (props: Props) {
    const [fileUuid, setFileUuid] = useState<string | null>(null);
    const [errors, setErrors] = useState([]);

    if (fileUuid) {
        return <ProgressBar intent={Intent.PRIMARY} />;
    }

    return (
        <>
            <Widget
                publicKey={props.uploadKey.publicKey}
                secureSignature={props.uploadKey.signature}
                secureExpire={props.uploadKey.expire}
                locale={window.Citipo.locale}
                systemDialog={true}
                doNotStore={true}
                onChange={(fileInfo) => {
                    setFileUuid(fileInfo.uuid);
                    setErrors([]);

                    request('POST', props.prepareUrl + '&fileUuid=' + fileInfo.uuid + '&fileName=' + fileInfo.name)
                        .then((res) => {
                            window.location = res.data.redirectUrl;
                        })
                        .catch(() => {
                            setFileUuid(null);
                            setErrors([props.errorLabel]);
                        });
                }}
            />

            {errors.map((error) => (
                <div className="text-danger mt-2">{error}</div>
            ))}
        </>
    );
}
