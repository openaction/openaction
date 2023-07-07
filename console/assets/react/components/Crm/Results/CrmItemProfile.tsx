import { CrmDocument } from '../Model/crm';
import React, { ReactElement, useEffect, useState } from 'react';
import { dateFormat } from '../../../../utils/formatter';
import { request } from '../../../../utils/http';
import { Spinner, SpinnerSize } from '@blueprintjs/core';
import { CrmItemAddress } from './CrmItemAddress';
import { CrmItemLinks } from './CrmItem';
import { createLink } from '../../../../utils/createLink';

export interface CrmItemProfileLabels {
    historyTitle: string;
    historyDescription: string;
    newsletter: string;
    sms: string;
    calls: string;
    birthdate: string;
    address: string;
    phone: string;
    work: string;
    socials: string;
    projects: string;
}

interface Props {
    result: CrmDocument;
    links: CrmItemLinks;
    labels: CrmItemProfileLabels;
}

export function CrmItemProfile(props: Props) {
    return (
        <div className="crm-search-results-item-more">
            <div className="crm-search-results-item-history">
                <strong>{props.labels.historyTitle}</strong>

                <div>{props.labels.historyDescription}</div>

                <CrmListItemProfileHistory {...props} />
            </div>

            <div className="crm-search-results-item-profile">
                <CrmListItemProfileInformation {...props} />
            </div>
        </div>
    );
}

function CrmListItemProfileHistory(props: Props) {
    const item = props.result;
    const [history, setHistory] = useState<string | null>(null);

    useEffect(() => {
        request('GET', createLink(props.links.history, { '-uuid-': item.id }), { responseType: 'text' }).then((res) => {
            setHistory(res.data);
        });
    }, []);

    if (!history) {
        return (
            <div className="mt-5 mb-5">
                <Spinner size={SpinnerSize.SMALL} />
            </div>
        );
    }

    return <div className="mt-3" dangerouslySetInnerHTML={{ __html: history }} />;
}

function CrmListItemProfileInformation(props: Props) {
    const item = props.result;

    let values: { [key: string]: ReactElement } = {
        [props.labels.newsletter]: (
            <div>
                <div
                    className={
                        'crm-search-results-item-profile-subscription crm-search-results-item-profile-subscription-' +
                        (item.settings_receive_newsletters ? 'subscribed' : 'unsubscribed')
                    }
                >
                    <i className={'far fa-' + (item.settings_receive_newsletters ? 'check' : 'times')} />
                    {props.labels.newsletter}
                </div>
            </div>
        ),
        [props.labels.sms]: (
            <div>
                <div
                    className={
                        'crm-search-results-item-profile-subscription crm-search-results-item-profile-subscription-' +
                        (item.settings_receive_sms ? 'subscribed' : 'unsubscribed')
                    }
                >
                    <i className={'far fa-' + (item.settings_receive_sms ? 'check' : 'times')} />
                    {props.labels.sms}
                </div>
            </div>
        ),
        [props.labels.calls]: (
            <div>
                <div
                    className={
                        'crm-search-results-item-profile-subscription crm-search-results-item-profile-subscription-' +
                        (item.settings_receive_calls ? 'subscribed' : 'unsubscribed')
                    }
                >
                    <i className={'far fa-' + (item.settings_receive_calls ? 'check' : 'times')} />
                    {props.labels.calls}
                </div>
            </div>
        ),
    };

    if (item.contact_phone) {
        values[props.labels.phone] = (
            <div className="crm-search-results-item-profile-section">
                <div className="crm-search-results-item-profile-section-label">{props.labels.phone}</div>
                <div className="crm-search-results-item-profile-section-value">{item.contact_phone}</div>
            </div>
        );
    }

    if (item.profile_birthdate) {
        values[props.labels.birthdate] = (
            <div className="crm-search-results-item-profile-section">
                <div className="crm-search-results-item-profile-section-label">{props.labels.birthdate}</div>
                <div className="crm-search-results-item-profile-section-value">
                    {dateFormat(item.profile_birthdate)}
                </div>
            </div>
        );
    }

    if (item.address_street_line1 || item.address_city || item.address_zip_code) {
        values[props.labels.address] = (
            <div className="crm-search-results-item-profile-section">
                <div className="crm-search-results-item-profile-section-label">{props.labels.address}</div>
                <div className="crm-search-results-item-profile-section-value">
                    <CrmItemAddress item={item} />
                </div>
            </div>
        );
    }

    if (
        item.social_facebook ||
        item.social_twitter ||
        item.social_linked_in ||
        item.social_whatsapp ||
        item.social_telegram
    ) {
        values[props.labels.socials] = (
            <div className="crm-search-results-item-profile-section">
                <div className="crm-search-results-item-profile-section-label">{props.labels.socials}</div>
                <div className="crm-search-results-item-profile-section-socials">
                    {item.social_facebook ? (
                        <a href={item.social_facebook} target="_blank">
                            <i className="fab fa-facebook" />
                        </a>
                    ) : (
                        ''
                    )}
                    {item.social_twitter ? (
                        <a href={item.social_twitter} target="_blank">
                            <i className="fab fa-twitter" />
                        </a>
                    ) : (
                        ''
                    )}
                    {item.social_linked_in ? (
                        <a href={item.social_linked_in} target="_blank">
                            <i className="fab fa-linkedin" />
                        </a>
                    ) : (
                        ''
                    )}
                    {item.social_telegram ? (
                        <a href={'https://t.me/' + item.social_telegram} target="_blank">
                            <i className="fab fa-telegram" />
                        </a>
                    ) : (
                        ''
                    )}
                    {item.social_whatsapp ? (
                        <a href={'https://wa.me/' + item.social_whatsapp} target="_blank">
                            <i className="fab fa-whatsapp" />
                        </a>
                    ) : (
                        ''
                    )}
                </div>
            </div>
        );
    }

    return (
        <>
            {Object.keys(values).map((section) => (
                <div key={section}>{values[section]}</div>
            ))}
        </>
    );
}
