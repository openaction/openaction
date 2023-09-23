<?php

namespace App\Tests\Controller\Api\Website;

use App\Tests\ApiTestCase;

class DocumentControllerTest extends ApiTestCase
{
    public function testList()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/documents');
        $this->assertCount(2, $result['data']);
        $this->assertArrayHasKey('created_at', $result['data'][0]);

        // Test mapping and weight
        $this->assertApiResponse($result, [
            'data' => [
                [
                    '_resource' => 'Document',
                    '_links' => [
                        'self' => 'http://localhost/api/website/documents/3XQBlNNIxJqs18qwKppaTx',
                    ],
                    'id' => '3XQBlNNIxJqs18qwKppaTx',
                    'name' => 'programme.pdf',
                    'file' => 'http://localhost/serve/programme.pdf',
                ],
                [
                    '_resource' => 'Document',
                    '_links' => [
                        'self' => 'http://localhost/api/website/documents/74iRs8hFworgB8paLpySeQ',
                    ],
                    'id' => '74iRs8hFworgB8paLpySeQ',
                    'name' => 'les-couts-de-la-campagne.pdf',
                    'file' => 'http://localhost/serve/les-couts-de-la-campagne.pdf',
                ],
            ],
        ]);
    }

    public function testListNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/documents', null, 401);
    }

    public function testListInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/documents', 'invalid', 401);
    }

    public function testView()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/documents/74iRs8hFworgB8paLpySeQ');
        $this->assertApiResponse($result, [
            '_resource' => 'Document',
            '_links' => [
                'self' => 'http://localhost/api/website/documents/74iRs8hFworgB8paLpySeQ',
            ],
            'id' => '74iRs8hFworgB8paLpySeQ',
            'name' => 'les-couts-de-la-campagne.pdf',
            'file' => 'http://localhost/serve/les-couts-de-la-campagne.pdf',
        ]);
    }

    public function testViewNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/documents/74iRs8hFworgB8paLpySeQ', null, 401);
    }

    public function testViewInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/documents/74iRs8hFworgB8paLpySeQ', 'invalid', 401);
    }

    public function testViewNotFound()
    {
        $client = self::createClient();

        $this->apiRequest($client, 'GET', '/api/website/documents/invalid', self::EXAMPLECO_TOKEN, 404);
        $this->apiRequest($client, 'GET', '/api/website/documents/7rnedzqzqk0hv5ktdm3a1m', self::EXAMPLECO_TOKEN, 404);
    }
}
