<?php

namespace App\Tests;

use App\Util\Json;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

abstract class ApiTestCase extends WebTestCase
{
    protected const EXAMPLECO_TOKEN = '41d7821176ed9079640650922e1290aba97b949362339a7ed5539f0d5b9f21ba';
    protected const EXAMPLECO_ADMIN_TOKEN = 'admin_6d06eec96e8c615b76ccf3b9166b174b4e2f59804f8b97773532957b4acf8691';
    protected const CITIPO_TOKEN = '748ea240b01970d6c9de708de7602e613adb4dd02aa084435088c8c5f806d9ad';
    protected const CITIPO_ORG = '219025aa-7fe2-4385-ad8f-31f386720d10';
    protected const ACME_TOKEN = '31cf08f5e0354198a3b26b5b08f59a4ed871cbaec6e4eb8b158fab57a7193b7a';
    protected const ACME_ADMIN_TOKEN = 'admin_31cf08f5e0354198a3b26b5b08f59a4ed871cbaec6e4eb8b158fab57a7193b7a';
    protected const ACME_ORG = 'cbeb774c-284c-43e3-923a-5a2388340f91';

    protected function createApiRequest(string $method, string $endpoint, ?KernelBrowser $client = null): ApiRequestBuilder
    {
        return new ApiRequestBuilder($client ?: self::createClient(), $method, $endpoint);
    }

    protected function apiRequest(
        KernelBrowser $client,
        string $method,
        string $endpoint,
        ?string $token = self::EXAMPLECO_TOKEN,
        int $expectedStatusCode = 200,
        ?string $content = null,
        array $parameters = [],
    ): array {
        $server = ['HTTP_ACCEPT' => 'application/ld+json'];
        if ($token) {
            $server['HTTP_AUTHORIZATION'] = 'Bearer '.$token;
        }

        $client->request($method, $endpoint, $parameters, [], $server, $content);
        $this->assertResponseStatusCodeSame($expectedStatusCode);

        $response = $client->getResponse();
        if (200 === $expectedStatusCode) {
            $this->assertJson($response->getContent());
        }

        try {
            return Json::decode($response->getContent());
        } catch (\Exception) {
            return [];
        }
    }
}
