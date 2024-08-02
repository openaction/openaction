// Styles
import './styles/blueprint.scss';
import './styles/crm.scss';
import './styles/unlayer.scss';
import './styles/contenteditor.scss';
import './styles/uploadcare.scss';

// Vendors
import 'flag-icons/sass/flag-icons.scss';
import '@uploadcare/react-uploader/core.css';

// Application
import { Application } from 'stimulus';
import { definitionsFromContext } from 'stimulus/webpack-helpers';

// Custom types
declare global {
    interface Window {
        Citipo: any;
    }
}

// Initialize Citipo state
window.Citipo = {};
window.Citipo.locale = document.documentElement.lang;
window.Citipo.token = null;

// Import React controllers
const reactControllers = {};

function importAll(r) {
    r.keys().forEach((key) => (reactControllers[key] = r(key).default));
}

importAll(require.context('./react/controllers', true, /\.tsx$/));

window.Citipo.resolveReactComponent = (name) => {
    const component = reactControllers['./' + name + '.tsx'];
    if (typeof component === 'undefined') {
        throw new Error('React controller "' + name + '" does not exist');
    }

    return component;
};

// Start Stimulus
const application = Application.start();
application.load(definitionsFromContext(require.context('./controllers', true, /\.tsx$/)));
