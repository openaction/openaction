<?php

namespace App\Platform;

final class Products
{
    /*
     * Plans
     */
    public const PLAN_ESSENTIAL = 'plan_essential';
    public const PLAN_STANDARD = 'plan_standard';
    public const PLAN_PREMIUM = 'plan_premium';
    public const PLAN_ORGANIZATION = 'plan_organization';

    public const PLAN_LEGISLATIVES = 'plan_legislatives';
    public const PLAN_WEBSITE = 'plan_website';
    public const PLAN_AVECVOUS = 'plan_avecvous';

    /*
     * Credits
     */
    public const CREDIT_EMAIL = 'email_credit';
    public const CREDIT_TEXT = 'text_credit';

    /*
     * Extra products
     */
    public const DOMAIN_NAME = 'domain_name';
    public const CUSTOM_SERVICE = 'custom_service';
    public const GSUITE_CAMPAIGN_ACCOUNT = 'gsuite_campaign_account';

    /*
     * Print
     */
    public const PRINT_OFFICIAL_POSTER = 'official_poster';
    public const PRINT_OFFICIAL_BANNER = 'official_banner';
    public const PRINT_OFFICIAL_PLEDGE = 'official_pledge';
    public const PRINT_OFFICIAL_BALLOT = 'official_ballot';
    public const PRINT_CAMPAIGN_FLYER = 'campaign_flyer';
    public const PRINT_CAMPAIGN_LARGE_FLYER = 'campaign_large_flyer';
    public const PRINT_CAMPAIGN_DOOR = 'campaign_door';
    public const PRINT_CAMPAIGN_BOOKLET_4 = 'campaign_booklet_4';
    public const PRINT_CAMPAIGN_BOOKLET_8 = 'campaign_booklet_8';
    public const PRINT_CAMPAIGN_LETTER = 'campaign_letter';
    public const PRINT_CAMPAIGN_POSTER = 'campaign_poster';
    public const PRINT_CAMPAIGN_CARD = 'campaign_card';
    public const PRINT_DELIVERY = 'delivery';
    public const PRINT_ENVELOPING = 'enveloping';
    public const PRINT_SENDING = 'sending';

    public static function getPrintProducts(): array
    {
        return [
            self::PRINT_OFFICIAL_POSTER,
            self::PRINT_OFFICIAL_BANNER,
            self::PRINT_OFFICIAL_PLEDGE,
            self::PRINT_OFFICIAL_BALLOT,
            self::PRINT_CAMPAIGN_POSTER,
            self::PRINT_CAMPAIGN_FLYER,
            self::PRINT_CAMPAIGN_BOOKLET_4,
            self::PRINT_CAMPAIGN_BOOKLET_8,
            self::PRINT_CAMPAIGN_LARGE_FLYER,
            self::PRINT_CAMPAIGN_LETTER,
            self::PRINT_CAMPAIGN_DOOR,
            self::PRINT_CAMPAIGN_CARD,
        ];
    }
}
