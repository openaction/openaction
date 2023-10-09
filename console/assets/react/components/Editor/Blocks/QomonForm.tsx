import React, { useEffect, useState } from 'react';

interface FormData {
    url: string;
}

interface PreviewProps {
    openModal: () => void;
    closeModal: () => void;
    data: FormData | null;
}

export function QomonFormPreview(props: PreviewProps) {
    return (
        <span className="contenteditor-custom-block-qomon">
            <span className="contenteditor-data" style={{ display: 'none' }}>
                {JSON.stringify(props.data)}
            </span>

            <img src="/res/qomon/icon-white.png" className="contenteditor-custom-block-qomon-icon" />

            <span className="contenteditor-custom-block-qomon-title">Formulaire Qomon</span>

            <span className="contenteditor-custom-block-qomon-help">
                {props.data?.url || 'Pas de formulaire configuré'}
            </span>

            <span className="contenteditor-custom-block-qomon-noedit" />

            <button type="button" className="btn btn-light" onClick={props.openModal}>
                Configurer le formulaire
            </button>

            <br />

            <button type="button" className="btn btn-link" onClick={() => window.open('https://qomon.com', '_blank')}>
                En savoir plus sur Qomon
            </button>
        </span>
    );
}

interface EditorProps {
    onChange: (data: FormData) => void;
    closeModal: () => void;
    initialData: FormData | null;
}

export function QomonFormModal(props: EditorProps) {
    const [url, setUrl] = useState<string>(props.initialData?.url || '');

    return (
        <div className="contenteditor-custom-block-modal">
            <div className="contenteditor-custom-block-modal-overlay" onClick={props.closeModal} />

            <div className="contenteditor-custom-block-modal-content contenteditor-custom-block-qomon-modal-content">
                <div className="mb-2">
                    Pour intégrer un formulaire Qomon dans votre contenu, vous devez tout d'abord créer un formulaire
                    sur votre espace Qomon :
                </div>

                <div className="text-center mb-4">
                    <a href="" className="btn btn-secondary mb-1">
                        <i className="far fa-plus mr-2"></i>
                        Créer un formulaire Qomon
                    </a>
                    <br />
                    <a href="https://help.qomon.com/fr/articles/8042129-comment-utiliser-les-formulaires-en-ligne"
                       className="btn btn-link" target="_blank">
                        Lire la documentation
                    </a>
                </div>

                <div className="mb-1">
                    Une fois que votre formulaire est prêt, indiquez ci-dessous son adresse URL :
                </div>

                <div className="mb-4">
                    <input
                        type="url"
                        className="form-control"
                        value={url}
                        onChange={(e) => setUrl(e.currentTarget.value)}
                        placeholder="https://form.qomon.org/votre-formulaire/"
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
