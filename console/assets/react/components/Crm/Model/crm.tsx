import { CategoriesDistribution, Index } from 'meilisearch';
import { createMeiliSearchIndex } from '../../../bridge/meilisearch';
import { parseQueryToSearchFilters } from './queryParser';

export interface CrmDocument {
    id: string | null;
    email: string;
    contact_additional_emails: string[];
    contact_phone: string | null;
    profile_first_name: string | null;
    profile_last_name: string | null;
    profile_birthdate: string | null;
    profile_company: string | null;
    profile_company_slug: string | null;
    profile_job_title: string | null;
    profile_job_title_slug: string | null;
    profile_age: number | null;
    profile_gender: string | null;
    address_street_line1: string | null;
    address_street_line2: string | null;
    address_zip_code: string | null;
    address_city: string | null;
    address_country: string | null;
    social_facebook: string | null;
    social_twitter: string | null;
    social_linked_in: string | null;
    social_telegram: string | null;
    social_whatsapp: string | null;
    picture: string | null;
    email_hash: string | null;
    status: string | null;
    settings_receive_newsletters: boolean;
    settings_receive_sms: boolean;
    settings_receive_calls: boolean;
    created_at: string | null;
    area_country: number | null;
    area_country_code: string | null;
    area_province: number | null;
    area_province_name: string | null;
    area_district: number | null;
    area_district_name: string | null;
    area_community: number | null;
    area_community_name: string | null;
    area_zip_code: number | null;
    area_zip_code_name: string | null;
    tags: number[];
    tags_names: string[];
    projects: string[];
    projects_names: string[];
}

enum CrmFacet {
    projects = 'projects',
    area_country_code = 'area_country_code',
    area_province_name = 'area_province_name',
    area_district_name = 'area_district_name',
    tags_names = 'tags_names',
    status = 'status',
    settings_receive_newsletters = 'settings_receive_newsletters',
    settings_receive_sms = 'settings_receive_sms',
    settings_receive_calls = 'settings_receive_calls',
}

export type CrmFacetName = keyof typeof CrmFacet;

export type CrmFacetsDistribution = Record<CrmFacetName, CategoriesDistribution>;

export const FACETS_LIST = Object.keys(CrmFacet);

export type CrmFacetFilter =
    | {}
    | {
          [facetName in CrmFacetName]: {
              [value: string]: 'include' | 'exclude' | null;
          };
      };

export type CrmFacetValueView = {
    value: any;
    count: number;
    status: 'inactive' | 'include' | 'exclude';
};

export type CrmFacetView = {} | { [facetName in CrmFacetName]: CrmFacetValueView[] };

export type CrmFacetFormatters = {} | { [facetName in CrmFacetName]: (value: string) => string };

interface CrmSearchResponse {
    hits: CrmDocument[];
    facets: CrmFacetsDistribution;
    nbHits: number;
    hasMoreResults: boolean;
}

export interface CrmBatchPayload {
    queryInput: string;
    queryFilter: string[];
    querySort: string[];
}

const ITEMS_PER_PAGE = 25;

export class SearchEngine {
    private meilisearch: Index<CrmDocument>;

    // Search options
    private project: string | null = null;
    private query: string = '';
    private _isQueryLanguage: boolean = false;
    private facetsFilter: CrmFacetFilter = {};
    private offset: number = 0;
    private sortField: string = 'created_at';
    private sortType: 'asc' | 'desc' = 'desc';

    // Previous response (for loading more and facets)
    private results: CrmDocument[] = [];
    private facets: CrmFacetsDistribution | null = null;

    constructor(endpoint: string, token: string, index: string, project: string | null = null) {
        this.meilisearch = createMeiliSearchIndex(endpoint, token, index);
        this.project = project;

        if (this.project) {
            this.includeFilter('projects', this.project);
        }
    }

    refreshResults(): Promise<CrmSearchResponse> {
        return new Promise((resolve, reject) => {
            this.doSearch()
                .then((response) => {
                    this.results = response.hits;
                    this.facets = response.facetDistribution as CrmFacetsDistribution;

                    resolve({
                        hits: this.results,
                        facets: this.facets,
                        nbHits: response.estimatedTotalHits,
                        hasMoreResults: true,
                    });
                })
                .catch(reject);
        });
    }

    loadMore(): Promise<CrmSearchResponse> {
        this.offset += ITEMS_PER_PAGE;

        return new Promise((resolve, reject) => {
            this.doSearch()
                .then((response) => {
                    this.results = this.results.concat(response.hits);
                    this.facets = response.facetDistribution as CrmFacetsDistribution;

                    resolve({
                        hits: this.results,
                        facets: this.facets,
                        nbHits: response.estimatedTotalHits,
                        hasMoreResults: response.hits.length > 0,
                    });
                })
                .catch(reject);
        });
    }

