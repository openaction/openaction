import React from 'react';
import { render } from 'react-dom';
import { AbstractController } from './abstract_controller';
import { QomonPetitionModal, QomonPetitionPreview } from '../react/components/Editor/Blocks/QomonPetition';
import { QomonFormModal, QomonFormPreview } from '../react/components/Editor/Blocks/QomonForm';

const customBlocks = {
    QomonPetition: {
        preview: QomonPetitionPreview,
        modal: QomonPetitionModal,
    },
    QomonForm: {
        preview: QomonFormPreview,
        modal: QomonFormModal,
    },
};

export default class extends AbstractController {
    static values = { name: String };

    nameValue: string;

    doConnect() {
        if (typeof customBlocks[this.nameValue] === 'undefined') {
            throw new Error('Invalid custom block name: ' + this.nameValue);
        }

        const editorId = this.element.closest('.editor-content').getAttribute('id');
        const customBlock = customBlocks[this.nameValue];

        const renderPreview = (data: any) => {
            render(
                React.createElement(customBlock.preview, {
                    openModal: openModal,
                    closeModal: closeModal,
                    data: data,
                }),
                this.element
            );
        };

        const openModal = () => {
            render(
                React.createElement(customBlock.modal, {
                    closeModal: closeModal,
                    onChange: (data) => {
                        renderPreview(data);
                        (window as any).editors[editorId]._handleCodeBuildChange();
                    },
                    initialData: JSON.parse(this.element.querySelector('.contenteditor-data')?.innerHTML || 'null'),
                }),
                document.getElementById('contenteditor-customblock-editor')
            );
        };

        const closeModal = () => {
            render(<div />, document.getElementById('contenteditor-customblock-editor'));
        };

        renderPreview(JSON.parse(this.element.querySelector('.contenteditor-data')?.innerHTML || 'null'));
    }
}
