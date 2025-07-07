import React, { useState } from 'react';
import { TagSelector } from '../../../components/TagSelector';

export function TagsInput(props) {
    const [state, setState] = useState({
        tags: props.tags ? JSON.parse(props.tags) : [],
    });

    return (
        <div>
            <TagSelector
                tags={state.tags}
                allowNew={props.allowAdd}
                onChange={(tags) => {
                    setState({ tags: tags });
                }}
            />

            <input type="hidden" name={props.name} value={JSON.stringify(state.tags)} />
        </div>
    );
}
