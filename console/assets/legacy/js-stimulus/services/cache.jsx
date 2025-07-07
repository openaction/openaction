/*
 * Easy-to-use local memory cache.
 */
class Cache {
    constructor() {
        window._cache = {};
    }

    has(key) {
        return typeof window._cache[key] !== 'undefined';
    }

    set(key, value) {
        window._cache[key] = value;
    }

    get(key) {
        return window._cache[key];
    }

    delete(key) {
        if (typeof window._cache[key] !== 'undefined') {
            delete window._cache[key];
        }
    }
}

export const cache = new Cache();
