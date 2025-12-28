<?php

namespace App\Platform;

final class Features
{
    /*
     * Organization features
     */

    // Website
    public const FEATURE_WEBSITE_STATS = 'feature_website_stats';
    public const FEATURE_WEBSITE_SOCIAL_SHARERS = 'feature_website_social_sharers';
    public const FEATURE_WEBSITE_SOCIAL_IFRAMES = 'feature_website_social_iframes';
    public const FEATURE_WEBSITE_SOCIAL_CROSSPOSTING = 'feature_website_social_crossposting';

    // Community
    public const FEATURE_COMMUNITY_EMAILING_TAGS = 'feature_community_emailing_tags';
    public const FEATURE_COMMUNITY_EMAILING_AREAS = 'feature_community_emailing_areas';
    public const FEATURE_COMMUNITY_EMAILING_SPECIFIC = 'feature_community_emailing_specific';
    public const FEATURE_COMMUNITY_EMAILING_MEMBERS = 'feature_community_emailing_members';
    public const FEATURE_COMMUNITY_EMAILING_STATS = 'feature_community_emailing_stats';
    public const FEATURE_COMMUNITY_TEXTING_TAGS = 'feature_community_texting_tags';
    public const FEATURE_COMMUNITY_TEXTING_AREAS = 'feature_community_texting_areas';
    public const FEATURE_COMMUNITY_TEXTING_SPECIFIC = 'feature_community_texting_specific';
    public const FEATURE_COMMUNITY_TEXTING_MEMBERS = 'feature_community_texting_members';
    public const FEATURE_COMMUNITY_TEXTING_STATS = 'feature_community_texting_stats';
    public const FEATURE_COMMUNITY_PHONING_TAGS = 'feature_community_phoning_tags';
    public const FEATURE_COMMUNITY_PHONING_AREAS = 'feature_community_phoning_areas';
    public const FEATURE_COMMUNITY_PHONING_SPECIFIC = 'feature_community_phoning_specific';
    public const FEATURE_COMMUNITY_PHONING_MEMBERS = 'feature_community_phoning_members';
    public const FEATURE_COMMUNITY_PHONING_STATS = 'feature_community_phoning_stats';
    public const FEATURE_COMMUNITY_CONTACTS_FLAGS = 'feature_community_contacts_flags';
    public const FEATURE_COMMUNITY_CONTACTS_HISTORY = 'feature_community_contacts_history';
    public const FEATURE_COMMUNITY_AUTOMATIONS = 'feature_community_automations';

    // Integrations
    public const FEATURE_INTEGRATION_WINGS = 'feature_integration_wings';
    public const FEATURE_INTEGRATION_QUORUM = 'feature_integration_quorum';
    public const FEATURE_INTEGRATION_TELEGRAM = 'feature_integration_telegram';
    public const FEATURE_INTEGRATION_INTEGROMAT = 'feature_integration_integromat';

    // API
    public const FEATURE_API = 'feature_api';

    /*
     * Project modules and tools
     */

    // Website
    public const MODULE_WEBSITE = 'website';
    public const TOOL_WEBSITE_PAGES = 'website_pages';
    public const TOOL_WEBSITE_POSTS = 'website_posts';
    public const TOOL_WEBSITE_DOCUMENTS = 'website_documents';
    public const TOOL_WEBSITE_EVENTS = 'website_events';
    public const TOOL_WEBSITE_FORMS = 'website_forms';
    public const TOOL_WEBSITE_PETITIONS = 'website_petitions';
    public const TOOL_WEBSITE_NEWSLETTER = 'website_newsletter';
    public const TOOL_WEBSITE_TROMBINOSCOPE = 'website_trombinoscope';
    public const TOOL_WEBSITE_MANIFESTO = 'website_manifesto';

    // Community
    public const MODULE_COMMUNITY = 'community';
    public const TOOL_COMMUNITY_CONTACTS = 'community_contacts';
    public const TOOL_COMMUNITY_EMAILING = 'community_emailing';
    public const TOOL_COMMUNITY_TEXTING = 'community_texting';
    public const TOOL_COMMUNITY_PHONING = 'community_phoning';

