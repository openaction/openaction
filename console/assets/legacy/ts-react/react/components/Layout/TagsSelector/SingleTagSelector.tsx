import React, { useEffect, useState } from 'react';
import { Button } from '@blueprintjs/core';
import { Tag, TagsRegistry } from './Model/tag';
import { SingleTagSelect } from './Selector/SingleTagSelect';

export interface SingleTagSelectorProps {
    tagsRegistry: TagsRegistry;
    defaultValue?: number;
    defaultValueName?: string;
    placeholder: string;
    noResultsLabel: string;
    onChange?: (tag: Tag) => void;
}

export function SingleTagSelector(props: SingleTagSelectorProps) {
    const [selectedTagId, setSelectedTagId] = useState<number | null>(null);
    const [tagsRegistryLoaded, setTagsRegistryLoaded] = useState<boolean>(false);

    const handleChange = (tagId: number) => {
        setSelectedTagId(tagId);
        props.onChange && props.onChange(props.tagsRegistry.getTag(tagId));
    };

    // Fetch available tags on mount
    useEffect(() => {
        props.tagsRegistry.loadTags().then(() => {
            setTagsRegistryLoaded(true);

            // If a default tag is provided, set it as selected
            if (props.defaultValue) {
                handleChange(props.defaultValue);
            }
        });
    }, []);

    if (!tagsRegistryLoaded) {
        return (
            <Button
                icon={<i className="far fa-tag" />}
                rightIcon={<i className="far fa-circle-notch fa-spin" />}
                text={props.defaultValue ? props.defaultValueName || props.placeholder : props.placeholder}
                intent={'slate' as any}
                disabled={true}
                outlined={true}
            />
        );
    }

    return (
        <SingleTagSelect
            choices={props.tagsRegistry.getTags()}
            selected={selectedTagId ? props.tagsRegistry.getTag(selectedTagId) : null}
            onChange={(tag) => handleChange(tag.id)}
            placeholder={props.placeholder}
            noResultsLabel={props.noResultsLabel}
            icon={<i className="far fa-tag" />}
        />
    );
}
