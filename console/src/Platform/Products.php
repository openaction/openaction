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
}
