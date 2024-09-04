import React from 'react';
import { ItemRenderer, MultiSelect } from '@blueprintjs/select';
import { MenuItem } from '@blueprintjs/core';
import Fuse from 'fuse.js';

interface Author {
    id: string;
    fullName: string;
}

interface Props {
    choices: Author[];
    selectedIds: string[];
    onChange: (ids: string[]) => void;
}

function fuzzySearchAuthors(availableAuthors: Author[], search: string): Author[] {
    const maxResults = 5;

    if (!search) {
        return availableAuthors.slice(0, maxResults);
    }

    const fuse = new Fuse(availableAuthors, {
        includeScore: true,
        keys: ['fullName'],
    });

    return fuse
        .search(search)
        .slice(0, maxResults)
        .map((result) => result.item);
}

const InternalTagSelect = MultiSelect.ofType<Author>();

export function AuthorsSelector(props: Props) {
    const [selectedIds, setSelectedIds] = React.useState<string[]>(props.selectedIds);

    const handleChange = (ids: string[]) => {
        setSelectedIds(ids);
        props.onChange(ids);
    };

    const renderItem: ItemRenderer<Author> = (author, { modifiers, handleClick }) => {
        if (!modifiers.matchesPredicate) {
            return null;
        }

        const selected = selectedIds.filter((id) => id === author.id).length > 0;

        return (
            <MenuItem
                active={false}
                selected={selected}
                icon={selected ? <i className="fal fa-check" /> : <i className="fal" />}
                key={author.id}
                text={author.fullName}
                onClick={handleClick}
                shouldDismissPopover={false}
            />
        );
    };

    return (
        <InternalTagSelect
            items={props.choices}
            selectedItems={props.choices.filter((author) => selectedIds.includes(author.id))}
            onItemSelect={(author: Author) => {
                if (selectedIds.includes(author.id)) {
                    handleChange(selectedIds.filter((id) => id !== author.id));
                } else {
                    handleChange(selectedIds.slice().concat([author.id]));
                }
            }}
            itemRenderer={renderItem}
            tagRenderer={(author) => author.fullName}
            noResults={<MenuItem disabled={true} text="Aucun résultat" />}
            resetOnSelect={true}
            itemListPredicate={(query: string, authors: Author[]) => fuzzySearchAuthors(authors, query)}
            itemsEqual={(author1: Author, author2: Author) => author1.id === author2.id}
            fill={true}
            popoverProps={{
                minimal: true,
                usePortal: false,
            }}
            tagInputProps={{
                leftIcon: <i className="far fa-user" />,
                placeholder: 'Rechercher un(e) auteur(e)',
                onRemove: (_, index) => handleChange(selectedIds.filter((id) => id !== selectedIds[index])),
            }}
        />
    );
}
