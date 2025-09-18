import URLSearchParams from '@ungap/url-search-params';

export default function registerStats() {
    var loc = window.location;
    var doc = window.document;

    var scriptEl = doc.currentScript;
    var api = scriptEl.getAttribute('data-stats-api');
    var ignoreFlag = window.localStorage.citipo_ignore;
    var lastPage;

    function warn(reason) {
        console.warn('Ignoring event: ' + reason);
    }

    function trigger(eventName, options) {
        if (
            /^localhost$|^127(?:\.[0-9]+){0,2}\.[0-9]+$|^(?:0*\:)*?:?0*1$/.test(loc.hostname) ||
            loc.protocol === 'file:'
        ) {
            console.log(eventName, options);
            return warn('localhost');
        }

        if (window.phantom || window._phantom || window.__nightmare || window.navigator.webdriver || window.Cypress) {
            return;
        }

        if (ignoreFlag == 'true') {
            return warn('localStorage flag');
        }

        var payload = {};
        payload.n = eventName;
        payload.u = loc.href;
        payload.p = scriptEl.getAttribute('data-project');
        payload.r = doc.referrer || null;
        payload.w = window.innerWidth;

        var urlParams = new URLSearchParams(loc.search);
        payload.uso = urlParams.get('utm_source');
        payload.ume = urlParams.get('utm_medium');
        payload.uca = urlParams.get('utm_campaign');
        payload.uco = urlParams.get('utm_content');

        if (options && options.meta) {
            payload.m = options.meta;
        }

        var request = new XMLHttpRequest();
        request.open('POST', api, true);
        request.setRequestHeader('Content-Type', 'text/plain');

        request.send(JSON.stringify(payload));

        request.onreadystatechange = function () {
            request.readyState === 4 && options && options.callback && options.callback();
        };
    }

    function page() {
        if (lastPage == loc.pathname) {
            return;
        }

        lastPage = loc.pathname;
        trigger('pageview');
    }

    function handleVisibilityChange() {
        if (!lastPage && doc.visibilityState === 'visible') {
            page();
        }
    }

    function handleOutbound(event) {
        var link = event.target;
        var middle = event.type == 'auxclick' && event.which == 2;
        var click = event.type == 'click';

        while (link && (typeof link.tagName == 'undefined' || link.tagName.toLowerCase() != 'a' || !link.href)) {
            link = link.parentNode;
        }

        if (link && link.href && link.host && link.host != loc.host) {
            if (middle || click) {
                citipo('Outbound Link: Click', { props: { url: link.href } });
            }

            // Delay navigation so that Citipo is notified of the click
            if (!link.target || link.target.match(/^_(self|parent|top)$/i)) {
                if (!(event.ctrlKey || event.metaKey || event.shiftKey) && click) {
                    setTimeout(function () {
                        loc.href = link.href;
                    }, 150);
                    event.preventDefault();
                }
            }
        }
    }

    function registerOutboundLinkEvents() {
        doc.addEventListener('click', handleOutbound);
        doc.addEventListener('auxclick', handleOutbound);
    }

    var his = window.history;
    if (his.pushState) {
        var originalPushState = his['pushState'];
        his.pushState = function () {
            originalPushState.apply(this, arguments);
            page();
        };
        window.addEventListener('popstate', page);
    }

    registerOutboundLinkEvents();

    window.Citipo.Event = trigger;

    if (doc.visibilityState === 'prerender') {
        doc.addEventListener('visibilitychange', handleVisibilityChange);
    } else {
        page();
    }
}
