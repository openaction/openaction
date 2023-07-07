import React, { useMemo } from 'react';
import { CrmSearchFieldLabels } from '../../../components/Crm/Results/CrmSearchField';
import { CrmListLabels } from '../../../components/Crm/Results/CrmList';
import { CrmFacetsLabels, CrmFacetsRegistries } from '../../../components/Crm/Results/CrmFacetsList';
import { CrmItemLabels, CrmItemLinks } from '../../../components/Crm/Results/CrmItem';
import { CrmItemProfileLabels } from '../../../components/Crm/Results/CrmItemProfile';
import { SearchEngine } from '../../../components/Crm/Model/crm';
import { Tag, TagsRegistry } from '../../../components/Layout/TagsSelector/Model/tag';
import { CrmItemTagsLabels } from '../../../components/Crm/Results/CrmItemTags';
import { request } from '../../../../utils/http';
import { createLink } from '../../../../utils/createLink';
import { CrmSearchEngine } from '../../../components/Crm/CrmSearchEngine';
import { CrmActionsBatchEndpoints, CrmActionsLabels } from '../../../components/Crm/Results/CrmActions';

interface Props {
    search: {
        endpoint: string;
        index: string;
        token: string;
    };
    batch: CrmActionsBatchEndpoints;
    tags: {
        endpoint: string;
        updateEndpoint: string;
        defaultFilter: string | null;
    };
    tagsNamesRegistry: string[];
    facetsRegistries: CrmFacetsRegistries;
    links: CrmItemLinks;
    searchFieldLabels: CrmSearchFieldLabels;
    listLabels: CrmListLabels;
    facetsLabels: CrmFacetsLabels;
    actionsLabels: CrmActionsLabels;
    itemLabels: CrmItemLabels;
    tagsLabels: CrmItemTagsLabels;
    profileLabels: CrmItemProfileLabels;
}

export default function (props: Props) {
    const searchEngine = useMemo(
        () => new SearchEngine(props.search.endpoint, props.search.token, props.search.index),
        [props.search.endpoint, props.search.token, props.search.index]
    );

    if (props.tags.defaultFilter) {
        searchEngine.includeFilter('tags_names', props.tags.defaultFilter);
    }

    const tagsRegistry = useMemo<TagsRegistry>(() => new TagsRegistry(props.tags.endpoint), [props.tags.endpoint]);

    return (
        <CrmSearchEngine
            searchEngine={searchEngine}
            isProjectView={false}
            isReadOnly={false}
            tagsRegistry={tagsRegistry}
            updateTagsCallback={(contactUuid: string, tags: Tag[]) => {
                request('POST', createLink(props.tags.updateEndpoint, { '-uuid-': contactUuid }), { data: tags });
            }}
            {...props}
        />
    );
}
