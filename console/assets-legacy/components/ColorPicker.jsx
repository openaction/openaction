import React, { useState } from 'react';
import { TwitterPicker } from 'react-color';

export function ColorChooser(props) {
    const [color, setColor] = useState(props.color);

    let choices = [
        '#d7201c',
        '#e7692b',
        '#ff404c',
        '#ec008c',
        '#a000d0',
        '#3145cd',
        '#0373bf',
        '#0693e3',
        '#117b8b',
        '#31a38f',
        '#52a09a',
        '#409b1a',
        '#128b4d',
        '#000000',
    ];

    if (props.choices) {
        choices = props.choices.split(',').map((color) => '#' + color);
    }

    return (
        <div>
            <input type="hidden" name={props.inputName} value={color} />

            <div
                style={{
                    width: 25,
                    height: 25,
                    borderRadius: 13,
                    backgroundColor: color ? '#' + color : '#fff',
                    marginBottom: 15,
                    marginLeft: 8,
                }}
            ></div>

            <TwitterPicker
                color={color ? '#' + color : null}
                onChangeComplete={(color) => setColor(color.hex.replace('#', ''))}
                colors={choices}
            />
        </div>
    );
}
