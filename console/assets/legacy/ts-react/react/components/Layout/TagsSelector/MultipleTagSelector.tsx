import React, { useEffect, useState } from 'react';
import { InputGroup } from '@blueprintjs/core';
import { Tag, TagsRegistry } from './Model/tag';
import { MultipleTagsSelect } from './Selector/MultipleTagsSelect';

export interface MultipleTagsSelectorProps {
    tagsRegistry: TagsRegistry;
    defaultValue?: number[];
    placeholder: string;
    noResultsLabel: string;
    onChange?: (tags: Tag[]) => void;
}

export function MultipleTagsSelector(props: MultipleTagsSelectorProps) {
    const [selectedTagsIds, setSelectedTagsIds] = useState<number[]>([]);
    const [tagsRegistryLoaded, setTagsRegistryLoaded] = useState<boolean>(false);

    const handleChange = (tagsIds: number[]) => {
        // Remove selected tags that do not exist in the database anymore (can happen when results
        // do not come from the database, like from search index).
        const selectedTags = tagsIds.map((id) => props.tagsRegistry.getTag(id)).filter((t) => t !== null);

        // Update selected tags
        setSelectedTagsIds(selectedTags.map((t) => t.id));

        // Trigger change
        props.onChange && props.onChange(selectedTags);
    };

    // Fetch available tags on mount
    useEffect(() => {
        props.tagsRegistry.loadTags().then(() => {
            setTagsRegistryLoaded(true);

            // If default tags are provided, set them as selected
            if (props.defaultValue) {
                handleChange(props.defaultValue);
            }
        });
    }, []);

    if (!tagsRegistryLoaded) {
        return (
            <InputGroup
                leftIcon={<i className="far fa-circle-notch fa-spin" />}
                placeholder={props.placeholder}
                disabled={true}
            />
        );
    }

    const getSelectedTagIndex = (tag: Tag) => selectedTagsIds.indexOf(tag.id);
    const isTagSelected = (tag: Tag) => getSelectedTagIndex(tag) > -1;

    const selectTags = (tag: Tag) => {
        if (!isTagSelected(tag)) {
            handleChange(selectedTagsIds.slice().concat([tag.id]));
        }
    };

    const deselectTag = (tag: Tag) => {
        handleChange(selectedTagsIds.filter((id) => id !== tag.id));
    };

    return (
        <MultipleTagsSelect
            choices={props.tagsRegistry.getTags()}
            selected={selectedTagsIds.map((id) => props.tagsRegistry.getTag(id))}
            onTagSelected={selectTags}
            onTagDeselected={deselectTag}
            placeholder={props.placeholder}
            noResultsLabel={props.noResultsLabel}
            icon={<i className="far fa-tag" />}
        />
    );
}
