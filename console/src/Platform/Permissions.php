<?php

namespace App\Platform;

final class Permissions
{
    // Organization
    public const ORGANIZATION_SEE_CREDITS = 'organization_see_credits';
    public const ORGANIZATION_TEAM_MANAGE = 'organization_team_manage';
    public const ORGANIZATION_PROJECT_MANAGE = 'organization_project_manage';
    public const ORGANIZATION_COMMUNITY_MANAGE = 'organization_community_manage';
    public const ORGANIZATION_BILLING_MANAGE = 'organization_billing_manage';

    // Project
    public const PROJECT_CONFIG_APPEARANCE = 'project_config_appearance';
    public const PROJECT_CONFIG_SOCIALS = 'project_config_socials';
    public const PROJECT_DEVELOPER_THEME = 'project_developer_theme';
    public const PROJECT_DEVELOPER_REDIRECTIONS = 'project_developer_redirections';
    public const PROJECT_DEVELOPER_ACCESS = 'project_developer_access';

    // Website module
    public const WEBSITE_SEE_MODULE = 'website_see_module';
    public const WEBSITE_ACCESS_STATS = 'website_access_stats';

    // Pages
    public const WEBSITE_PAGES_MANAGE_ENTITY = 'website_pages_manage_entity';
    public const WEBSITE_PAGES_MANAGE = 'website_pages_manage';
    public const WEBSITE_PAGES_MANAGE_CATEGORIES = 'website_pages_manage_categories';

    // Posts
    public const WEBSITE_POSTS_MANAGE_ENTITY = 'website_posts_manage_entity';
    public const WEBSITE_POSTS_MANAGE_DRAFTS = 'website_posts_manage_drafts';
    public const WEBSITE_POSTS_MANAGE_PUBLISHED = 'website_posts_manage_published';
    public const WEBSITE_POSTS_PUBLISH = 'website_posts_publish';
    public const WEBSITE_POSTS_MANAGE_CATEGORIES = 'website_posts_manage_categories';

    // Documents
    public const WEBSITE_DOCUMENTS_MANAGE = 'website_documents_manage';

    // Events
    public const WEBSITE_EVENTS_MANAGE_ENTITY = 'website_events_manage_entity';
    public const WEBSITE_EVENTS_MANAGE_DRAFTS = 'website_events_manage_drafts';
    public const WEBSITE_EVENTS_MANAGE_PUBLISHED = 'website_events_manage_published';
    public const WEBSITE_EVENTS_PUBLISH = 'website_events_publish';

    // Forms
    public const WEBSITE_FORMS_MANAGE = 'website_forms_manage';
    public const WEBSITE_FORMS_ACCESS_RESULTS = 'website_forms_access_results';

    // Petitions
    public const WEBSITE_PETITIONS_MANAGE_ENTITY = 'website_petitions_manage_entity';
    public const WEBSITE_PETITIONS_MANAGE_DRAFTS = 'website_petitions_manage_drafts';
    public const WEBSITE_PETITIONS_MANAGE_PUBLISHED = 'website_petitions_manage_published';
    public const WEBSITE_PETITIONS_PUBLISH = 'website_petitions_publish';
    public const WEBSITE_PETITIONS_MANAGE_CATEGORIES = 'website_petitions_manage_categories';

    // Trombinoscope
    public const WEBSITE_TROMBINOSCOPE_MANAGE_ENTITY = 'website_trombinoscope_manage_entity';
    public const WEBSITE_TROMBINOSCOPE_MANAGE_DRAFTS = 'website_trombinoscope_manage_drafts';
    public const WEBSITE_TROMBINOSCOPE_MANAGE_PUBLISHED = 'website_trombinoscope_manage_published';
    public const WEBSITE_TROMBINOSCOPE_PUBLISH = 'website_trombinoscope_publish';
    public const WEBSITE_TROMBINOSCOPE_MANAGE_CATEGORIES = 'website_trombinoscope_manage_categories';

    // Manifesto
    public const WEBSITE_MANIFESTO_MANAGE_ENTITY = 'website_manifesto_manage_entity';
    public const WEBSITE_MANIFESTO_MANAGE_DRAFTS = 'website_manifesto_manage_drafts';
    public const WEBSITE_MANIFESTO_MANAGE_PUBLISHED = 'website_manifesto_manage_published';
    public const WEBSITE_MANIFESTO_PUBLISH = 'website_manifesto_publish';

    // Community module
    public const COMMUNITY_SEE_MODULE = 'community_see_module';
    public const COMMUNITY_ACCESS_STATS = 'community_access_stats';

