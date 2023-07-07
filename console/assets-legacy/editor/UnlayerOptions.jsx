import { exposedDataReader } from '../services/exposed-data-reader';

export const UNLAYER_EMAIL_OPTIONS = {
    projectId: parseInt(document.body.getAttribute('data-unlayer-project-id')),
    displayMode: 'email',
    locale: exposedDataReader.read('locale', 'en'),
    features: {
        preheaderText: false,
        stockImages: {
            enabled: true,
        },
    },
    tools: {
        text: {
            position: 1,
        },
        image: {
            position: 2,
        },
        heading: {
            position: 3,
        },
        button: {
            position: 4,
        },
        columns: {
            position: 5,
        },
        video: {
            position: 6,
        },
        menu: {
            position: 7,
        },
        divider: {
            position: 8,
        },
        html: {
            position: 9,
        },
    },
};
