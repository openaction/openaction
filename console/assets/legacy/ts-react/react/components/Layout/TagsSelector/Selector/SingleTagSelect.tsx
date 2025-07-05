import React from 'react';
import { Button, MenuItem } from '@blueprintjs/core';
import { ItemRenderer, Select } from '@blueprintjs/select';
import { IconName } from '@blueprintjs/icons';
import { MaybeElement } from '@blueprintjs/core/lib/esm/common/props';
import { fuzzySearchTag, Tag } from '../Model/tag';

const InternalTagSelect = Select.ofType<Tag>();

export interface SingleTagSelectProps {
    choices: Tag[] | null;
    selected: Tag | null;
    onChange: (value: Tag | null) => void;
    placeholder: string;
    noResultsLabel: string;
    icon: IconName | MaybeElement;
}

export function SingleTagSelect(props: SingleTagSelectProps) {
    const renderItem: ItemRenderer<Tag> = (tag, { modifiers, handleClick }) => {
        if (!modifiers.matchesPredicate) {
            return null;
        }

        const selected = props.selected && props.selected.id === tag.id;

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
            itemRenderer={renderItem}
            noResults={<MenuItem disabled={true} text={props.noResultsLabel} />}
            onItemSelect={(tag) => props.onChange(tag)}
            itemListPredicate={(query: string, tags: Tag[]) => fuzzySearchTag(tags, query)}
            itemsEqual={(tag1: Tag, tag2: Tag) => tag1.id === tag2.id}
            popoverProps={{
                minimal: true,
                usePortal: false,
            }}
        >
            <Button
                icon={props.icon}
                rightIcon="caret-down"
                text={props.selected ? props.selected.name : props.placeholder}
                intent={'slate' as any}
                outlined={true}
            />
        </InternalTagSelect>
    );
}
