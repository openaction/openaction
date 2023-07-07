import React from 'react';
import { CrmDocument } from '../Model/crm';
import { CrmItem, CrmItemLabels, CrmItemLinks } from './CrmItem';
import { CrmItemProfileLabels } from './CrmItemProfile';
import { Button, Menu, MenuItem, NonIdealState, Spinner } from '@blueprintjs/core';
import { TagsRegistry } from '../../Layout/TagsSelector/Model/tag';
import { CrmItemTagsLabels } from './CrmItemTags';
import { UpdateTagsCallback } from '../CrmSearchEngine';
import { SlateButton } from '../../Layout/SlateButton';
import { Popover2 } from '@blueprintjs/popover2';

export interface CrmListLabels {
    location: string;
    createdAt: string;
    status: string;
    noResultsTitle: string;
    noResultsDescription: string;
    loadMore: string;
    sortBy: string;
    sortByAsc: string;
    sortByDesc: string;
    sortByDate: string;
    sortByFirstName: string;
    sortByLastName: string;
    sortByEmail: string;
}

interface Props {
    isReadOnly: boolean;
    tagsRegistry: TagsRegistry;
    tagsNamesRegistry: string[];
    updateTagsCallback: UpdateTagsCallback;
    results: CrmDocument[] | null;
    resultsLoading: boolean;
    sortField: string;
    sortType: 'asc' | 'desc';
    onSortChange: (fieldName: string, type: 'asc' | 'desc') => void;
    openedItems: string[];
    hasMoreResults: boolean;
    onItemClick: (item: CrmDocument) => void;
    onLoadMoreClick: () => void;
    isLoadingMore: boolean;
    links: CrmItemLinks;
    listLabels: CrmListLabels;
    itemLabels: CrmItemLabels;
    tagsLabels: CrmItemTagsLabels;
    profileLabels: CrmItemProfileLabels;
}

export function CrmList(props: Props) {
    if (props.results === null) {
        return <Spinner className="crm-search-results-list-loading" />;
    }

    if (props.results.length === 0) {
        return (
            <div className="crm-search-results-list-noresult">
                <NonIdealState
                    icon="search"
                    title={props.listLabels.noResultsTitle}
                    description={props.listLabels.noResultsDescription}
                />
            </div>
        );
    }

    return (
        <div className={props.resultsLoading ? 'crm-search-results-loading' : ''}>
            <div className="crm-search-results-head">
                <div className="crm-search-results-head-sort">
                    <Popover2
                        interactionKind="click"
                        placement="bottom-start"
                        content={
                            <CrmListSortMenu
                                selectedField={props.sortField}
                                selectedType={props.sortType}
                                labels={props.listLabels}
                                onChange={props.onSortChange}
                            />
                        }
                    >
                        <CrmListSortButton
                            selectedField={props.sortField}
                            selectedType={props.sortType}
                            labels={props.listLabels}
                        />
                    </Popover2>
                </div>
                <div className="crm-search-results-head-location">{props.listLabels.location}</div>
                <div className="crm-search-results-head-activity">{props.listLabels.createdAt}</div>
                <div className="crm-search-results-head-status">{props.listLabels.status}</div>
            </div>

            {props.results.map((result) => (
                <CrmItem
                    key={result.id}
                    result={result}
                    isOpen={props.openedItems.indexOf(result.id) > -1}
                    onClick={() => props.onItemClick(result)}
                    {...props}
                />
            ))}

            {props.hasMoreResults ? (
                <SlateButton
                    onClick={props.onLoadMoreClick}
                    disabled={props.isLoadingMore}
                    icon={props.isLoadingMore ? <i className="far fa-spin fa-circle-notch" /> : null}
                    className="crm-search-results-list-loadmore"
                >
                    {props.listLabels.loadMore}
                </SlateButton>
            ) : (
                ''
            )}
        </div>
    );
}

interface CrmListSortButtonProps {
    selectedField: string;
    selectedType: 'asc' | 'desc';
    labels: CrmListLabels;
}

function CrmListSortButton(props: CrmListSortButtonProps) {
    const fieldsLabels = {
        created_at: props.labels.sortByDate,
        profile_first_name: props.labels.sortByFirstName,
        profile_last_name: props.labels.sortByLastName,
        email: props.labels.sortByEmail,
    };

    return (
        <Button
            outlined={true}
            small={true}
            className="crm-search-results-head-sort-button"
            icon={<i className="far fa-sort-alt" />}
        >
            {props.labels.sortBy + ' '}
            {fieldsLabels[props.selectedField] + ' '}(
            {props.selectedType === 'asc' ? props.labels.sortByAsc : props.labels.sortByDesc})
        </Button>
    );
}

interface CrmListSortMenuProps {
    selectedField: string;
    selectedType: 'asc' | 'desc';
    labels: CrmListLabels;
    onChange: (field: string, type: 'asc' | 'desc') => void;
}

function CrmListSortMenu(props: CrmListSortMenuProps) {
    return (
        <Menu>
            <MenuItem
                text={props.labels.sortByDate + ' (' + props.labels.sortByDesc + ')'}
                onClick={() => props.onChange('created_at', 'desc')}
            />

            <MenuItem
                text={props.labels.sortByDate + ' (' + props.labels.sortByAsc + ')'}
                onClick={() => props.onChange('created_at', 'asc')}
            />

            <MenuItem
                text={props.labels.sortByFirstName + ' (' + props.labels.sortByDesc + ')'}
                onClick={() => props.onChange('profile_first_name', 'desc')}
            />

            <MenuItem
                text={props.labels.sortByFirstName + ' (' + props.labels.sortByAsc + ')'}
                onClick={() => props.onChange('profile_first_name', 'asc')}
            />

            <MenuItem
                text={props.labels.sortByLastName + ' (' + props.labels.sortByDesc + ')'}
                onClick={() => props.onChange('profile_last_name', 'desc')}
            />

            <MenuItem
                text={props.labels.sortByLastName + ' (' + props.labels.sortByAsc + ')'}
                onClick={() => props.onChange('profile_last_name', 'asc')}
            />

            <MenuItem
                text={props.labels.sortByEmail + ' (' + props.labels.sortByDesc + ')'}
                onClick={() => props.onChange('email', 'desc')}
            />

            <MenuItem
                text={props.labels.sortByEmail + ' (' + props.labels.sortByAsc + ')'}
                onClick={() => props.onChange('email', 'asc')}
            />
        </Menu>
    );
}
