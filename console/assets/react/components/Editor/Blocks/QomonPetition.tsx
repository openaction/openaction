import React, { useEffect, useState } from 'react';

interface PetitionData {
    url: string;
}

interface PreviewProps {
    openModal: () => void;
    closeModal: () => void;
    data: PetitionData | null;
}

export function QomonPetitionPreview(props: PreviewProps) {
    return (
        <span className="contenteditor-custom-block-qomon">
            <span className="contenteditor-data" style={{ display: 'none' }}>
                {JSON.stringify(props.data)}
            </span>

            <img src="/res/qomon/icon-white.png" className="contenteditor-custom-block-qomon-icon" />

            <span className="contenteditor-custom-block-qomon-title">Pétition Qomon</span>

            <span className="contenteditor-custom-block-qomon-help">
                {props.data?.url || 'Pas de pétition configurée'}
            </span>

            <span className="contenteditor-custom-block-qomon-noedit" />

            <button type="button" className="btn btn-light" onClick={props.openModal}>
                Configurer la pétition
            </button>

            <br />

            <button type="button" className="btn btn-link" onClick={() => window.open('https://qomon.com', '_blank')}>
                En savoir plus sur Qomon
            </button>
        </span>
    );
}

interface EditorProps {
    onChange: (data: PetitionData) => void;
    closeModal: () => void;
    initialData: PetitionData | null;
}

export function QomonPetitionModal(props: EditorProps) {
    const [url, setUrl] = useState<string>(props.initialData?.url || '');

    return (
        <div className="contenteditor-custom-block-modal">
            <div className="contenteditor-custom-block-modal-overlay" onClick={props.closeModal} />

            <div className="contenteditor-custom-block-modal-content contenteditor-custom-block-qomon-modal-content">
                <div className="mb-2">
                    Pour intégrer une pétition Qomon dans votre contenu, vous devez tout d'abord créer une pétition sur
                    votre espace Qomon :
                </div>

                <div className="text-center mb-4">
                    <a href="" className="btn btn-secondary mb-1">
                        <i className="far fa-plus mr-2"></i>
                        Créer une pétition Qomon
                    </a>
                    <br />
                    <a href="https://help.qomon.com/fr/articles/6926604-comment-creer-une-petition"
                       className="btn btn-link" target="_blank">
                        Lire la documentation
                    </a>
                </div>

                <div className="mb-1">Une fois que votre pétition est prête, indiquez ci-dessous son adresse URL :</div>

                <div className="mb-4">
                    <input
                        type="url"
                        className="form-control"
                        value={url}
                        onChange={(e) => setUrl(e.currentTarget.value)}
                        placeholder="https://petition.qomon.org/votre-petition/"
                    />
                </div>

                <div className="text-center">
                    <button
                        type="button"
                        className="btn btn-primary"
                        onClick={() => {
                            props.onChange({ url: url });
                            props.closeModal();
                        }}
                    >
                        Enregistrer
                    </button>
                </div>
            </div>
        </div>
    );
}
