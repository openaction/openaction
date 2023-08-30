import React, { useState, useEffect } from 'react';
import ReactTags from 'react-tag-autocomplete';
import { exposedDataReader } from '../services/exposed-data-reader';
import { tagsApiClient } from '../services/tags-api-client';

export function TagSelector(props) {
    const [loading, setLoading] = useState(true);
    const [suggestions, setSuggestions] = useState([]);

    // Load suggestions on mount
    useEffect(() => {
        tagsApiClient.fetchTags(exposedDataReader.read('tags_url'), (tags) => {
            const suggestions = [];
            for (let id in tags) {
                if (tags.hasOwnProperty(id)) {
                    suggestions.push({ id: id, name: tags[id].name });
                }
            }

            setLoading(false);
            setSuggestions(suggestions);
        });
    }, []);

    const handleDelete = (i) => {
        const nextTags = props.tags.slice(0);
        nextTags.splice(i, 1);
        props.onChange(nextTags);
    };

    const handleAddition = (tag) => {
        const nextTags = props.tags;
        nextTags.push(tag);
        props.onChange(nextTags);
    };

    if (loading) {
        return (
            <div className="text-center">
                <i className="fal fa-circle-notch fa-spin"></i>
            </div>
        );
    }

    const selectedTagsNames = {};
    for (let i in props.tags) {
        selectedTagsNames[props.tags[i].name] = true;
    }

    const displayedSuggestions = [];
    for (let i in suggestions) {
        if (typeof selectedTagsNames[suggestions[i].name] === 'undefined') {
            displayedSuggestions.push(suggestions[i]);
        }
    }

    return (
        <ReactTags
            tags={props.tags}
            suggestions={displayedSuggestions}
            handleDelete={handleDelete}
            handleAddition={handleAddition}
            autoresize={false}
            autofocus={false}
            minQueryLength={0}
            allowNew={props.allowNew || false}
        />
    );
}
