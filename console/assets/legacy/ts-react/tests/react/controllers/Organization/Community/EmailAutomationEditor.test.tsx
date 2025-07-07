import React from 'react';
import { render, screen } from '@testing-library/react';
import '@testing-library/jest-dom';
import EmailAutomationEditor from '../../../../../react/controllers/Organization/Community/EmailAutomationEditor';
import { createEditor } from '../../../../../react/bridge/unlayer';
import { mockUnlayerEditor } from '../../../../fixtures/unlayer';

jest.mock('../../../../../react/bridge/unlayer');
(createEditor as any).mockImplementation(() => mockUnlayerEditor());

describe('EmailAutomationEditor', () => {
    it('renders', async () => {
        render(
            <div role="container">
                <div id="status-node" role="status-node" />

                <EmailAutomationEditor
                    projectId={null}
                    design={{
                        counters: {},
                        body: {
                            rows: [],
                            values: {},
                        },
                    }}
                    mergeTags={[]}
                    appearance={{
                        fontTitle: 'Open Sans',
                        fontText: 'Open Sans',
                        colorPrimary: '#000',
                        colorSecondary: '#000',
                        colorThird: '#000',
                    }}
                    labels={{
                        defaultText: 'Text',
                        defaultTitle: 'Title',
                    }}
                    saveUrl="http://localhost"
                    uploadUrl="http://localhost"
                    statusNode="#status-node"
                    statusLabels={{
                        saving: 'Saving ...',
                        saved: 'Saved',
                        error: 'Error',
                    }}
                />
            </div>
        );

        expect(await screen.findByRole('container')).toBeInTheDocument();
        expect(await screen.findByRole('status-node')).toBeInTheDocument();
    });
});
