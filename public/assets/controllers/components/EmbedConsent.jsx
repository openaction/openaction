import '../../styles/embed-consent.css';

import React, {useEffect, useState} from 'react';
import Cookies from 'js-cookie';

export const EmbedConsent = (props) => {
    if (!props.enableConsent) {
        Cookies.set('embed-consent-'+props.type, '1');
    }

    const parsedUrl = new URL(props.url, 'https://example.com');
    const [hasConsent, setHasConsent] = useState('1' === Cookies.get('embed-consent-'+props.type));

    const giveConsent = () => {
        setHasConsent(true);
        Cookies.set('embed-consent-'+props.type, '1');
    };

    const cancelConsent = () => {
        Cookies.set('embed-consent-'+props.type, '0');
        window.location.reload();
    };

    if (hasConsent) {
        let view = <div />;

        if ('twitter-timeline' === props.type) {
            view = <TwitterEmbed url={props.url} />;
        } else if ('twitter-tweet' === props.type) {
            view = <TwitterTweet htmlContent={props.htmlContent} />;
        } else if ('facebook-timeline' === props.type) {
            view = <FacebookEmbed width={props.containerWidth} url={props.url} />
        } else if ('facebook-video' === props.type) {
            view = <FacebookVideo width={props.containerWidth} url={props.url} />
        } else if ('facebook-post' === props.type) {
            view = <FacebookPost width={props.containerWidth} url={props.url} />
        } else if ('youtube-video' === props.type) {
            view = <YoutubeVideo width={props.containerWidth} url={props.url} />
        } else if ('google-map' === props.type) {
            view = <GoogleMap width={props.containerWidth} url={props.url} />
        }

        return (
            <>
                {view}
                <div className="embed-consent-cancel">
                    <button type="button" className="btn btn-light btn-sm" onClick={() => cancelConsent()}>
                        {props.cancelLabel}
                    </button>
                </div>
            </>
        );
    }

    return (
        <div className="embed-consent">
            <div>
                <h5 className="embed-consent-title">
                    {props.titleLabel}
                </h5>

                <div className="embed-consent-description mb-1">
                    <a href={props.url} target="_blank" rel="noreferrer noopener" className="text-white">
                        {props.url.substr(0, 50)}
                    </a>
                </div>

                <div className="embed-consent-description mb-3">
                    {props.descriptionLabel.replace('%host%', parsedUrl.hostname)}
                </div>

                <button type="button" className="btn btn-sm btn-primary mr-2" onClick={() => giveConsent()}>
                    {props.acceptLabel}
                </button>

                <a href={props.url} target="_blank" rel="noreferrer noopener" className="btn btn-sm btn-outline-light border-0">
                    <i className="fa fa-external-link mr-1" />
                    {props.externalLabel}
                </a>
            </div>
        </div>
    )
}

const TwitterEmbed = (props) => {
    // Load Twitter SDK if necessary
    useEffect(() => {
        if (!window.twttr) {
            window.twttr = (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0],
                    t = window.twttr || {};
                if (d.getElementById(id)) return t;
                js = d.createElement(s);
                js.id = id;
                js.src = "https://platform.twitter.com/widgets.js";
                fjs.parentNode.insertBefore(js, fjs);

                t._e = [];
                t.ready = function(f) {
                    t._e.push(f);
                };

                return t;
            }(document, "script", "twitter-wjs"));
        }
    }, []);

    return (
        <a className="twitter-timeline" data-height="600" target="_blank" rel="noreferrer noopener" href={props.url}>
            {props.url}
        </a>
    );
};

const TwitterTweet = (props) => {
    // Load Twitter SDK if necessary
    useEffect(() => {
        if (!window.twttr) {
            window.twttr = (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0],
                    t = window.twttr || {};
                if (d.getElementById(id)) return t;
                js = d.createElement(s);
                js.id = id;
                js.src = "https://platform.twitter.com/widgets.js";
                fjs.parentNode.insertBefore(js, fjs);

                t._e = [];
                t.ready = function(f) {
                    t._e.push(f);
                };

                return t;
            }(document, "script", "twitter-wjs"));
        }
    }, []);

    return (
        <div dangerouslySetInnerHTML={{ __html: props.htmlContent }} />
    );
};

const FacebookEmbed = (props) => {
    const iframeUrl = 'https://www.facebook.com/plugins/page.php?href=' + props.url +
        '&tabs=timeline&width=' + props.width + '&height=600&small_header=false&adapt_container_width=true&hide_cover=false&' +
        'show_facepile=true&appId=219062691446351';

    return (
        <iframe src={iframeUrl} width={props.width} height="500" scrolling="no" frameBorder="0"
                style={{ border: 'none', overflow: 'hidden', width: props.width }} allowTransparency />
    );
};

const FacebookVideo = (props) => {
    return (
        <iframe src={'https://www.facebook.com/plugins/video.php?height=451&width=750&href='+ props.url +'&show_text=false'}
                width="476" height="476" scrolling="no" frameBorder="0" style={{ border: 'none', overflow: 'hidden' }}
                allowFullScreen allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" />
    );
};

const FacebookPost = (props) => {
    return (
        <iframe src={props.url} style={{ border: 'none', overflow: 'hidden' }} scrolling="no" allowFullScreen="true"
            allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" width="500" height="883"
            frameBorder="0" />
    );
};

const YoutubeVideo = (props) => {
    return (
        <iframe src={props.url} width="1110" height="705" scrolling="no" frameBorder="0"
                style={{ border: 'none', overflow: 'hidden' }} allowFullScreen />
    );
};

const GoogleMap = (props) => {
    return (
        <iframe scrolling="no" marginHeight="0" marginWidth="0" className="mg1" src={props.url}
                width="100%" height="400" frameBorder="0" />
    );
};
