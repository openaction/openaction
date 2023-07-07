import { Index, MeiliSearch } from 'meilisearch';
import { CrmDocument } from '../components/Crm/Model/crm';

export function createMeiliSearchIndex(endpoint: string, token: string, index: string): Index<CrmDocument> {
    return new MeiliSearch({ host: endpoint, apiKey: token }).index<CrmDocument>(index);
}
