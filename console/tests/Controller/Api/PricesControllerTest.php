<?php

namespace App\Tests\Controller\Api;

use App\Platform\Prices;
use App\Tests\ApiTestCase;

class PricesControllerTest extends ApiTestCase
{
    public function testGetPrices()
    {
        $this->assertApiResponse($this->createApiRequest('GET', '/api/prices')->toArray(), [
            '_resource' => 'Prices',
            'credits' => [
                'email' => Prices::CREDIT_EMAIL,
                'text' => Prices::CREDIT_TEXT,
            ],
        ]);
    }
}
