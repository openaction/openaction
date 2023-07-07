import { cache } from './cache';

/*
 * Read data exposed from Symfony in a dedicated JSON script tag.
 */
class ExposedDataReader {
    read(key, defaultValue = null) {
        let exposedData = cache.get('exposed-data');

        if (typeof exposedData === 'undefined') {
            const tag = document.getElementById('exposed-data');
            exposedData = tag ? JSON.parse(tag.innerText) : {};

            cache.set('exposed-data', exposedData);
        }

        return exposedData[key] ?? defaultValue;
    }
}

export const exposedDataReader = new ExposedDataReader();
