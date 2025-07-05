import React, { useState } from 'react';
import { translator } from '../../services/translator';
import { FacebookSharer } from './Network/FacebookSharer';
import { TwitterSharer } from './Network/TwitterSharer';
import { LinkedInSharer } from './Network/LinkedInSharer';
import { TelegramSharer } from './Network/TelegramSharer';
import { WhatsappSharer } from './Network/WhatsappSharer';

const getHost = (url) => {
    const link = document.createElement('a');
    link.href = url;

    return link.hostname;
};

const networks = {
    facebook: {
        icon: 'fab fa-facebook',
        buildView: (metadata) => {
            return <FacebookSharer metadata={metadata} />;
        },
    },
    twitter: {
        icon: 'fab fa-twitter',
        buildView: (metadata) => {
            return <TwitterSharer metadata={metadata} />;
        },
    },
    linkedin: {
        icon: 'fab fa-linkedin',
        buildView: (metadata) => {
            return <LinkedInSharer metadata={metadata} />;
        },
    },
    telegram: {
        icon: 'fab fa-telegram-plane',
        buildView: (metadata) => {
            return <TelegramSharer metadata={metadata} />;
        },
    },
    whatsapp: {
        icon: 'fab fa-whatsapp',
        buildView: (metadata) => {
            return <WhatsappSharer metadata={metadata} />;
        },
    },
};

export function SocialSharer(props) {
    const [activeNetwork, setActiveNetwork] = useState('facebook');

    return (
        <div className="row no-gutters">
            <div className="col-lg-3 social-sharer-menu">
                <div className="list-group list-group-flush">
                    {Object.keys(networks).map((network) => (
                        <button
                            type="button"
                            key={network + '-' + activeNetwork}
                            className={
                                'list-group-item list-group-item-action ' + (network === activeNetwork ? 'active' : '')
                            }
                            onClick={() => setActiveNetwork(network)}
                        >
                            <i className={networks[network].icon + ' mr-2'}></i>
                            {translator.trans('social_sharer.menu.' + network)}
                        </button>
                    ))}
                </div>
            </div>
            <div className="col-lg-9">
                <div className="social-sharer-view">
                    {networks[activeNetwork].buildView({
                        url: props.url,
                        host: getHost(props.url),
                        title: props.title,
                        imageUrl: props.imageUrl,
                        description: props.description,
                    })}
                </div>
            </div>
        </div>
    );
}
