import React from 'react';
import ReactTimeAgo, { ReactTimeagoProps } from 'react-timeago';
import buildFormatter from 'react-timeago/lib/formatters/buildFormatter';

const TRANSLATIONS = {
    en: {
        prefixAgo: null,
        prefixFromNow: null,
        suffixAgo: 'ago',
        suffixFromNow: 'from now',
        seconds: 'less than a minute',
        minute: 'a minute',
        minutes: '%d minutes',
        hour: 'an hour',
        hours: 'about %d hours',
        day: 'a day',
        days: '%d days',
        month: 'a month',
        months: '%d months',
        year: 'a year',
        years: '%d years',
        wordSeparator: ' ',
    },
    fr: {
        prefixAgo: 'il y a',
        prefixFromNow: 'dans',
        seconds: "moins d'une minute",
        minute: 'une minute',
        minutes: '%d minutes',
        hour: 'une heure',
        hours: '%d heures',
        day: 'un jour',
        days: '%d jours',
        month: 'un mois',
        months: '%d mois',
        year: 'un an',
        years: '%d ans',
    },
};

export function TimeAgo(props: ReactTimeagoProps) {
    const options: ReactTimeagoProps = Object.assign({}, props, {
        formatter: buildFormatter(TRANSLATIONS[window.Citipo.locale] || TRANSLATIONS['en']),
    });

    return <ReactTimeAgo {...options} />;
}
