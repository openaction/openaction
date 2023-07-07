<?php

namespace App\Tests\Controller\Api\Integrations;

use App\Tests\ApiTestCase;

class AccountControllerTest extends ApiTestCase
{
    public function testIndex()
    {
        $client = self::createClient();

        $result = $this->apiRequest(
            $client,
            'GET',
            '/api/integrations/account',
            'telegram_16c43545f60e99a58c699e8473266352b6c0dfdd36c5963883ea3e7a80662538'
        );

        $this->assertApiResponse($result, [
            '_resource' => 'OrganizationMember',
            'firstName' => 'Titouan',
            'lastName' => 'Galopin',
            'isAdmin' => true,
            'externalPermissions' => ['Acme'],
        ]);
    }

    public function testIndexNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/integrations/account', null, 401);
    }

    public function testIndexInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/integrations/account', 'invalid', 401);
    }
}
