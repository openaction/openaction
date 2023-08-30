import React, { useState } from 'react';
import { CrmFacetName, CrmFacetValueView } from '../Model/crm';
import { Button, ControlGroup, InputGroup, Menu, MenuItem } from '@blueprintjs/core';
import { Popover2 } from '@blueprintjs/popover2';
import { CrmFacetsLabels } from './CrmFacetsList';
import Fuse from 'fuse.js';

interface Props {
    facet: CrmFacetName;
    values: CrmFacetValueView[];
    formatValue: (value: string) => string;
    onFilterInclude: (value: string) => void;
    onFilterExclude: (value: string) => void;
    onFilterCancel: (value: string) => void;
    facetsLabels: CrmFacetsLabels;
}

export function CrmFacetSelector(props: Props) {
    const [valueFilter, setValueFilter] = useState('');

    const sortedValues = props.values.sort((a: CrmFacetValueView, b: CrmFacetValueView) => {
        // First sort by status
        const STATUS_ORDER = { include: 2, exclude: 1, inactive: 0 };
        if (a.status !== b.status) {
            return STATUS_ORDER[a.status] > STATUS_ORDER[b.status] ? -1 : 1;
        }

        // Then alphabetically
        return props.formatValue(a.value).localeCompare(props.formatValue(b.value));
    });

    const displayedValues = fuzzySearchFacetValue(sortedValues, valueFilter);

    return (
        <div className="crm-search-results-facet">
            <div className="crm-search-results-facet-title">{props.facetsLabels[props.facet]}</div>

            {sortedValues.length > 10 ? (
                <div className="crm-search-results-facet-search">
                    <InputGroup
                        value={valueFilter}
                        onChange={(e) => setValueFilter(e.currentTarget.value)}
                        placeholder={props.facetsLabels.valueSearch}
                        rightElement={
                            valueFilter ? (
                                <Button outlined={true} style={{ border: 0 }} onClick={() => setValueFilter('')}>
                                    <i className="far fa-times" />
                                </Button>
                            ) : (
                                <div />
                            )
                        }
                    />
                </div>
            ) : (
                ''
            )}

            {displayedValues.slice(0, 10).map((row) => {
                const label = props.formatValue(row.value);
                if (label === '') {
                    return '';
                }

                return (
                    <div
                        className={'crm-search-results-facet-value crm-search-results-facet-value-' + row.status}
                        key={row.value}
                        onClick={() => {
                            if (row.status === 'inactive') {
                                props.onFilterInclude(row.value);
                            } else {
                                props.onFilterCancel(row.value);
                            }
                        }}
                    >
                        <div className="crm-search-results-facet-value-label">{props.formatValue(row.value)}</div>

                        <div className="crm-search-results-facet-value-count">{row.count}</div>

                        <div className="crm-search-results-facet-value-actions" onClick={(e) => e.stopPropagation()}>
                            <Popover2
                                content={<CrmFacetMenu value={row.value} status={row.status} {...props} />}
                                position="top-left"
                            >
                                <Button
                                    outlined={true}
                                    small={true}
                                    className="crm-search-results-facet-value-actions-button"
                                >
                                    <i className="far fa-ellipsis-h" />
                                </Button>
                            </Popover2>
                        </div>
                    </div>
                );
            })}
        </div>
    );
}

interface CrmFacetMenuProps {
    value: string;
    status: 'inactive' | 'include' | 'exclude';
    onFilterInclude: (value: string) => void;
    onFilterExclude: (value: string) => void;
    onFilterCancel: (value: string) => void;
    facetsLabels: CrmFacetsLabels;
}

function CrmFacetMenu(props: CrmFacetMenuProps) {
    return (
        <Menu>
            {props.status !== 'include' ? (
                <MenuItem text={props.facetsLabels.includeFilter} onClick={() => props.onFilterInclude(props.value)} />
            ) : (
                ''
            )}

            {props.status !== 'exclude' ? (
                <MenuItem text={props.facetsLabels.excludeFilter} onClick={() => props.onFilterExclude(props.value)} />
            ) : (
                ''
            )}

            {props.status !== 'inactive' ? (
                <MenuItem text={props.facetsLabels.cancelFilter} onClick={() => props.onFilterCancel(props.value)} />
            ) : (
                ''
            )}
        </Menu>
    );
}

function fuzzySearchFacetValue(values: CrmFacetValueView[], search: string): CrmFacetValueView[] {
    if (!search) {
        return values;
    }

    const fuse = new Fuse(values, {
        includeScore: true,
        keys: ['value'],
    });

    return fuse.search(search).map((result) => result.item);
}
