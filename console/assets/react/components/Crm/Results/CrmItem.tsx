import React, { useEffect, useState } from 'react';
import { CrmItemProfile, CrmItemProfileLabels } from './CrmItemProfile';
import { CrmDocument } from '../Model/crm';
import { countryName, datetimeFormat } from '../../../../utils/formatter';
import { Popover2, Tooltip2 } from '@blueprintjs/popover2';
import { Button, Collapse, Menu, MenuItem, Position } from '@blueprintjs/core';
import { TimeAgo } from '../../Layout/TimeAgo';
import { Tag, TagsRegistry } from '../../Layout/TagsSelector/Model/tag';
import { CrmItemAddress } from './CrmItemAddress';
import { CrmItemTags, CrmItemTagsLabels } from './CrmItemTags';
import { createLink } from '../../../../utils/createLink';
import { UpdateTagsCallback } from '../CrmSearchEngine';

export interface CrmItemLinks {
    view: string;
    edit: string;
    history: string;
}

export interface CrmItemLabels {
    status: {
        c: string;
        m: string;
        u: string;
    };
    actions: {
        view: string;
        edit: string;
    };
}

interface Props {
    isReadOnly: boolean;
    tagsRegistry: TagsRegistry;
    tagsNamesRegistry: string[];
    updateTagsCallback: UpdateTagsCallback;
    result: CrmDocument;
    isOpen: boolean;
    onClick: () => void;
    links: CrmItemLinks;
    itemLabels: CrmItemLabels;
    tagsLabels: CrmItemTagsLabels;
    profileLabels: CrmItemProfileLabels;
}

export function CrmItem(props: Props) {
    const item = props.result;

    const [tags, setTags] = useState<Tag[] | null>(null);

    const handleTagsChange = (newTags: Tag[]) => {
        props.updateTagsCallback(item.id, newTags);
        setTags(newTags);
    };

    let details: { [key: string]: any } = {
        mainName: item.email,
        subName: '',
        picture: item.picture
            ? '/serve/' + item.picture
            : 'https://www.gravatar.com/avatar/' + item.email_hash + '?d=mp&s=64',
    };

    if (item.profile_first_name || item.profile_last_name) {
        details.mainName = item.profile_first_name + ' ' + item.profile_last_name;
        details.subName = (
            <a href={'mailto:' + item.email} target="_blank" onClick={(e) => e.stopPropagation()}>
                {item.email}
            </a>
        );
    }

    // Select first non-empty location
    const locationPriority = [
        item.area_zip_code_name,
        item.address_zip_code,
        item.area_district_name,
        item.area_province_name,
        item.address_country ? countryName(item.address_country) : '',
        item.area_country_code ? countryName(item.area_country_code) : '',
    ];

    return (
        <div className="crm-search-results-item">
            <div className="crm-search-results-item-details" onClick={props.onClick}>
                <div className="crm-search-results-item-picture">
                    <a href={createLink(props.links.view, { '-uuid-': item.id })} onClick={(e) => e.stopPropagation()}>
                        <img src={details.picture} />
                    </a>
                </div>

                <div className="crm-search-results-item-name">
                    <div>
                        <h4 className="crm-search-results-item-mainname">
                            <a
                                href={createLink(props.links.view, { '-uuid-': item.id })}
                                onClick={(e) => e.stopPropagation()}
                            >
                                {details.mainName}
                            </a>
                        </h4>

                        <div className="crm-search-results-item-subname">{details.subName}</div>
                    </div>
                </div>

                <div className="crm-search-results-item-location">
                    <div>
                        <Tooltip2 content={<CrmItemAddress item={item} />} position={Position.TOP}>
                            <div>
                                {locationPriority.reduce((prev, value) => (!prev && value ? value : prev))}
                                {item.area_country_code ? <span className={'fi fi-' + item.area_country_code} /> : ''}
                            </div>
                        </Tooltip2>
                    </div>
                </div>

                <div className="crm-search-results-item-activity">
                    <div>
                        <Tooltip2 content={datetimeFormat(item.created_at)} position={Position.TOP}>
                            <div>
                                <TimeAgo date={item.created_at} />
                            </div>
                        </Tooltip2>
                    </div>
                </div>

                <div
                    className={'crm-search-results-item-status crm-search-results-item-status-' + item.status}
                    onClick={(e) => e.stopPropagation()}
                >
                    <div className="crm-search-results-item-status-label">{props.itemLabels.status[item.status]}</div>

                    <div className="crm-search-results-item-status-action">
                        <Popover2
                            content={<CrmItemMenu result={item} links={props.links} labels={props.itemLabels} />}
                            position="bottom-right"
                        >
                            <Button outlined={true} large={true} className="crm-search-results-item-status-button">
                                <i className="far fa-ellipsis-v" />
                            </Button>
                        </Popover2>
                    </div>
                </div>
            </div>

            <CrmItemTags
                isReadOnly={props.isReadOnly}
                result={item}
                tags={tags}
                onTagsChange={handleTagsChange}
                tagsRegistry={props.tagsRegistry}
                tagsNamesRegistry={props.tagsNamesRegistry}
                labels={props.tagsLabels}
            />

            <Collapse isOpen={props.isOpen}>
                <CrmItemProfile result={item} links={props.links} labels={props.profileLabels} />
            </Collapse>
        </div>
    );
}

function CrmItemMenu(props: { result: CrmDocument; links: CrmItemLinks; labels: CrmItemLabels }) {
    return (
        <Menu>
            <MenuItem
                text={props.labels.actions.view}
                href={createLink(props.links.view, { '-uuid-': props.result.id })}
            />
            <MenuItem
                text={props.labels.actions.edit}
                href={createLink(props.links.edit, { '-uuid-': props.result.id })}
            />
        </Menu>
    );
}
