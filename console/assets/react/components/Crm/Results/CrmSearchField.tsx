import { InputGroup, Tag } from '@blueprintjs/core';
import React, { useEffect, useState } from 'react';
import { CrmFacetFormatters, CrmFacetValueView, CrmFacetView } from '../Model/crm';
import { SlateButton } from '../../Layout/SlateButton';
import { isQueryValid } from '../Model/queryParser';

export interface CrmSearchFieldLabels {
    placeholder: string;
    modeSimple: string;
    modeAdvanced: string;
    enterQuery: string;
    runQuery: string;
}

interface Props {
    value: string;
    isQueryLanguage: boolean;
    onQueryChange: (value: string) => void;
    onModeChange: (isQueryLanguage: boolean) => void;
    facetsView: CrmFacetView;
    facetsFormatters: CrmFacetFormatters;
    showProjectFacet: boolean;
    labels: CrmSearchFieldLabels;
}

export function CrmSearchField(props: Props) {
    const [query, setQuery] = useState(props.value);

    useEffect(() => {
        let timer = undefined;

        // Trigger standard field changes with a slight delay
        // (query language changes are run using a button)
        if (!props.isQueryLanguage) {
            timer = setTimeout(() => props.onQueryChange(query), 300);
        }

        return () => clearTimeout(timer);
    }, [query]);

    return (
        <div className="crm-search-field">
            <InputGroup
                large={true}
                autoFocus={true}
                placeholder={props.isQueryLanguage ? props.labels.enterQuery : props.labels.placeholder}
                leftIcon={props.isQueryLanguage ? <i className="fal fa-code" /> : <i className="fal fa-search" />}
                rightElement={<CrmSearchFieldFacets {...props} />}
                value={query}
                style={{
                    fontFamily: props.isQueryLanguage ? 'monospace' : '',
                    fontSize: props.isQueryLanguage ? '12px' : '',
                    boxShadow: !props.isQueryLanguage || isQueryValid(query) ? '' : 'inset 0 0 0 2px rgb(205, 66, 70)',
                }}
                onChange={(e) => setQuery(e.currentTarget.value)}
                onKeyDown={(e) => {
                    if (e.key === 'Enter' && props.isQueryLanguage) {
                        props.onQueryChange(query);
                    }
                }}
            />

            {props.isQueryLanguage ? (
                <SlateButton
                    text={props.labels.runQuery}
                    icon={<i className="far fa-play" />}
                    className="ml-2"
                    minimal={true}
                    outlined={false}
                    onClick={() => props.onQueryChange(query)}
                />
            ) : (
                ''
            )}

            <SlateButton
                text={props.isQueryLanguage ? props.labels.modeSimple : props.labels.modeAdvanced}
                icon={<i className="far fa-repeat" />}
                className="ml-2"
                minimal={true}
                outlined={false}
                onClick={() => props.onModeChange(!props.isQueryLanguage)}
            />
        </div>
    );
}

function CrmSearchFieldFacets(props: Props) {
    let values: { facetName: string; formattedValue: string; status: 'inactive' | 'include' | 'exclude' }[] = [];

    for (let facetName in props.facetsView) {
        props.facetsView[facetName]
            .filter((v: CrmFacetValueView) => v.status !== 'inactive')
            .forEach((v: CrmFacetValueView) =>
                values.push({
                    facetName: facetName,
                    formattedValue: props.facetsFormatters[facetName](v.value),
                    status: v.status,
                })
            );
    }

    // Sort alphabetically
    values.sort((a, b) => a.formattedValue.localeCompare(b.formattedValue));

    const facetsIcons = {
        tags_names: 'far fa-tags',
        projects: 'far fa-cubes',
        status: 'far fa-users',
        area_country_code: 'far fa-globe-europe',
        area_province_name: 'far fa-map-marked-alt',
        area_district_name: 'far fa-map-marked-alt',
    };

    return (
        <>
            {values.map((v, key) => {
                if (v.facetName === 'projects' && !props.showProjectFacet) {
                    return '';
                }

                return (
                    <Tag
                        key={v.facetName + '-' + v.formattedValue + '-' + key}
                        minimal={true}
                        intent={v.status === 'exclude' ? 'danger' : 'none'}
                        icon={<i className={facetsIcons[v.facetName]} />}
                    >
                        {v.formattedValue}
                    </Tag>
                );
            })}
        </>
    );
}
