import React, { useState } from 'react';
import { translator } from '../../../../../services/translator';
import { SortableContainer, SortableElement, SortableHandle } from 'react-sortable-hoc';
import { arrayMoveImmutable } from 'array-move';

function parseChoices(choices) {
    let sortableChoices = [];
    for (let i in choices) {
        sortableChoices.push({ value: choices[i], id: choices[i] });
    }

    return sortableChoices;
}

function encodeChoices(sortableChoices) {
    let persistedChoices = [];
    for (let i in sortableChoices) {
        persistedChoices.push(sortableChoices[i].value);
    }

    return persistedChoices;
}

const DragHandle = SortableHandle(() => (
    <div className="col-auto form-choice-grip">
        <i className="fad fa-sort" />
    </div>
));

const SortableItem = SortableElement(({ value, choices, updateChoice, deleteChoice }) => (
    <div className="row align-items-center mb-1">
        <DragHandle />
        <div className="col">
            <input
                type="text"
                className="form-control form-control-sm form-field"
                value={value.value}
                onChange={(event) => updateChoice(choices.indexOf(value), event.target.value)}
            />
        </div>
        <div className="col-auto">
            <button
                type="button"
                className="btn btn-link text-danger"
                onClick={() => deleteChoice(choices.indexOf(value))}
            >
                <i className="far fa-times" />
            </button>
        </div>
    </div>
));

const SortableList = SortableContainer(({ items, choices, updateChoice, deleteChoice }) => {
    return (
        <div>
            {items.map((choice, index) => (
                <SortableItem
                    key={`item-${choice.id}`}
                    index={index}
                    value={choice}
                    choices={choices}
                    updateChoice={updateChoice}
                    deleteChoice={deleteChoice}
                />
            ))}
        </div>
    );
});

export function ChoiceEditor(props) {
    const [choices, setChoices] = useState(parseChoices(props.choices));

    // State helpers
    const persistChoices = (sortableChoices) => {
        props.onChange(encodeChoices(sortableChoices));
    };

    const addChoice = () => {
        const newChoices = [...choices, { value: '', id: 'created-' + Math.floor(Math.random() * 9999999) }];

        setChoices(newChoices);
        persistChoices(newChoices);
    };

    const updateChoice = (key, value) => {
        let newChoices = [];
        for (let i in choices) {
            if (choices.hasOwnProperty(i)) {
                if (i + '' !== key + '') {
                    newChoices.push(choices[i]);
                } else {
                    newChoices.push({ value: value, id: choices[i].id });
                }
            }
        }

        setChoices(newChoices);
        persistChoices(newChoices);
    };

    const deleteChoice = (key) => {
        let newChoices = [];
        for (let i in choices) {
            if (choices.hasOwnProperty(i) && i + '' !== key + '') {
                newChoices.push(choices[i]);
            }
        }

        setChoices(newChoices);
        persistChoices(newChoices);
    };

    return (
        <div>
            <SortableList
                items={choices}
                choices={choices}
                updateChoice={updateChoice}
                deleteChoice={deleteChoice}
                useDragHandle={true}
                onSortEnd={({ oldIndex, newIndex }) => {
                    const newChoices = arrayMoveImmutable(choices, oldIndex, newIndex);
                    setChoices(newChoices);
                    persistChoices(newChoices);
                }}
            />

            <div className="mt-3">
                <button type="button" className="btn btn-sm btn-outline-primary border-0" onClick={addChoice}>
                    <i className="far fa-plus mr-1" />
                    {translator.trans('form.add_choice')}
                </button>
            </div>
        </div>
    );
}
