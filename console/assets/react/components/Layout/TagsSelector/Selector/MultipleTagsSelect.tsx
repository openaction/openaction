import React from 'react';
import { MenuItem } from '@blueprintjs/core';
import { ItemRenderer, MultiSelect } from '@blueprintjs/select';
import { IconName } from '@blueprintjs/icons';
import { MaybeElement } from '@blueprintjs/core/lib/esm/common/props';
import { fuzzySearchTag, Tag } from '../Model/tag';

const InternalTagSelect = MultiSelect.ofType<Tag>();

export interface MultipleTagsSelectProps {
    choices: Tag[];
    selected: Tag[];
    onTagSelected: (tag: Tag) => void;
    onTagDeselected: (tag: Tag) => void;
    placeholder: string;
    noResultsLabel: string;
    icon: IconName | MaybeElement;
}

export function MultipleTagsSelect(props: MultipleTagsSelectProps) {
    const renderItem: ItemRenderer<Tag> = (tag, { modifiers, handleClick }) => {
        if (!modifiers.matchesPredicate) {
            return null;
        }

        const selected = props.selected.filter((t) => t.id === tag.id).length > 0;

        return (
            <MenuItem
                active={false}
                selected={selected}
                icon={selected ? <i className="fal fa-check" /> : <i className="fal" />}
                key={tag.id}
                text={tag.name}
                onClick={handleClick}
                shouldDismissPopover={false}
            />
        );
    };

    return (
        <InternalTagSelect
            items={props.choices}
            selectedItems={props.selected.sort((a, b) => a.slug.localeCompare(b.slug))}
            onItemSelect={(tag: Tag) => {
                if (props.selected.filter((t) => t.id === tag.id).length > 0) {
                    props.onTagDeselected(tag);
                } else {
                    props.onTagSelected(tag);
                }
            }}
            itemRenderer={renderItem}
            tagRenderer={(tag) => tag.name}
            noResults={<MenuItem disabled={true} text={props.noResultsLabel} />}
            resetOnSelect={true}
            itemListPredicate={(query: string, tags: Tag[]) => fuzzySearchTag(tags, query)}
            itemsEqual={(tag1: Tag, tag2: Tag) => tag1.id === tag2.id}
            fill={true}
            popoverProps={{
                minimal: true,
                usePortal: false,
            }}
            tagInputProps={{
                leftIcon: props.icon,
                placeholder: props.placeholder,
                onRemove: (_, index) => props.onTagDeselected(props.selected[index]),
            }}
        />
    );
}
