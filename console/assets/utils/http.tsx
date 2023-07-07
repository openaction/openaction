import axios, { Options } from 'redaxios';

export function request(method, url, options: Options = {}) {
    options.headers = options.headers || {};
    options.headers['X-Requested-With'] = 'XMLHttpRequest';
    options.headers['X-XSRF-TOKEN'] = window.Citipo ? window.Citipo.token || '' : '';

    options = Object.assign(
        {
            method: method,
            url: url,
            timeout: 15000,
            withCredentials: false,
            responseType: 'json',
        },
        options
    );

    return axios.request(options as any);
}

export function createUrlEncoded(payload) {
    const params = new URLSearchParams();

    for (let key in payload) {
        if (payload.hasOwnProperty(key)) {
            params.append(key, payload[key]);
        }
    }

    return params;
}
