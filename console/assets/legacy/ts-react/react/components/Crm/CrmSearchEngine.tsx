import React, { useEffect, useState } from 'react';
import { Tag, TagsRegistry } from '../Layout/TagsSelector/Model/tag';
import { CrmItemProfileLabels } from './Results/CrmItemProfile';
import { CrmItemLabels, CrmItemLinks } from './Results/CrmItem';
import { CrmDocument, CrmFacetFormatters, CrmFacetName, CrmFacetsDistribution, SearchEngine } from './Model/crm';
import { CrmSearchFieldLabels } from './Results/CrmSearchField';
import { CrmSearchField } from './Results/CrmSearchField';
import { CrmList, CrmListLabels } from './Results/CrmList';
import { CrmFacetsList, CrmFacetsLabels, CrmFacetsRegistries } from './Results/CrmFacetsList';
import { CrmItemTagsLabels } from './Results/CrmItemTags';
import { countryName } from '../../../utils/formatter';
import { CrmActions, CrmActionsBatchEndpoints, CrmActionsLabels } from './Results/CrmActions';

export type UpdateTagsCallback = (contactUuid: string, tags: Tag[]) => void;

interface Props {
    searchEngine: SearchEngine;
    isProjectView: boolean;
    isReadOnly: boolean;
    batch: CrmActionsBatchEndpoints;
    updateTagsCallback: UpdateTagsCallback;
    tagsRegistry: TagsRegistry;
    tagsNamesRegistry: string[];
    facetsRegistries?: CrmFacetsRegistries;
    links: CrmItemLinks;
    searchFieldLabels: CrmSearchFieldLabels;
    listLabels: CrmListLabels;
    facetsLabels: CrmFacetsLabels;
    actionsLabels: CrmActionsLabels;
    itemLabels: CrmItemLabels;
    tagsLabels: CrmItemTagsLabels;
    profileLabels: CrmItemProfileLabels;
    fakeEmailSuffix: string;
}

export function CrmSearchEngine(props: Props) {
    const [results, setResults] = useState<null | CrmDocument[]>(null);
    const [resultsLoading, setResultsLoading] = useState<boolean>(false);
    const [hasMoreResults, setHasMoreResults] = useState<boolean>(true);
    const [moreResultsLoading, setMoreResultsLoading] = useState<boolean>(false);
    const [facets, setFacets] = useState<null | CrmFacetsDistribution>(null);
    const [openedResults, setOpenedResults] = useState<string[]>([]);
    const [nbHits, setNbHits] = useState<number | null>(null);

    const refreshResults = () => {
        setResultsLoading(true);
        props.searchEngine.refreshResults().then((response) => {
            setResultsLoading(false);
            setResults(response.hits);
            setNbHits(response.nbHits);
            setFacets(response.facets);
            setHasMoreResults(response.hasMoreResults);
        });
    };

    // Initial results
    useEffect(() => refreshResults(), []);

    // Handle loading more
    const handleLoadMoreClick = () => {
        setMoreResultsLoading(true);
        props.searchEngine.loadMore().then((response) => {
            setResults(response.hits);
            setNbHits(response.nbHits);
            setFacets(response.facets);
            setMoreResultsLoading(false);
            setHasMoreResults(response.hasMoreResults);
        });
    };

    // Handle items details
    const handleItemClick = (item: CrmDocument) => {
        if (openedResults.indexOf(item.id) > -1) {
            setOpenedResults(openedResults.filter((i) => i !== item.id));
        } else {
            setOpenedResults(openedResults.concat([item.id]));
        }
    };

    const facetsView = props.searchEngine.getFacetsView();

    const facetsFormatters: CrmFacetFormatters = {
        tags_names: (v) => v,
        projects: (v) => (props.facetsRegistries ? props.facetsRegistries.projectsNames[v] || '' : v),
        status: (v) => props.itemLabels.status[v],
        profile_company: (v) => v,
        area_country_code: (v) => countryName(v),
        area_province_name: (v) => v,
        area_district_name: (v) => v,
    };

    return (
        <div className="crm-search" role={resultsLoading ? 'crm-loading' : 'crm-ready'}>
            <CrmSearchField
                key={props.searchEngine.getQuery()}
                value={props.searchEngine.getQuery()}
                isQueryLanguage={props.searchEngine.isQueryLanguage()}
                facetsView={facetsView}
                facetsFormatters={facetsFormatters}
                showProjectFacet={!props.isProjectView}
                labels={props.searchFieldLabels}
                onQueryChange={(query) => {
                    props.searchEngine.setQuery(query);
                    refreshResults();
                }}
                onModeChange={(isQueryLanguage) => {
                    props.searchEngine.setMode(isQueryLanguage);
                    refreshResults();
                }}
            />

            <div className="crm-search-results">
                <div className="crm-search-results-list">
                    <CrmList
                        results={results}
                        resultsLoading={resultsLoading}
                        sortField={props.searchEngine.getSortField()}
                        sortType={props.searchEngine.getSortType()}
                        onSortChange={(field, type) => {
                            props.searchEngine.setSort(field, type);
                            refreshResults();
                        }}
                        openedItems={openedResults}
                        hasMoreResults={hasMoreResults}
                        onItemClick={handleItemClick}
                        onLoadMoreClick={handleLoadMoreClick}
                        isLoadingMore={moreResultsLoading}
                        {...props}
                    />
                </div>
                <div className="crm-search-results-side">
                    <div className="crm-search-results-actions">
                        <CrmActions
                            nbHits={nbHits}
                            batch={props.batch}
                            createBatchPayload={() => props.searchEngine.getBatchPayload()}
                            tagsRegistry={props.tagsRegistry}
                            actionsLabels={props.actionsLabels}
                            onActionFinished={() => {
                                refreshResults();
                            }}
                            onResetClick={() => {
                                props.searchEngine.resetFilters();
                                refreshResults();
                            }}
                        />
                    </div>

                    {Object.keys(facetsView).length > 0 ? (
                        <div className="crm-search-results-facets">
                            <CrmFacetsList
                                facets={facets}
                                facetsView={facetsView}
                                facetsFormatters={facetsFormatters}
                                showProjectFacet={!props.isProjectView}
                                facetsLabels={props.facetsLabels}
                                onFilterInclude={(facetName: CrmFacetName, value: string) => {
                                    props.searchEngine.includeFilter(facetName, value);
                                    refreshResults();
                                }}
                                onFilterExclude={(facetName: CrmFacetName, value: string) => {
                                    props.searchEngine.excludeFilter(facetName, value);
                                    refreshResults();
                                }}
                                onFilterCancel={(facetName: CrmFacetName, value: string) => {
                                    props.searchEngine.cancelFilter(facetName, value);
                                    refreshResults();
                                }}
                            />
                        </div>
                    ) : (
                        ''
                    )}
                </div>
            </div>
        </div>
    );
}
