import React from 'react';
import { ItemRenderer, MultiSelect } from '@blueprintjs/select';
import { MenuItem } from '@blueprintjs/core';
import Fuse from 'fuse.js';

interface Participant {
    id: string;
    fullName: string;
}

interface Props {
    choices: Participant[];
    selectedIds: string[];
    onChange: (ids: string[]) => void;
}

function fuzzySearchParticipants(availableParticipants: Participant[], search: string): Participant[] {
    const maxResults = 5;

    if (!search) {
        return availableParticipants.slice(0, maxResults);
    }

    const fuse = new Fuse(availableParticipants, {
        includeScore: true,
        keys: ['fullName'],
    });

    return fuse
        .search(search)
        .slice(0, maxResults)
        .map((result) => result.item);
}

const InternalTagSelect = MultiSelect.ofType<Participant>();

export function ParticipantsSelector(props: Props) {
    const [selectedIds, setSelectedIds] = React.useState<string[]>(props.selectedIds);

    const handleChange = (ids: string[]) => {
        setSelectedIds(ids);
        props.onChange(ids);
    };

    const renderItem: ItemRenderer<Participant> = (participant, { modifiers, handleClick }) => {
        if (!modifiers.matchesPredicate) {
            return null;
        }

        const selected = selectedIds.filter((id) => id === participant.id).length > 0;

        return (
            <MenuItem
                active={false}
                selected={selected}
                icon={selected ? <i className="fal fa-check" /> : <i className="fal" />}
                key={participant.id}
                text={participant.fullName}
                onClick={handleClick}
                shouldDismissPopover={false}
            />
        );
    };

    return (
        <InternalTagSelect
            items={props.choices}
            selectedItems={props.choices.filter((participant) => selectedIds.includes(participant.id))}
            onItemSelect={(participant: Participant) => {
                if (selectedIds.includes(participant.id)) {
                    handleChange(selectedIds.filter((id) => id !== participant.id));
                } else {
                    handleChange(selectedIds.slice().concat([participant.id]));
                }
            }}
            itemRenderer={renderItem}
            tagRenderer={(participant) => participant.fullName}
            noResults={<MenuItem disabled={true} text="Aucun résultat" />}
            resetOnSelect={true}
            itemListPredicate={(query: string, participants: Participant[]) =>
                fuzzySearchParticipants(participants, query)
            }
            itemsEqual={(participant1: Participant, participant2: Participant) => participant1.id === participant2.id}
            fill={true}
            popoverProps={{
                minimal: true,
                usePortal: false,
            }}
            tagInputProps={{
                leftIcon: <i className="far fa-user" />,
                placeholder: 'Rechercher une personne',
                onRemove: (_, index) => handleChange(selectedIds.filter((id) => id !== selectedIds[index])),
            }}
        />
    );
}
