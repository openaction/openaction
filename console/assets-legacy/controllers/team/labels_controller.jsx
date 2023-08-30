import { Controller } from 'stimulus';
import React, { useState } from 'react';
import { render } from 'react-dom';
import ReactTags from 'react-tag-autocomplete';

function LabelsInput(props) {
    const [state, setState] = useState({ labels: props.labels });

    const handleDelete = (i) => {
        const nextLabels = state.labels.slice(0);
        nextLabels.splice(i, 1);
        setState({ labels: nextLabels });
    };

    const handleAddition = (label) => {
        if (label.name) {
            const nextLabels = state.labels;
            nextLabels.push(label);
            setState({ labels: nextLabels });
        }
    };

    return (
        <>
            <ReactTags
                tags={state.labels}
                handleDelete={handleDelete}
                handleAddition={handleAddition}
                autoresize={false}
                autofocus={false}
                minQueryLength={0}
                allowNew={true}
            />

            <input type="hidden" name={props.name} value={state.labels.map((label) => label.name).join('|')} />
        </>
    );
}

export default class extends Controller {
    static targets = ['input'];

    connect() {
        const labels = this.inputTarget.value.split('|');

        let tags = [];
        for (let i in labels) {
            if (labels[i]) {
                tags.push({ name: labels[i] });
            }
        }

        render(<LabelsInput labels={tags} name={this.inputTarget.getAttribute('name')} />, this.element);
    }
}
