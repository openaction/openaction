import React from 'react';
import { CrmFacetFormatters, CrmFacetName, CrmFacetsDistribution, CrmFacetView } from '../Model/crm';
import { Spinner, SpinnerSize } from '@blueprintjs/core';
import { CrmFacetSelector } from './CrmFacetSelector';

export interface CrmFacetsRegistries {
    projectsNames: { [key: string]: string };
}

export interface CrmFacetsLabels {
    tags_names: string;
    projects: string;
    status: string;
    profile_company: string;
    area_country_code: string;
    area_province_name: string;
    area_district_name: string;
    includeFilter: string;
    excludeFilter: string;
    cancelFilter: string;
    valueSearch: string;
}

interface Props {
    facets: CrmFacetsDistribution;
    facetsView: CrmFacetView;
    facetsFormatters: CrmFacetFormatters;
    showProjectFacet: boolean;
    onFilterInclude: (facetName: CrmFacetName, value: string) => void;
    onFilterExclude: (facetName: CrmFacetName, value: string) => void;
    onFilterCancel: (facetName: CrmFacetName, value: string) => void;
    facetsLabels: CrmFacetsLabels;
}

export function CrmFacetsList(props: Props) {
    if (!props.facetsView) {
        return <Spinner size={SpinnerSize.SMALL} />;
    }

    let facetsList: CrmFacetName[] = [
        'tags_names',
        'status',
        'profile_company',
        'area_country_code',
        'area_province_name',
        'area_district_name',
    ];

    if (props.showProjectFacet) {
        facetsList.push('projects');
    }

    return (
        <>
            {facetsList.map((facetName) => {
                const values = props.facetsView[facetName] || null;
                if (!values || values.length === 0) {
                    return <div key={facetName} />;
                }

                return (
                    <CrmFacetSelector
                        key={facetName}
                        facet={facetName}
                        values={values}
                        formatValue={props.facetsFormatters[facetName]}
                        onFilterInclude={(value) => props.onFilterInclude(facetName, value)}
                        onFilterExclude={(value) => props.onFilterExclude(facetName, value)}
                        onFilterCancel={(value) => props.onFilterCancel(facetName, value)}
                        facetsLabels={props.facetsLabels}
                    />
                );
            })}
        </>
    );
}
