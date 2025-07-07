import React, { useState, useEffect } from 'react';
import { translator } from '../../../services/translator';
import { httpClient } from '../../../services/http-client';

export function DefaultImportArea(props) {
    // State
    let levels = {};
    for (let i = 0; i <= 4; i++) {
        const [loading, setLoading] = useState(false);
        const [suggestions, setSuggestions] = useState({});
        const [selection, setSelection] = useState(null);

        levels[i] = {
            loading: loading,
            setLoading: setLoading,
            suggestions: suggestions,
            setSuggestions: setSuggestions,
            selection: selection,
            setSelection: setSelection,
            update: (selection) => {
                setSelection(selection);

                if (selection) {
                    // If there is a new selection, query for next level suggestions
                    if (i < 4) {
                        levels[i + 1].setLoading(true);

                        httpClient.get('/console/api/areas/search?p=' + selection.id).then((res) => {
                            levels[i + 1].setLoading(false);
                            levels[i + 1].setSuggestions(res.data);
                        });
                    }
                }

                // The new selection changed, clear all the next levels
                for (let j = i + 1; j <= 4; j++) {
                    levels[j].setSelection(null);
                    levels[j].setSuggestions({});
                }
            },
        };
    }

    // Update suggestions when needed (countries on mount, others on update)
    useEffect(() => {
        levels[0].setLoading(true);

        httpClient.get('/console/api/areas/search').then((res) => {
            levels[0].setLoading(false);
            levels[0].setSuggestions(res.data);
        });
    }, []);

    // Loader while finding countries
    if (!Object.keys(levels[0].suggestions).length) {
        return (
            <div className="h5 my-4">
                <i className="fal fa-circle-notch fa-spin"></i>
            </div>
        );
    }

    let view = [];

    // Levels
    for (let i = 0; i <= 4; i++) {
        view.push(
            <div key={'select-row-' + i} className="mt-2">
                <div className="row no-gutters align-items-center">
                    <div className={'text-center ' + (i === 0 ? 'd-none' : 'col-1')}>
                        <i className="fas fa-filter"></i>
                    </div>
                    <div className={i === 0 ? 'col-12' : 'col-11'}>
                        {i === 0 ? (
                            <label htmlFor={'select-level-' + i}>{translator.trans('area_selector.add.country')}</label>
                        ) : (
                            ''
                        )}

                        <select
                            className="form-control"
                            id={'select-level-' + i}
                            disabled={levels[i].loading}
                            value={levels[i].selection ? levels[i].selection.id : ''}
                            onChange={(e) => {
                                const id = e.target.value ? e.target.value : null;

                                if (!id) {
                                    levels[i].update(null);
                                } else {
                                    levels[i].update({ id: id, name: levels[i].suggestions[id].name });
                                }
                            }}
                        >
                            <option value=""></option>
                            {Object.keys(levels[i].suggestions).map((id) => (
                                <option value={id} key={id}>
                                    {levels[i].suggestions[id].name}
                                    {levels[i].suggestions[id].desc ? ' (' + levels[i].suggestions[id].desc + ')' : ''}
                                </option>
                            ))}
                        </select>
                    </div>
                </div>
            </div>
        );

        if (!levels[i].selection) {
            break;
        }
    }

    let smallestArea = null;
    for (let i = 0; i <= 4; i++) {
        if (levels[i].selection) {
            const parent = smallestArea;

            smallestArea = levels[i].selection;
            smallestArea.parent = parent;
        }
    }

    return (
        <div>
            {view}

            <input type="hidden" name={props.inputName} value={smallestArea ? smallestArea.id : ''} />
        </div>
    );
}
