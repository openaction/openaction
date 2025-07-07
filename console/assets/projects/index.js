import '@stimulus/polyfills';
import './styles/project.scss';

import registerStats from './analytics/tracker';

window.Citipo = {};
registerStats();

import { Application } from 'stimulus';
import ContentViewController from './analytics/contentview_controller';
import CustomEventController from './analytics/customevent_controller';

// Launch Stimulus
window.Citipo.App = Application.start();
window.Citipo.App.register('citipo-contentview', ContentViewController);
window.Citipo.App.register('citipo-customevent', CustomEventController);
