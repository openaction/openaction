<?php

namespace App\Tests\Controller\Api\Integrations;

use App\Tests\ApiTestCase;

class StatsControllerTest extends ApiTestCase
{
    public function testTraffic()
    {
        $client = self::createClient();

        $result = $this->apiRequest(
            $client,
            'GET',
            '/api/integrations/'.self::PROJECT_ACME_UUID.'/stats/traffic',
            'telegram_16c43545f60e99a58c699e8473266352b6c0dfdd36c5963883ea3e7a80662538'
        );

        $this->assertApiResponse($result, [
            '_resource' => 'TrafficDashboard',
            'totals' => ['users' => 0, 'page_views' => 0],
            'pages' => [],
            'sources' => [],
            'countries' => [],
            'platforms' => [],
            'browsers' => [],
        ]);

        $this->assertArrayHasKey('traffic', $result);
    }

    public function testTrafficNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/integrations/'.self::PROJECT_ACME_UUID.'/stats/traffic', null, 401);
    }

    public function testTrafficInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/integrations/'.self::PROJECT_ACME_UUID.'/stats/traffic', 'invalid', 401);
    }

    public function testCommunity()
    {
        $client = self::createClient();

        $result = $this->apiRequest(
            $client,
            'GET',
            '/api/integrations/'.self::PROJECT_ACME_UUID.'/stats/community',
            'telegram_16c43545f60e99a58c699e8473266352b6c0dfdd36c5963883ea3e7a80662538'
        );

        $this->assertApiResponse($result, [
            '_resource' => 'CommunityDashboard',
            'totals' => ['contacts' => 0, 'members' => 0, 'newsletter_subscribers' => 0, 'sms_subscribers' => 0],
            'tags' => [],
            'countries' => [],
        ]);

        $this->assertArrayHasKey('growth', $result);
    }

    public function testCommunityNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/integrations/'.self::PROJECT_ACME_UUID.'/stats/community', null, 401);
    }

    public function testCommunityInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/integrations/'.self::PROJECT_ACME_UUID.'/stats/community', 'invalid', 401);
    }
}
