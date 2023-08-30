import React from 'react';
import { AgGridColumn, AgGridReact } from 'ag-grid-react';
import { translator } from '../../../services/translator';
import { httpClient } from '../../../services/http-client';

import { contactRenderer } from './renderers/contactRenderer';
import { textRenderer } from './renderers/textRenderer';
import { dateRenderer } from './renderers/dateRenderer';
import { createMainTagRenderer, createReadOnlyMainTagRenderer } from './renderers/mainTagRenderer';

export function CommunityList(props) {
    const dataSource = {
        getRows: (params) => {
            httpClient
                .post(props.endpoint, {
                    start: params.startRow,
                    end: params.endRow,
                    filter: params.filterModel,
                    sort: params.sortModel,
                })
                .then((rowData) => {
                    params.successCallback(rowData.data.contacts, rowData.data.total);
                });
        },
    };

    const totalMainTags = Object.keys(props.mainTags).length;

    let maxTagsWidth = Math.floor(472 / totalMainTags);
    if (maxTagsWidth > 90) {
        maxTagsWidth = 90;
    }

    return (
        <div className="ag-theme-alpine" style={{ height: 1885, width: '100%' }}>
            <AgGridReact
                localeText={translator.trans('aggrid')}
                gridOptions={{
                    rowHeight: 58,
                    headerHeight: 20,
                    floatingFiltersHeight: 20,
                    pagination: true,
                    paginationPageSize: 30,
                    rowModelType: 'infinite',
                    datasource: dataSource,
                    components: {
                        contactRenderer: contactRenderer,
                        textRenderer: textRenderer,
                        dateRenderer: dateRenderer,
                        mainTagRenderer: props.readOnly
                            ? createReadOnlyMainTagRenderer()
                            : createMainTagRenderer(props.syncTagsEndpoint, props.isProgress),
                    },
                }}
                defaultColDef={{
                    floatingFilter: true,
                    filterParams: {
                        buttons: ['reset', 'apply'],
                    },
                }}
            >
                <AgGridColumn
                    headerName={translator.trans('community.list.columns.contact')}
                    minWidth={370}
                    flex={1}
                    field="email"
                    sortable={true}
                    filter={true}
                    filterParams={{
                        filterOptions: [
                            'contains',
                            'notContains',
                            'equals',
                            'notEqual',
                            'startsWith',
                            'endsWith',
                            {
                                hideFilterInput: true,
                                displayKey: 'member',
                                displayName: translator.trans('community.list.filter.member'),
                                test: () => {},
                            },
                            {
                                hideFilterInput: true,
                                displayKey: 'contact',
                                displayName: translator.trans('community.list.filter.contact'),
                                test: () => {},
                            },
                        ],
                    }}
                    cellRenderer="contactRenderer"
                />

                <AgGridColumn
                    headerName={translator.trans('community.list.columns.firstName')}
                    width={100}
                    minWidth={100}
                    field="firstName"
                    sortable={true}
                    filter={true}
                    cellRenderer="textRenderer"
                />

                <AgGridColumn
                    headerName={translator.trans('community.list.columns.lastName')}
                    field="lastName"
                    width={100}
                    minWidth={100}
                    sortable={true}
                    filter={true}
                    cellRenderer="textRenderer"
                />

                <AgGridColumn
                    headerName={translator.trans('community.list.columns.createdAt')}
                    field="createdAt"
                    width={125}
                    minWidth={125}
                    sortable={true}
                    sort="desc"
                    cellRenderer="dateRenderer"
                    filter="agDateColumnFilter"
                />

                <AgGridColumn headerName={translator.trans('community.list.columns.tags')}>
                    {Object.keys(props.mainTags).map((key) => {
                        if (!props.mainTags[key]) {
                            return <React.Fragment key={key} />;
                        }

                        return (
                            <AgGridColumn
                                headerName={props.mainTags[key]}
                                key={key}
                                field={'tag' + key}
                                width={maxTagsWidth}
                                minWidth={maxTagsWidth}
                                cellRenderer="mainTagRenderer"
                                sortable={true}
                                filter={false}
                            />
                        );
                    })}
                </AgGridColumn>
            </AgGridReact>
        </div>
    );
}
