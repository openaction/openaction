import React from 'react';
import { clearDOM, mountDOM } from '@symfony/stimulus-testing';
import ReactController from '../../controllers/react_controller';
import { startController } from './start-controller';
import { screen } from '@testing-library/react';

function MockComponent(props: { message: string }) {
    return <div>{props.message}</div>;
}

window.Citipo.resolveReactComponent = (name: string) => {
    if (name !== 'MockComponent') {
        throw new Error('Invalid React name passed, MockComponent expected, ' + name + ' given');
    }

    return MockComponent;
};

describe('ReactController', () => {
    let container;

    beforeEach(() => {
        container = mountDOM(`
            <div data-controller="react" data-testid="element"></div>
        `);

        const element = container.querySelector('[data-testid="element"]');
        element.setAttribute('data-react-component-value', 'MockComponent');
        element.setAttribute('data-react-props-value', JSON.stringify({ message: 'Hello from React' }));
    });

    afterEach(() => {
        clearDOM();
    });

    it('connect', async () => {
        await startController<ReactController>('react', ReactController, container);
        expect(await screen.findByText('Hello from React')).toBeInTheDocument();
    });
});
