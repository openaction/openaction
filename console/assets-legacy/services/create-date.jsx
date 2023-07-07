import { exposedDataReader } from './exposed-data-reader';
import dayjs from 'dayjs';
import localizedFormat from 'dayjs/plugin/localizedFormat';
import relativeTime from 'dayjs/plugin/relativeTime';
import customParseFormat from 'dayjs/plugin/customParseFormat';
import 'dayjs/locale/en';
import 'dayjs/locale/fr';

dayjs.extend(localizedFormat);
dayjs.extend(relativeTime);
dayjs.extend(customParseFormat);
dayjs.locale(exposedDataReader.read('locale', 'en'));

export const createDate = dayjs;
