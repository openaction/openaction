import React from 'react';
import { Button } from '@blueprintjs/core';
import { ButtonProps } from '@blueprintjs/core/src/components/button/abstractButton';

export function SlateButton(props: ButtonProps) {
    return (
        <Button outlined={true} intent={'slate' as any} icon={props.icon || null} {...props}>
            {props.children || ''}
        </Button>
    );
}