    // Contacts
    public const COMMUNITY_CONTACTS_VIEW = 'community_contacts_view';
    public const COMMUNITY_CONTACTS_UPDATE = 'community_contacts_update';
    public const COMMUNITY_CONTACTS_DELETE = 'community_contacts_delete';
    public const COMMUNITY_CONTACTS_TAG_ADD = 'community_contacts_tag_add';

    // Emailing
    public const COMMUNITY_EMAIL_MANAGE_DRAFTS = 'community_emailing_manage_drafts';
    public const COMMUNITY_EMAIL_SEND = 'community_emailing_send';
    public const COMMUNITY_EMAIL_STATS = 'community_emailing_stats';

    // Texting
    public const COMMUNITY_TEXTING_MANAGE_DRAFTS = 'community_texting_manage_drafts';
    public const COMMUNITY_TEXTING_SEND = 'community_texting_send';
    public const COMMUNITY_TEXTING_STATS = 'community_texting_stats';

    // Phoning
    public const COMMUNITY_PHONING_MANAGE_DRAFTS = 'community_phoning_manage_drafts';
    public const COMMUNITY_PHONING_MANAGE_ACTIVE = 'community_phoning_manage_active';
    public const COMMUNITY_PHONING_STATS = 'community_phoning_stats';

    public static function allForProjects()
    {
        return [
            // Modules
            self::WEBSITE_SEE_MODULE,
            self::COMMUNITY_SEE_MODULE,

            // Tools
            self::PROJECT_CONFIG_APPEARANCE,
            self::PROJECT_CONFIG_SOCIALS,
            self::PROJECT_DEVELOPER_THEME,
            self::PROJECT_DEVELOPER_REDIRECTIONS,
            self::PROJECT_DEVELOPER_ACCESS,

            self::WEBSITE_PAGES_MANAGE,
            self::WEBSITE_PAGES_MANAGE_CATEGORIES,

            self::WEBSITE_POSTS_MANAGE_DRAFTS,
            self::WEBSITE_POSTS_MANAGE_PUBLISHED,
            self::WEBSITE_POSTS_PUBLISH,
            self::WEBSITE_POSTS_MANAGE_CATEGORIES,

            self::WEBSITE_DOCUMENTS_MANAGE,

            self::WEBSITE_EVENTS_MANAGE_DRAFTS,
            self::WEBSITE_EVENTS_MANAGE_PUBLISHED,
            self::WEBSITE_EVENTS_PUBLISH,

            self::WEBSITE_FORMS_MANAGE,
            self::WEBSITE_FORMS_ACCESS_RESULTS,

            self::WEBSITE_PETITIONS_MANAGE_DRAFTS,
            self::WEBSITE_PETITIONS_MANAGE_PUBLISHED,
            self::WEBSITE_PETITIONS_PUBLISH,
            self::WEBSITE_PETITIONS_MANAGE_CATEGORIES,

            self::WEBSITE_TROMBINOSCOPE_MANAGE_DRAFTS,
            self::WEBSITE_TROMBINOSCOPE_MANAGE_PUBLISHED,
            self::WEBSITE_TROMBINOSCOPE_PUBLISH,
            self::WEBSITE_TROMBINOSCOPE_MANAGE_CATEGORIES,

            self::WEBSITE_MANIFESTO_MANAGE_DRAFTS,
            self::WEBSITE_MANIFESTO_MANAGE_PUBLISHED,
            self::WEBSITE_MANIFESTO_PUBLISH,

            self::WEBSITE_ACCESS_STATS,

            self::COMMUNITY_CONTACTS_VIEW,
            self::COMMUNITY_CONTACTS_UPDATE,
            self::COMMUNITY_CONTACTS_DELETE,
            self::COMMUNITY_CONTACTS_TAG_ADD,

            self::COMMUNITY_EMAIL_MANAGE_DRAFTS,
            self::COMMUNITY_EMAIL_SEND,
            self::COMMUNITY_EMAIL_STATS,

            self::COMMUNITY_TEXTING_MANAGE_DRAFTS,
            self::COMMUNITY_TEXTING_SEND,
            self::COMMUNITY_TEXTING_STATS,

            self::COMMUNITY_PHONING_MANAGE_DRAFTS,
            self::COMMUNITY_PHONING_MANAGE_ACTIVE,
            self::COMMUNITY_PHONING_STATS,

            self::COMMUNITY_ACCESS_STATS,
        ];
    }
}
