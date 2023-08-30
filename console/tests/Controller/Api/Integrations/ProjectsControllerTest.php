<?php

namespace App\Tests\Controller\Api\Integrations;

use App\Tests\ApiTestCase;

class ProjectsControllerTest extends ApiTestCase
{
    public function testIndex()
    {
        $client = self::createClient();

        $result = $this->apiRequest(
            $client,
            'GET',
            '/api/integrations/projects',
            'telegram_16c43545f60e99a58c699e8473266352b6c0dfdd36c5963883ea3e7a80662538'
        );

        $this->assertApiResponse($result, [
            '_resource' => 'Dashboard',
            'organization' => 'Acme',
            'globalProjects' => [
                [
                    '_resource' => 'DashboardItem',
                    '_links' => [
                        'stats_traffic' => 'http://localhost/api/integrations/'.self::PROJECT_ACME_UUID.'/stats/traffic',
                        'stats_community' => 'http://localhost/api/integrations/'.self::PROJECT_ACME_UUID.'/stats/community',
                    ],
                    'id' => '2c720420-65fd-4360-9d77-731758008497',
                    'name' => 'Acme Inc',
                    'areas' => null,
                    'tools' => [
                        'website_pages',
                        'website_posts',
                        'website_documents',
                        'website_events',
                        'website_forms',
                        'website_newsletter',
                        'website_trombinoscope',
                        'website_manifesto',
                        'community_contacts',
                        'community_emailing',
                        'community_texting',
                        'community_phoning',
                        'members_area_account',
                    ],
                    'contacts' => 0,
                    'members' => 0,
                ],
            ],
            'localProjects' => [],
        ]);
    }

    public function testIndexNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/integrations/projects', null, 401);
    }

    public function testIndexInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/integrations/projects', 'invalid', 401);
    }
}
