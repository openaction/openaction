<?php

namespace App\Tests\Controller\Console;

use App\Tests\WebTestCase;

class UnauthorizedAccessTest extends WebTestCase
{
    public function provideConsoleUnauthorizedAnonymousAccess()
    {
        yield ['/console/api/areas/search'];
        yield ['/console/api/csrf-token/refresh'];
        yield ['/console/api/tags/search'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/configure/main-tags'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/configure/tags'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts/20e51b91-bdec-495d-854d-85d6e74fc75e/view'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts/create'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts/20e51b91-bdec-495d-854d-85d6e74fc75e/edit'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations/disabled'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations/828e0d22-0fab-4a59-a9d6-9b5dc575680f/preview'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations/828e0d22-0fab-4a59-a9d6-9b5dc575680f/metadata'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations/828e0d22-0fab-4a59-a9d6-9b5dc575680f/content'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts/export'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts/import'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/integrations'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/integrations/telegram'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/integrations/telegram/register'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/integrations/telegram/3a9c0c55-bb74-48d7-9cce-117fbf8e0293/details'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/projects'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/project/create'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/team'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/team/invite/member'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/team/pending'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/community/contacts'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/community/contacts/20e51b91-bdec-495d-854d-85d6e74fc75e/view'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/community/contacts/20e51b91-bdec-495d-854d-85d6e74fc75e/edit'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/community/emailing'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/community/emailing/31fedd69-2d28-4900-8088-d28ad9606a99/metadata'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/community/emailing/31fedd69-2d28-4900-8088-d28ad9606a99/content'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/community/emailing/31fedd69-2d28-4900-8088-d28ad9606a99/send-test'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/community/emailing/31fedd69-2d28-4900-8088-d28ad9606a99/send-all'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/community/emailing/31fedd69-2d28-4900-8088-d28ad9606a99/stats'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/community/emailing/31fedd69-2d28-4900-8088-d28ad9606a99/report'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/community/emailing/31fedd69-2d28-4900-8088-d28ad9606a99/report/search'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/access'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/appearance'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/appearance/logos'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/appearance/terminology'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/appearance/website/homepage/block/create'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/appearance/website/homepage'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/appearance/website/intro'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/appearance/website/theme'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/menu'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/menu/create/header'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings/membership'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings/details'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings/modules'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings/legalities'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings/domain'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings/remove'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/social-networks/metas'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/social-networks/accounts'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/social-networks/sharers'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/developers/access'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/developers/redirections'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/developers/redirections/create'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/developers/theme'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/start'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/stats/traffic'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/stats/traffic/live'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/stats/community'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/upgrade'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/documents'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/documents/58da29fd-2190-41c4-8bcb-9e0bbd0ee042/download'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/categories'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/categories/9966b3b5-901d-4609-9cf1-ffa949987043/edit'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/b186291d-b1ee-5458-a0f2-e31410fd26a5/edit'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/b186291d-b1ee-5458-a0f2-e31410fd26a5/view'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/forms'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/forms/a2b2dbd9-f0b8-435c-ae65-00bc93ad3356/edit'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/forms/a2b2dbd9-f0b8-435c-ae65-00bc93ad3356/view'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/forms/results/a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/forms/results/a2b2dbd9-f0b8-435c-ae65-00bc93ad3356/export'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/forms/results/a2b2dbd9-f0b8-435c-ae65-00bc93ad3356/view'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/manifesto'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/manifesto/topic/61d592f6-8435-4b7f-984a-d6b2f406c36b/edit'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/manifesto/topic/61d592f6-8435-4b7f-984a-d6b2f406c36b/view'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/manifesto/proposal/85a12e9e-921e-43a1-a12d-630de3656510/edit'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/categories'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/categories/create'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/categories/e2c5977a-5ddd-41b6-93b8-ccc7cea925cf/edit'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/30f26de9-fe21-4d24-9b17-217d02156ac9/edit'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/30f26de9-fe21-4d24-9b17-217d02156ac9/view'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/categories'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/categories/create'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/categories/62686dd5-33b6-476f-bedb-bfbc3a84df0d/edit'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/53aba31d-f8bb-483d-a5dd-2926a1d2265e/edit'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/53aba31d-f8bb-483d-a5dd-2926a1d2265e/view'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/trombinoscope/categories'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/trombinoscope/categories/ee760968-6581-40ad-8b4d-af073e8943a4/edit'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/trombinoscope'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/view'];
        yield ['/console/user/account'];
        yield ['/console/user/notification/update'];
        yield ['/console/user/two-factor'];
        yield ['/console/user/two-factor/confirm-password'];
        yield ['/console/user/two-factor/enable'];
        yield ['/console/user/two-factor/qr-code'];
        yield ['/console/user/two-factor/enabled'];
        yield ['/console/user/two-factor/download-backup-codes'];
        yield ['/console/user/two-factor/disable'];
        yield ['/console/user/password/update'];
        yield ['/console/i/t-me/1mart8wanRysFnJD3QjCS3'];
    }

