import React, { ReactElement } from 'react';

export type FormErrors = null | { [property: string]: string[] }[];

export function getFieldErrors(errors: FormErrors, fieldName: string): string[] {
    if (!errors || typeof errors[fieldName] === 'undefined') {
        return [];
    }

    return errors[fieldName];
}

export function hasFieldErrors(errors: FormErrors, fieldName: string): boolean {
    return getFieldErrors(errors, fieldName).length > 0;
}

export function renderFieldErrors(errors: FormErrors, fieldName: string): ReactElement {
    if (!hasFieldErrors(errors, fieldName)) {
        return <></>;
    }

    return (
        <div className="bp4-form-errors">
            <i className="far fa-exclamation-circle" />
            <ul>
                {getFieldErrors(errors, fieldName).map((error) => (
                    <li key={error}>{error}</li>
                ))}
            </ul>
        </div>
    );
}
