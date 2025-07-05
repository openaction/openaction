import React from 'react';
import redaxios from 'redaxios';
import { fireEvent, render, screen } from '@testing-library/react';
import '@testing-library/jest-dom';
import ContactsList from '../../../../../react/controllers/Project/Community/ContactsList';
import { createMeiliSearchIndex } from '../../../../../react/bridge/meilisearch';
import { mockHistoryHtmlResponse, mockMeiliSearch } from '../../../../fixtures/crm';

jest.mock('../../../../../react/bridge/meilisearch');
(createMeiliSearchIndex as any).mockImplementation(() => mockMeiliSearch());

jest.mock('redaxios');
(redaxios.request as any).mockImplementation(() => mockHistoryHtmlResponse());

describe('ContactsList', () => {
    it('render', async () => {
        render(<div role="container">{createControllerComponent()}</div>);

        const container = await screen.findByRole('container');
        expect(container).toBeInTheDocument();

        // Should display all contacts
        expect(await screen.findByText('troycovillon@teleworm.us')).toBeInTheDocument();
        expect(await screen.findByText('jeanpaul@gmail.com')).toBeInTheDocument();
        expect(await screen.findByText('jean.marting@gmail.com')).toBeInTheDocument();
        expect(await screen.findByText('michael.mousseau@exampleco.com')).toBeInTheDocument();
    });

    it('open item details', async () => {
        render(<div role="container">{createControllerComponent()}</div>);

        const container = await screen.findByRole('container');
        expect(container).toBeInTheDocument();

        // Wait for contacts loading
        expect(await screen.findByText('troycovillon@teleworm.us')).toBeInTheDocument();

        // Open first item
        const contact = container.querySelectorAll('.crm-search-results-item')[0];
        fireEvent.click(contact.querySelector('.crm-search-results-item-details'));

        // Profile content
        expect(await screen.findByText('Email campaigns')).toBeInTheDocument();
        expect(await screen.findByText('Text campaigns')).toBeInTheDocument();
        expect(await screen.findByText('Call campaigns')).toBeInTheDocument();

        // History content
        expect(await screen.findByText('Mock history HTML')).toBeInTheDocument();
    });

    it('filter facet', async () => {
        render(<div role="container">{createControllerComponent()}</div>);

        const container = await screen.findByRole('container');
        expect(container).toBeInTheDocument();

        // Wait for contacts loading
        expect(await screen.findByText('troycovillon@teleworm.us')).toBeInTheDocument();

        // Filter by facet
        fireEvent.click(await screen.findByText('Île-de-France'));
        expect(await screen.findByRole('crm-ready')).toBeInTheDocument();
    });
});

function createControllerComponent() {
    return (
        <ContactsList
            isReadOnly={false}
            search={{
                endpoint: 'http://localhost/search',
                index: 'index',
                token: 'token',
                project: '',
            }}
            batch={{
                addTag: 'http://localhost/tags',
                removeTag: 'http://localhost/tags',
            }}
            tags={{
                endpoint: 'http://localhost/tags',
                updateEndpoint: 'http://localhost/update-tags',
            }}
            tagsNamesRegistry={[]}
            links={{
                view: 'http://localhost/view',
                edit: 'http://localhost/edit',
                history: 'http://localhost/history',
            }}
            facetsRegistries={{
                projectsNames: {},
            }}
            searchFieldLabels={{
                placeholder: 'Search...',
                modeSimple: 'Switch to simple mode',
                modeAdvanced: 'Switch to advanced mode',
                enterQuery: 'Enter a query...',
                runQuery: 'Run',
            }}
            listLabels={{
                location: 'Location',
                createdAt: 'Joined at',
                status: 'Status',
                noResultsTitle: 'No search results',
                noResultsDescription:
                    "Your search didn't match any contact. Try searching for something else, or create a new contact.",
                loadMore: 'Load more',
                sortBy: 'Sort by',
                sortByAsc: 'ascending',
                sortByDesc: 'descending',
                sortByDate: 'Date',
                sortByFirstName: 'First name',
                sortByLastName: 'Last name',
                sortByEmail: 'Email',
            }}
            facetsLabels={{
                tags_names: 'Tags',
                projects: 'Projects',
                status: 'Status',
                area_country_code: 'Country',
                area_province_name: 'Region / State',
                area_district_name: 'Departement / District',
                profile_company: 'Company',
                includeFilter: 'Add this filter',
                excludeFilter: 'Exclude this filter',
                cancelFilter: 'Cancel this filter',
                valueSearch: 'Search...',
            }}
            actionsLabels={{
                nbHits: 'contacts',
                clear: 'Clear',
                applyLabel: 'For these contacts:',
                export: 'Export',
                exportQuestion: 'Export',
                exportConfirm: 'Export',
                exportCancel: 'Export',
                exportStarting: 'Export',
                exportInProgress: 'Export',
                exportSuccess: 'Export',
                exportDownload: 'Export',
                addTag: 'Add tag',
                addTagQuestion: 'Add tag',
                addTagLabel: 'Add tag',
                addTagNoResults: 'Add tag',
                addTagConfirm: 'Add tag',
                addTagCancel: 'Add tag',
                addTagStarting: 'Add tag',
                addTagInProgress: 'Add tag',
                addTagSuccess: 'Add tag',
                removeTag: 'Remove tag',
                removeTagQuestion: 'Remove tag',
                removeTagLabel: 'Remove tag',
                removeTagNoResults: 'Remove tag',
                removeTagConfirm: 'Remove tag',
                removeTagCancel: 'Remove tag',
                removeTagStarting: 'Remove tag',
                removeTagInProgress: 'Remove tag',
                removeTagSuccess: 'Remove tag',
                remove: 'Remove tag',
                removeQuestion: 'Remove tag',
                removeConfirm: 'Remove tag',
                removeCancel: 'Remove tag',
                removeStarting: 'Remove tag',
                removeInProgress: 'Remove tag',
                removeSuccess: 'Remove tag',
            }}
            itemLabels={{
                age: 'yo',
                status: {
                    c: 'Contact',
                    m: 'Member',
                    u: 'Unsubscribed',
                },
                actions: {
                    view: 'View profile',
                    edit: 'Edit contact',
                },
            }}
            tagsLabels={{
                noTags: 'No tags',
                placeholder: 'Search a tag...',
                noTagsFound: 'No tags found',
            }}
            profileLabels={{
                historyTitle: 'Interactions history',
                historyDescription: 'Interactions your organisation had with this contact.',
                newsletter: 'Email campaigns',
                sms: 'Text campaigns',
                calls: 'Call campaigns',
                birthdate: 'Birthdate',
                address: 'Postal address',
                phone: 'Phone number (personal)',
                work: 'Employment',
                socials: 'Social networks',
                projects: 'Projects',
            }}
        />
    );
}