    /**
     * @dataProvider provideConsoleUnauthorizedAnonymousAccess
     */
    public function testConsoleUnauthorizedAnonymousAccess(string $url)
    {
        $client = static::createClient();
        $client->request('GET', $url);
        $this->assertResponseRedirects('/security/login');
    }

    public function provideConsoleUnauthorizedWrongUserAccess()
    {
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/configure/main-tags'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/configure/tags'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts/20e51b91-bdec-495d-854d-85d6e74fc75e/view'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts/create'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts/20e51b91-bdec-495d-854d-85d6e74fc75e/edit'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations/disabled'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations/828e0d22-0fab-4a59-a9d6-9b5dc575680f/preview'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations/828e0d22-0fab-4a59-a9d6-9b5dc575680f/metadata'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/automations/828e0d22-0fab-4a59-a9d6-9b5dc575680f/content'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts/export'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts/import'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/integrations'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/integrations/telegram'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/integrations/telegram/register'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/integrations/telegram/3a9c0c55-bb74-48d7-9cce-117fbf8e0293/details'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/projects'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/project/create'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/team'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/team/invite/member'];
        yield ['/console/organization/'.self::ORGA_CITIPO_UUID.'/team/pending'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/community/contacts'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/community/contacts/20e51b91-bdec-495d-854d-85d6e74fc75e/view'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/community/contacts/20e51b91-bdec-495d-854d-85d6e74fc75e/edit'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/community/emailing'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/community/emailing/31fedd69-2d28-4900-8088-d28ad9606a99/metadata'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/community/emailing/31fedd69-2d28-4900-8088-d28ad9606a99/content'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/community/emailing/31fedd69-2d28-4900-8088-d28ad9606a99/send-test'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/community/emailing/31fedd69-2d28-4900-8088-d28ad9606a99/send-all'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/community/emailing/31fedd69-2d28-4900-8088-d28ad9606a99/stats'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/community/emailing/31fedd69-2d28-4900-8088-d28ad9606a99/report'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/community/emailing/31fedd69-2d28-4900-8088-d28ad9606a99/report/search'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/access'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/appearance'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/appearance/logos'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/appearance/terminology'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/appearance/website/homepage/block/create'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/appearance/website/homepage'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/appearance/website/intro'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/appearance/website/theme'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/menu'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/menu/create/header'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings/membership'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings/details'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings/modules'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings/legalities'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings/domain'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings/remove'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/social-networks/metas'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/social-networks/accounts'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/social-networks/sharers'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/developers/access'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/developers/redirections'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/developers/redirections/create'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/developers/theme'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/start'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/stats/traffic'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/stats/traffic/live'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/stats/community'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/upgrade'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/documents'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/documents/58da29fd-2190-41c4-8bcb-9e0bbd0ee042/download'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/categories'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/categories/9966b3b5-901d-4609-9cf1-ffa949987043/edit'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/b186291d-b1ee-5458-a0f2-e31410fd26a5/edit'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/events/b186291d-b1ee-5458-a0f2-e31410fd26a5/view'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/forms'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/forms/a2b2dbd9-f0b8-435c-ae65-00bc93ad3356/edit'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/forms/a2b2dbd9-f0b8-435c-ae65-00bc93ad3356/view'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/forms/results/a2b2dbd9-f0b8-435c-ae65-00bc93ad3356'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/forms/results/a2b2dbd9-f0b8-435c-ae65-00bc93ad3356/export'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/forms/results/a2b2dbd9-f0b8-435c-ae65-00bc93ad3356/view'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/manifesto'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/manifesto/topic/61d592f6-8435-4b7f-984a-d6b2f406c36b/edit'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/manifesto/topic/61d592f6-8435-4b7f-984a-d6b2f406c36b/view'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/manifesto/proposal/85a12e9e-921e-43a1-a12d-630de3656510/edit'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/categories'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/categories/create'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/categories/e2c5977a-5ddd-41b6-93b8-ccc7cea925cf/edit'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/30f26de9-fe21-4d24-9b17-217d02156ac9/edit'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/pages/30f26de9-fe21-4d24-9b17-217d02156ac9/view'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/categories'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/categories/create'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/categories/62686dd5-33b6-476f-bedb-bfbc3a84df0d/edit'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/53aba31d-f8bb-483d-a5dd-2926a1d2265e/edit'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/posts/53aba31d-f8bb-483d-a5dd-2926a1d2265e/view'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/trombinoscope/categories'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/trombinoscope/categories/ee760968-6581-40ad-8b4d-af073e8943a4/edit'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/trombinoscope'];
        yield ['/console/project/'.self::PROJECT_CITIPO_UUID.'/website/view'];
        yield ['/console/i/t-me/1mart8wanRysFnJD3QjCS3'];
    }

    /**
     * @dataProvider provideConsoleUnauthorizedWrongUserAccess
     */
    public function testConsoleUnauthorizedWrongUserAccess(string $url)
    {
        $client = static::createClient();
        $this->authenticate($client, 'ema.anderson@away.com');

        $client->request('GET', $url);
        $this->assertResponseStatusCodeSame(404);
    }
}
