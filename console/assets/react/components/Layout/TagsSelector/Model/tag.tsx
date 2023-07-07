import Fuse from 'fuse.js';
import { request } from '../../../../../utils/http';

export interface Tag {
    id: number;
    name: string;
    slug: string;
}

export function fuzzySearchTag(tagsList: Tag[], search: string): Tag[] {
    if (!search) {
        return tagsList;
    }

    const fuse = new Fuse(tagsList, {
        includeScore: true,
        keys: ['name'],
    });

    return fuse.search(search).map((result) => result.item);
}

export class TagsRegistry {
    private endpoint: string;
    private loaded: boolean = false;
    private tags: { [key: number]: Tag } = {};

    constructor(endpoint: string) {
        this.endpoint = endpoint;
    }

    loadTags(): Promise<void> {
        return new Promise((resolve) => {
            if (this.loaded) {
                resolve();
                return;
            }

            this.loaded = true;

            request('GET', this.endpoint).then((res) => {
                Object.keys(res.data).forEach((tagId) => {
                    this.tags[parseInt(tagId)] = {
                        id: parseInt(tagId),
                        name: res.data[tagId].name,
                        slug: res.data[tagId].slug,
                    };
                });

                resolve();
            });
        });
    }

    getTags(): Tag[] {
        let tags = [];
        for (let i in this.tags) {
            tags.push(this.tags[i]);
        }

        return tags;
    }

    getTag(id: number): Tag | null {
        return this.tags[id] || null;
    }
}
