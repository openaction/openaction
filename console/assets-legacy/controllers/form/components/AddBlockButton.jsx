import React, { useState } from 'react';
import { translator } from '../../../services/translator';

const typeGroups = [
    {
        key: 'custom_content',
        children: [
            { type: 'header', icon: 'fal fa-heading', label: 'Heading' },
            { type: 'paragraph', icon: 'fal fa-text', label: 'Paragraph' },
            { type: 'html', icon: 'fal fa-code', label: 'Custom HTML' },
        ],
    },
    {
        key: 'fields',
        children: [
            { type: 'text', icon: 'fal fa-text' },
            { type: 'textarea', icon: 'fal fa-text' },
            { type: 'radio', icon: 'fal fa-check-circle' },
            { type: 'checkbox', icon: 'fal fa-check-square' },
            { type: 'confirmation', icon: 'fal fa-file-signature' },
            { type: 'select', icon: 'fal fa-caret-circle-down' },
            { type: 'rating', icon: 'fal fa-star' },
            { type: 'money_amount', icon: 'fal fa-dollar-sign' },
            { type: 'date', icon: 'fal fa-calendar-alt' },
            { type: 'time', icon: 'fal fa-clock' },
            { type: 'file', icon: 'fal fa-file' },
        ],
    },
    {
        key: 'automatic_fields',
        children: [
            { type: 'email', icon: 'fal fa-at' },
            { type: 'formal_title', icon: 'fal fa-address-card' },
            { type: 'firstname', icon: 'fal fa-address-card' },
            { type: 'middlename', icon: 'fal fa-address-card' },
            { type: 'lastname', icon: 'fal fa-address-card' },
            { type: 'birthdate', icon: 'fal fa-calendar-alt' },
            { type: 'gender', icon: 'fal fa-passport' },
            { type: 'nationality', icon: 'fal fa-passport' },
            { type: 'company', icon: 'fal fa-building' },
            { type: 'job_title', icon: 'fal fa-briefcase' },
            { type: 'phone', icon: 'fal fa-phone' },
            { type: 'work_phone', icon: 'fal fa-phone' },
            { type: 'social_facebook', icon: 'fab fa-facebook' },
            { type: 'social_twitter', icon: 'fab fa-twitter' },
            { type: 'social_linkedin', icon: 'fab fa-linkedin' },
            { type: 'social_telegram', icon: 'fab fa-telegram-plane' },
            { type: 'social_whatsapp', icon: 'fab fa-whatsapp' },
            { type: 'street_address', icon: 'fal fa-mailbox' },
            { type: 'street_address_2', icon: 'fal fa-mailbox' },
            { type: 'city', icon: 'fal fa-city' },
            { type: 'zip_code', icon: 'fal fa-map-marked-alt' },
            { type: 'country', icon: 'fal fa-globe-europe' },
            { type: 'picture', icon: 'fal fa-image' },
        ],
    },
    {
        key: 'tags_fields',
        children: [
            { type: 'tag_radio', icon: 'fal fa-check-circle' },
            { type: 'tag_checkbox', icon: 'fal fa-check-square' },
            { type: 'tag_hidden', icon: 'fal fa-tag' },
        ],
    },
];

export function AddBlockButton(props) {
    const [displayTypes, setDisplayTypes] = useState(false);

    if (!displayTypes) {
        return (
            <div className="text-center">
                <button
                    type="button"
                    onClick={() => setDisplayTypes(true)}
                    className="btn btn-outline-primary border-0 text-uppercase"
                >
                    {translator.trans('form.add_block')}
                </button>
            </div>
        );
    }

    return (
        <div>
            {typeGroups.map((group) => (
                <div className="mb-3" key={group.key}>
                    <strong>{translator.trans('form.typesGroups.' + group.key + '.label')}</strong>
                    <div className="text-muted mb-2">{translator.trans('form.typesGroups.' + group.key + '.help')}</div>
                    <div className="row no-gutters">
                        {group.children.map((type) => (
                            <div
                                className={'mb-2 ' + (group.key !== 'tags_fields' ? 'col-2' : 'col-4')}
                                key={type.type}
                            >
                                <div className="px-1">
                                    <button
                                        type="button"
                                        className="btn btn-sm btn-block btn-secondary"
                                        style={{ height: 80 }}
                                        onClick={() => {
                                            setDisplayTypes(false);
                                            props.onAdd(type.type);
                                        }}
                                    >
                                        <div className="mb-1 h4">
                                            <i className={type.icon} />
                                        </div>
                                        {translator.trans('form.types.' + type.type)}
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            ))}

            <div className="text-center">
                <button
                    type="button"
                    onClick={() => setDisplayTypes(false)}
                    className="btn btn-outline-primary border-0 text-uppercase font-weight-normal"
                >
                    {translator.trans('form.cancel')}
                </button>
            </div>
        </div>
    );
}
