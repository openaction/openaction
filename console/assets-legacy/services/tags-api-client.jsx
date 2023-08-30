import { httpClient } from './http-client';
import { cache } from './cache';

/*
 * Read the Console tags API.
 */
class TagsApiClient {
    fetchTags(endpoint, callback) {
        if (cache.has('tags-' + endpoint)) {
            callback(cache.get('tags-' + endpoint));
        } else {
            httpClient.get(endpoint).then((res) => {
                callback(res.data);
                cache.set('tags-' + endpoint, res.data);
            });
        }
    }
}

export const tagsApiClient = new TagsApiClient();
