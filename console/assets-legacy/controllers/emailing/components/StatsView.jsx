import React, { useState, useEffect } from 'react';
import { translator } from '../../../services/translator';
import { httpClient } from '../../../services/http-client';
import dayjs from 'dayjs';

export function StatsView(props) {
    const [stats, setStats] = useState(null);

    const refreshStats = () => {
        httpClient.get(props.url).then(({ data }) => {
            setStats(data);

            if (props.sentAt.isAfter(dayjs().subtract(12, 'hour'))) {
                // Update every minute if sent less than 12 hours ago
                setTimeout(() => refreshStats(), 60_000);
            }
        });
    };

    // Initial refresh start
    useEffect(() => refreshStats(), []);

    if (!stats) {
        return (
            <div>
                <i className="fal fa-circle-notch fa-spin" />
            </div>
        );
    }

    if (!stats.total) {
        return (
            <div>
                <i className="fal fa-circle-notch fa-spin mr-1" />
                {translator.trans('emailing.stats.sending')}
            </div>
        );
    }

    const view = (
        <>
            <div className="d-inline-block mx-2">
                <i className="fad fa-users mr-1" />
                {stats.total}
                <div>
                    <small className="text-uppercase">{translator.trans('emailing.stats.sent')}</small>
                </div>
            </div>

            <div className="d-inline-block mx-2">
                <i className="fad fa-envelope-open-text mr-1" />
                {stats.opened}
                <div>
                    <small className="text-uppercase">{translator.trans('emailing.stats.opened')}</small>
                </div>
            </div>

            <div className="d-inline-block mx-2">
                <i className="fad fa-external-link mr-1" />
                {stats.clicked}
                <div>
                    <small className="text-uppercase">{translator.trans('emailing.stats.clicked')}</small>
                </div>
            </div>
        </>
    );

    if (!props.link) {
        return <div className="d-block">{view}</div>;
    }

    return <a href={props.link} className="d-block">{view}</a>;
}
