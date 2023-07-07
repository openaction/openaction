import 'jquery';
import React, { useState } from 'react';
import { Widget } from '@uploadcare/react-widget';
import { request } from '../../../../utils/http';

let statusCheckInterval = null;

interface UploadKey {
    publicKey: string;
    signature: string;
    expire: number;
}

interface Props {
    uploadKey: UploadKey;
    uploadUrl: string;
    statusUrl: string;
    redirectUrl: string;
}

export default function (props: Props) {
    const [fileUuid, setFileUuid] = useState(null);
    const [errors, setErrors] = useState([]);

    const checkStatus = () => {
        request('GET', props.statusUrl)
            .then((response) => {
                if (response.data.errors.length > 0) {
                    if (statusCheckInterval) {
                        clearInterval(statusCheckInterval);
                    }

                    setFileUuid(null);
                    setErrors(response.data.errors);
                } else if (response.data.success) {
                    if (statusCheckInterval) {
                        clearInterval(statusCheckInterval);
                    }

                    window.location.href = props.redirectUrl;
                }
            })
            .catch(() => {
                setFileUuid(null);
                setErrors(["Une erreur s'est produite lors de la validation du fichier. Pouvez-vous réessayer ?"]);

                if (statusCheckInterval) {
                    clearInterval(statusCheckInterval);
                }
            });
    };

    if (!fileUuid) {
        return (
            <div>
                <Widget
                    publicKey={props.uploadKey.publicKey}
                    secureSignature={props.uploadKey.signature}
                    secureExpire={props.uploadKey.expire}
                    locale={window.Citipo.locale}
                    systemDialog={true}
                    doNotStore={true}
                    onChange={(fileInfo) => {
                        setFileUuid(fileInfo.uuid);

                        request('POST', props.uploadUrl + '&fileUuid=' + fileInfo.uuid)
                            .then(() => {
                                // Try a first time quickly, then wait a bit longer
                                setTimeout(() => {
                                    checkStatus();
                                    statusCheckInterval = setInterval(() => checkStatus(), 2000);
                                }, 500);
                            })
                            .catch(() => {
                                setFileUuid(null);
                                setErrors([
                                    "Une erreur s'est produite lors de l'envoi du fichier. Pouvez-vous réessayer ?",
                                ]);
                            });
                    }}
                />

                {errors.map((error) => (
                    <div className="text-danger mt-2">{error}</div>
                ))}
            </div>
        );
    }

    return (
        <div className="print-uploader-validating">
            <i className="fas fa-spin fa-circle-notch mr-2" />
            Validation du fichier en cours...
        </div>
    );
}
