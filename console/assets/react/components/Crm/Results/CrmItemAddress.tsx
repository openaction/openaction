import React from 'react';
import { CrmDocument } from '../Model/crm';
import { countryName } from '../../../../utils/formatter';

export function CrmItemAddress(props: { item: CrmDocument }) {
    return (
        <>
            <div>{props.item.address_street_line1}</div>
            <div>{props.item.address_street_line2}</div>
            <div>
                {props.item.address_zip_code} {props.item.address_city}
            </div>
            <div>{props.item.address_country ? countryName(props.item.address_country) : ''}</div>
        </>
    );
}
