import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import localizedFormat from 'dayjs/plugin/localizedFormat';

import 'dayjs/locale/fr';
import 'dayjs/locale/en';

import { exposedDataReader } from '../../../../services/exposed-data-reader';

dayjs.extend(relativeTime);
dayjs.extend(localizedFormat);
dayjs.locale(exposedDataReader.read('locale', 'en'));

export function dateRenderer(params) {
    if (params.value === undefined) {
        return '<div class="community-contact-loader"><i class="fal fa-circle-notch fa-spin"></i></div>';
    }

    const date = dayjs(params.value);

    // If the creation happened less then 1 week ago, display relative time
    if (date.isAfter(dayjs().subtract(7, 'day'))) {
        const formatted = date.from(dayjs());

        return '<div class="community-contact-value">' + formatted[0].toUpperCase() + formatted.substr(1) + '</div>';
    }

    return '<div class="community-contact-value">' + date.format('l LT') + '</div>';
}
