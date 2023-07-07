import { waitFor } from '@testing-library/dom';
import { clearDOM, mountDOM } from '@symfony/stimulus-testing';
import redaxios from 'redaxios';
import CsrfController from '../../controllers/csrf_controller';
import { startController } from './start-controller';

jest.mock('redaxios');

describe('CsrfController', () => {
    let container;

    beforeEach(() => {
        container = mountDOM(`
            <div data-controller="csrf"
                 data-testid="element"
                 data-csrf-token-value="token" 
                 data-csrf-refresh-url-value="http://localhost"></div>
        `);
    });

    afterEach(() => {
        clearDOM();
    });

    it('connect', async () => {
        await startController<CsrfController>('csrf', CsrfController, container);

        expect(window.Citipo.token).toBe('token');
    });

    it('refreshCsrfToken', async () => {
        // Mock response
        (redaxios.request as any).mockResolvedValueOnce(
            Promise.resolve({
                then: (callback) => callback({ data: { token: 'refreshed' } }),
            })
        );

        // Trigger refresh
        const controller = await startController<CsrfController>('csrf', CsrfController, container);
        controller.refreshCsrfToken('http://localhost');

        await waitFor(() => expect(window.Citipo.token).toBe('refreshed'));
    });
});
