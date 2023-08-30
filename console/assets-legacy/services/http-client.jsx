import axios from 'axios';

export function createUrlEncoded(payload) {
    const params = new URLSearchParams();

    for (let key in payload) {
        if (payload.hasOwnProperty(key)) {
            params.append(key, payload[key]);
        }
    }

    return params;
}

export const httpClient = axios.create({
    method: 'get',
    baseURL: '/',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
    },
    timeout: 15000,
    withCredentials: false,
    responseType: 'json',
});

httpClient.interceptors.request.use(
    (config) => {
        const token = window.Citipo.token;
        if (token) {
            config.headers = {
                'X-XSRF-TOKEN': token,
                ...config.headers,
            };
        }

        return config;
    },
    (error) => Promise.reject(error)
);
