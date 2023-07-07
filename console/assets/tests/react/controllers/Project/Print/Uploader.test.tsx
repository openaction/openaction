import React from 'react';
import { render, screen } from '@testing-library/react';
import '@testing-library/jest-dom';
import Uploader from '../../../../../react/controllers/Project/Print/Uploader';

describe('Uploader', () => {
    it('renders', async () => {
        render(
            <div role="container">
                <Uploader
                    uploadKey={{
                        publicKey: 'public',
                        signature: 'signature',
                        expire: 1,
                    }}
                    uploadUrl="http://localhost"
                    redirectUrl="http://localhost"
                    statusUrl="http://localhost"
                />
            </div>
        );

        const container = await screen.findByRole('container');
        expect(container).toBeInTheDocument();

        // Should display button
        expect(await screen.findByText('Choose a file')).toBeInTheDocument();
    });
});
