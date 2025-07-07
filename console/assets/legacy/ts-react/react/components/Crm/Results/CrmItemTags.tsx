import React from 'react';
import { CrmDocument } from '../Model/crm';
import { Popover2 } from '@blueprintjs/popover2';
import { Button } from '@blueprintjs/core';
import { MultipleTagsSelector } from '../../Layout/TagsSelector/MultipleTagSelector';
import { Tag, TagsRegistry } from '../../Layout/TagsSelector/Model/tag';

export interface CrmItemTagsLabels {
    noTags: string;
    placeholder: string;
    noTagsFound: string;
}

interface Props {
    isReadOnly: boolean;
    result: CrmDocument;
    tags: Tag[] | null;
    onTagsChange: (tags: Tag[]) => void;
    tagsRegistry: TagsRegistry;
    tagsNamesRegistry: string[];
    labels: CrmItemTagsLabels;
}

export function CrmItemTags(props: Props) {
    // Filter displayed names to hide potentially removed tags (names may not directly come from database)
    const tagsNames = props.result.tags_names
        .filter((name) => props.tagsNamesRegistry.indexOf(name) > -1)
        .sort((a, b) => a.localeCompare(b));

    return (
        <div className="crm-search-results-item-tags">
            {!props.isReadOnly ? (
                <Popover2
                    interactionKind="click"
                    placement="bottom-start"
                    content={
                        <div className="crm-search-results-item-tags-edit">
                            <MultipleTagsSelector
                                tagsRegistry={props.tagsRegistry}
                                defaultValue={props.tags ? props.tags.map((t) => t.id) : props.result.tags}
                                onChange={props.onTagsChange}
                                placeholder={props.labels.placeholder}
                                noResultsLabel={props.labels.noTagsFound}
                            />
                        </div>
                    }
                >
                    <Button outlined={true} small={true} className="crm-search-results-item-tags-more">
                        <i className="far fa-edit" />
                    </Button>
                </Popover2>
            ) : (
                ''
            )}

            <CrmItemTagsList
                initialTagsNames={tagsNames}
                tags={props.tags !== null ? props.tags.sort((a, b) => a.slug.localeCompare(b.slug)) : null}
                labels={props.labels}
            />
        </div>
    );
}

interface CrmItemTagsListProps {
    initialTagsNames: string[];
    tags: Tag[] | null;
    labels: CrmItemTagsLabels;
}

function CrmItemTagsList(props: CrmItemTagsListProps) {
    // Tags not loaded from registry: use CRM document names
    if (null === props.tags) {
        if (0 === props.initialTagsNames.length) {
            return <div className="crm-search-results-item-tags-none">{props.labels.noTags}</div>;
        }

        return (
            <>
                {props.initialTagsNames.map((name: string) => (
                    <div className="crm-search-results-item-tag" key={name}>
                        {name}
                    </div>
                ))}
            </>
        );
    }

    // Tags loaded from registry: use them
    if (0 === props.tags.length) {
        return <div className="crm-search-results-item-tags-none">{props.labels.noTags}</div>;
    }

    return (
        <>
            {props.tags.map((tag: Tag) => (
                <div className="crm-search-results-item-tag" key={tag.id}>
                    {tag.name}
                </div>
            ))}
        </>
    );
}
