import React from 'react';
import { AgGridColumn, AgGridReact } from 'ag-grid-react';
import { translator } from '../../../services/translator';
import { httpClient } from '../../../services/http-client';

import { contactRenderer } from './renderers/contactRenderer';
import { textRenderer } from './renderers/textRenderer';
import { numberRenderer } from './renderers/numberRenderer';

export function ReportList(props) {
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
                        numberRenderer: numberRenderer,
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
                    headerName={translator.trans('emailing.report.columns.contact')}
                    minWidth={400}
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
                                displayName: translator.trans('emailing.report.filter.member'),
                                test: () => {},
                            },
                            {
                                hideFilterInput: true,
                                displayKey: 'contact',
                                displayName: translator.trans('emailing.report.filter.contact'),
                                test: () => {},
                            },
                        ],
                    }}
                    cellRenderer="contactRenderer"
                ></AgGridColumn>

                <AgGridColumn
                    headerName={translator.trans('emailing.report.columns.firstName')}
                    width={100}
                    minWidth={100}
                    field="firstName"
                    sortable={true}
                    filter={true}
                    cellRenderer="textRenderer"
                ></AgGridColumn>

                <AgGridColumn
                    headerName={translator.trans('emailing.report.columns.lastName')}
                    field="lastName"
                    width={100}
                    minWidth={100}
                    sortable={true}
                    filter={true}
                    cellRenderer="textRenderer"
                ></AgGridColumn>

                <AgGridColumn
                    headerName={translator.trans('emailing.report.columns.opens')}
                    field="opens"
                    width={100}
                    minWidth={100}
                    sortable={true}
                    cellRenderer="numberRenderer"
                ></AgGridColumn>

                <AgGridColumn
                    headerName={translator.trans('emailing.report.columns.clicks')}
                    field="clicks"
                    sort="desc"
                    width={100}
                    minWidth={100}
                    sortable={true}
                    cellRenderer="numberRenderer"
                ></AgGridColumn>
            </AgGridReact>
        </div>
    );
}