    getFacetsView(): CrmFacetView {
        const FILTERS_ORDER = { include: 2, exclude: 1, inactive: 0 };

        let view: CrmFacetView = {};

        // Sort facets values by filter type and count
        let facetCounts = this.facets;
        for (let facetName in this.facets) {
            // Add excluded values (they won't come up in response as they are excluded)
            for (let value in this.facetsFilter[facetName]) {
                if (this.facetsFilter[facetName][value] === 'exclude') {
                    facetCounts[facetName][value] = 0;
                }
            }

            const currentFacetFilters = this.facetsFilter[facetName] || {};

            let sortable = [];
            for (let value in facetCounts[facetName]) {
                sortable.push([value, currentFacetFilters[value] || 'inactive', facetCounts[facetName][value]]);
            }

            sortable.sort((a, b) => {
                if (0 === FILTERS_ORDER[b[1]] - FILTERS_ORDER[a[1]]) {
                    return b[2] - a[2];
                }

                return FILTERS_ORDER[b[1]] - FILTERS_ORDER[a[1]];
            });

            view[facetName] = sortable.map((item) => {
                return {
                    value: item[0],
                    status: item[1],
                    count: item[2],
                };
            });
        }

        return view;
    }

    getActiveFacetsFilters(): CrmFacetFilter {
        let active: CrmFacetFilter = {};

        for (let facetName in this.facetsFilter) {
            for (let value in this.facetsFilter[facetName]) {
                if (this.facetsFilter[facetName][value] !== null) {
                    if (typeof active[facetName] === 'undefined') {
                        active[facetName] = {};
                    }

                    active[facetName][value] = this.facetsFilter[facetName][value];
                }
            }
        }

        return active;
    }

    setQuery(query: string) {
        this.offset = 0;
        this.query = query;
    }

    setMode(isQueryLanguage: boolean) {
        this._isQueryLanguage = isQueryLanguage;
        this.offset = 0;
        this.query = '';
    }

    setSort(fieldName: string, type: 'asc' | 'desc') {
        this.sortField = fieldName;
        this.sortType = type;
    }

    includeFilter(facetName: CrmFacetName, value: string) {
        this.setFilter(facetName, value, 'include');
    }

    excludeFilter(facetName: CrmFacetName, value: string) {
        this.setFilter(facetName, value, 'exclude');
    }

    cancelFilter(facetName: CrmFacetName, value: string) {
        this.setFilter(facetName, value, null);
    }

    resetFilters() {
        this.offset = 0;
        this.query = '';
        this.facetsFilter = {};

        if (this.project) {
            this.includeFilter('projects', this.project);
        }
    }

    getQuery(): string {
        return this.query;
    }

    isQueryLanguage(): boolean {
        return this._isQueryLanguage;
    }

    getSortField(): string {
        return this.sortField;
    }

    getSortType(): 'asc' | 'desc' {
        return this.sortType;
    }

    getBatchPayload(): CrmBatchPayload {
        let filter = this.createSearchFilter();
        if (this._isQueryLanguage) {
            filter = [parseQueryToSearchFilters(this.query), ...filter];
        }

        return {
            queryInput: this._isQueryLanguage ? '' : this.query,
            queryFilter: filter,
            querySort: this.createSearchSort(),
        };
    }

    private doSearch() {
        let filter = this.createSearchFilter();
        if (this._isQueryLanguage) {
            filter = [parseQueryToSearchFilters(this.query), ...filter];
        }

        return this.meilisearch.search<CrmDocument>(this._isQueryLanguage ? '' : this.query, {
            filter: filter,
            sort: this.createSearchSort(),
            offset: this.offset,
            limit: ITEMS_PER_PAGE,
            facets: FACETS_LIST,
            attributesToRetrieve: [
                'id',
                'email',
                'contact_additional_emails',
                'contact_phone',
                'profile_first_name',
                'profile_last_name',
                'profile_company',
                'profile_company_slug',
                'profile_job_title',
                'profile_birthdate',
                'profile_age',
                'profile_gender',
                'address_street_line1',
                'address_street_line2',
                'address_zip_code',
                'address_city',
                'address_country',
                'social_facebook',
                'social_twitter',
                'social_linked_in',
                'social_telegram',
                'social_whatsapp',
                'picture',
                'email_hash',
                'status',
                'settings_receive_newsletters',
                'settings_receive_calls',
                'settings_receive_sms',
                'created_at',
                'area_country',
                'area_country_code',
                'area_province',
                'area_province_name',
                'area_district',
                'area_district_name',
                'area_community',
                'area_community_name',
                'area_zip_code',
                'area_zip_code_name',
                'tags',
                'tags_names',
                'projects',
                'projects_names',
            ],
        });
    }

    private createSearchFilter(): string[] {
        let filter = [];
        for (let facetName in this.facetsFilter) {
            for (let value in this.facetsFilter[facetName]) {
                if (this.facetsFilter[facetName][value]) {
                    filter.push(
                        facetName +
                            ' ' +
                            (this.facetsFilter[facetName][value] === 'include' ? '=' : '!=') +
                            ' ' +
                            "'" +
                            value +
                            "'"
                    );
                }
            }
        }

        return filter;
    }

    private createSearchSort(): string[] {
        return [this.sortField + ':' + this.sortType];
    }

    private setFilter(facetName: CrmFacetName, value: string, type: 'include' | 'exclude' | null) {
        this.offset = 0;

        if (typeof this.facetsFilter[facetName] === 'undefined') {
            this.facetsFilter[facetName] = {};
        }

        this.facetsFilter[facetName][value] = type;
    }
}
