import 'core-js/features/promise';
import 'core-js/features/object/keys';
import 'core-js/features/dom-collections/for-each';

import * as Sentry from '@sentry/browser';
import { Integrations } from '@sentry/tracing';
import { Application } from 'stimulus';
import { definitionsFromContext } from 'stimulus/webpack-helpers';
import Clipboard from 'stimulus-clipboard';

// Log errors
const sentryDsn = document.body.getAttribute('data-sentry-dsn');

if (sentryDsn) {
    Sentry.init({
        dsn: sentryDsn,
        integrations: [new Integrations.BrowserTracing()],
        tracesSampleRate: 1.0,
    });
}

// Launch Stimulus
const application = Application.start();
const context = require.context('./controllers', true, /\.jsx$/);

application.load(definitionsFromContext(context));
application.register('stimulus-clipboard', Clipboard);

// Launch Bootstrap
require('bootstrap/js/src/modal');
require('bs-custom-file-input').init();