    // Members
    public const MODULE_MEMBERS_AREA = 'members_area';
    public const TOOL_MEMBERS_AREA_ACCOUNT = 'members_area_account';
    public const TOOL_MEMBERS_AREA_RESOURCES = 'members_area_resources';
    public const TOOL_MEMBERS_AREA_POSTS = 'members_area_posts';
    public const TOOL_MEMBERS_AREA_EVENTS = 'members_area_events';
    public const TOOL_MEMBERS_AREA_FORMS = 'members_area_forms';

    public static function all(): array
    {
        return array_merge(self::allModules(), self::allTools(), [
            self::FEATURE_WEBSITE_STATS,
            self::FEATURE_WEBSITE_SOCIAL_SHARERS,
            self::FEATURE_WEBSITE_SOCIAL_IFRAMES,
            self::FEATURE_WEBSITE_SOCIAL_CROSSPOSTING,

            self::FEATURE_COMMUNITY_EMAILING_TAGS,
            self::FEATURE_COMMUNITY_EMAILING_AREAS,
            self::FEATURE_COMMUNITY_EMAILING_SPECIFIC,
            self::FEATURE_COMMUNITY_EMAILING_MEMBERS,
            self::FEATURE_COMMUNITY_EMAILING_STATS,
            self::FEATURE_COMMUNITY_TEXTING_TAGS,
            self::FEATURE_COMMUNITY_TEXTING_AREAS,
            self::FEATURE_COMMUNITY_TEXTING_SPECIFIC,
            self::FEATURE_COMMUNITY_TEXTING_MEMBERS,
            self::FEATURE_COMMUNITY_TEXTING_STATS,
            self::FEATURE_COMMUNITY_PHONING_TAGS,
            self::FEATURE_COMMUNITY_PHONING_AREAS,
            self::FEATURE_COMMUNITY_PHONING_SPECIFIC,
            self::FEATURE_COMMUNITY_PHONING_MEMBERS,
            self::FEATURE_COMMUNITY_PHONING_STATS,
            self::FEATURE_COMMUNITY_CONTACTS_FLAGS,
            self::FEATURE_COMMUNITY_CONTACTS_HISTORY,
            self::FEATURE_COMMUNITY_AUTOMATIONS,

            self::FEATURE_INTEGRATION_QUORUM,
            self::FEATURE_INTEGRATION_TELEGRAM,

            self::FEATURE_API,
        ]);
    }

    public static function allModules(): array
    {
        return [
            self::MODULE_WEBSITE,
            self::MODULE_COMMUNITY,
            self::MODULE_MEMBERS_AREA,
        ];
    }

    public static function allTools(): array
    {
        return [
            self::TOOL_WEBSITE_PAGES,
            self::TOOL_WEBSITE_POSTS,
            self::TOOL_WEBSITE_DOCUMENTS,
            self::TOOL_WEBSITE_EVENTS,
            self::TOOL_WEBSITE_FORMS,
            self::TOOL_WEBSITE_PETITIONS,
            self::TOOL_WEBSITE_NEWSLETTER,
            self::TOOL_WEBSITE_TROMBINOSCOPE,
            self::TOOL_WEBSITE_MANIFESTO,

            self::TOOL_COMMUNITY_CONTACTS,
            self::TOOL_COMMUNITY_EMAILING,
            self::TOOL_COMMUNITY_TEXTING,
            self::TOOL_COMMUNITY_PHONING,

            self::TOOL_MEMBERS_AREA_ACCOUNT,
            self::TOOL_MEMBERS_AREA_RESOURCES,
            self::TOOL_MEMBERS_AREA_POSTS,
            self::TOOL_MEMBERS_AREA_EVENTS,
            self::TOOL_MEMBERS_AREA_FORMS,
        ];
    }
}
