<?php

namespace App\Platform;

use App\Entity\Organization;

final class Plans
{
    public const ESSENTIAL = 'essential';
    public const STANDARD = 'standard';
    public const PREMIUM = 'premium';
    public const ORGANIZATION = 'organization';

    public static function all(): array
    {
        return [self::ESSENTIAL, self::STANDARD, self::PREMIUM, self::ORGANIZATION];
    }

    /**
     * @param Organization|string $planOrOrganization
     */
    public static function isFeatureAccessibleFor(string $feature, $planOrOrganization): bool
    {
        $plan = $planOrOrganization;
        if ($planOrOrganization instanceof Organization) {
            $plan = $planOrOrganization->getSubscriptionPlan();
        }

        return \in_array($feature, self::getPlansFeatures()[(string) $plan] ?? [], true);
    }

    private static function getPlansFeatures(): array
    {
        $featuresByPlan = [];
        $lastPlanFeatures = [];

        foreach (self::PLANS_FEATURES as $plan => $features) {
            $lastPlanFeatures = array_merge($lastPlanFeatures, $features);
            $featuresByPlan[$plan] = $lastPlanFeatures;
        }

        return $featuresByPlan;
    }

    private const PLANS_FEATURES = [
        self::ESSENTIAL => [
            Features::MODULE_WEBSITE,
            Features::MODULE_COMMUNITY,

            Features::TOOL_WEBSITE_PAGES,
            Features::TOOL_WEBSITE_NEWSLETTER,
            Features::TOOL_COMMUNITY_CONTACTS,
            Features::TOOL_COMMUNITY_EMAILING,
            Features::TOOL_COMMUNITY_TEXTING,

            Features::FEATURE_API,
        ],
        self::STANDARD => [
            Features::TOOL_WEBSITE_POSTS,
            Features::TOOL_WEBSITE_DOCUMENTS,
            Features::TOOL_WEBSITE_TROMBINOSCOPE,
            Features::TOOL_WEBSITE_MANIFESTO,

            Features::FEATURE_WEBSITE_SOCIAL_SHARERS,
            Features::FEATURE_COMMUNITY_EMAILING_TAGS,
            Features::FEATURE_COMMUNITY_EMAILING_AREAS,
            Features::FEATURE_COMMUNITY_EMAILING_SPECIFIC,
            Features::FEATURE_COMMUNITY_TEXTING_TAGS,
            Features::FEATURE_COMMUNITY_TEXTING_AREAS,
            Features::FEATURE_COMMUNITY_TEXTING_SPECIFIC,
        ],
        self::PREMIUM => [
            Features::TOOL_WEBSITE_EVENTS,
            Features::TOOL_WEBSITE_FORMS,
            Features::TOOL_WEBSITE_PETITIONS,

            Features::FEATURE_WEBSITE_STATS,
            Features::FEATURE_WEBSITE_SOCIAL_IFRAMES,
            Features::FEATURE_WEBSITE_SOCIAL_CROSSPOSTING,
            Features::FEATURE_COMMUNITY_EMAILING_STATS,
            Features::FEATURE_COMMUNITY_TEXTING_STATS,
            Features::FEATURE_COMMUNITY_CONTACTS_HISTORY,
            Features::FEATURE_COMMUNITY_CONTACTS_FLAGS,
            Features::FEATURE_COMMUNITY_AUTOMATIONS,

            Features::FEATURE_INTEGRATION_QUORUM,
            Features::FEATURE_INTEGRATION_WINGS,
            Features::FEATURE_INTEGRATION_REVUE,
        ],
        self::ORGANIZATION => [
            Features::MODULE_MEMBERS_AREA,

            Features::TOOL_MEMBERS_AREA_ACCOUNT,
            Features::TOOL_MEMBERS_AREA_RESOURCES,
            Features::TOOL_MEMBERS_AREA_EVENTS,
            Features::TOOL_MEMBERS_AREA_POSTS,
            Features::TOOL_MEMBERS_AREA_FORMS,

            Features::FEATURE_COMMUNITY_EMAILING_MEMBERS,

            Features::TOOL_COMMUNITY_PHONING,
            Features::FEATURE_COMMUNITY_PHONING_TAGS,
            Features::FEATURE_COMMUNITY_PHONING_AREAS,
            Features::FEATURE_COMMUNITY_PHONING_SPECIFIC,
            Features::FEATURE_COMMUNITY_PHONING_STATS,
            Features::FEATURE_COMMUNITY_PHONING_MEMBERS,

            Features::FEATURE_INTEGRATION_TELEGRAM,
            Features::FEATURE_INTEGRATION_INTEGROMAT,
        ],
    ];
}
