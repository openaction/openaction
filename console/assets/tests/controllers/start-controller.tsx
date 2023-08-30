import { Application, ControllerConstructor } from 'stimulus';
import { getByTestId, waitFor } from '@testing-library/dom';

export async function startController<T>(
    name: string,
    constructor: ControllerConstructor,
    container: HTMLDivElement
): Promise<T> {
    const application = Application.start();
    application.register(name, constructor);

    return new Promise(async (resolve) => {
        await waitFor(() => {
            expect((getByTestId(container, 'element') as any)[name + '_controller'].isConnected).toBe(true);
        });

        resolve((getByTestId(container, 'element') as any)[name + '_controller']);
    });
}
