import get from 'lodash.get';
import { exposedDataReader } from './exposed-data-reader';

const catalog = {
    en: require('../translations/en.json'),
    fr: require('../translations/fr.json'),
};

/*
 * Translate JS strings depending on the locale exposed by Symfony.
 */
class Translator {
    trans(key, locale = null) {
        locale = locale ?? exposedDataReader.read('locale', 'en');

        if (!catalog[locale]) {
            throw new Error(`Locale '${locale}' not available`);
        }

        return get(catalog[locale], key, key);
    }
}

export const translator = new Translator();
