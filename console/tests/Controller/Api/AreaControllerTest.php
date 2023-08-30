<?php

namespace App\Tests\Controller\Api;

use App\Repository\AreaRepository;
use App\Tests\ApiRequestBuilder;
use App\Tests\ApiTestCase;

class AreaControllerTest extends ApiTestCase
{
    public function testGetCountries()
    {
        $data = $this->createApiRequest('GET', '/api/areas/countries')->toArray();

        $areas = static::getContainer()->get(AreaRepository::class)->findAllCountries();

        $areaArray = [];
        foreach ($areas as $key => $area) {
            $areaArray['data'][$key]['_resource'] = 'Area';
            $areaArray['data'][$key]['name'] = $area->getName();
            $areaArray['data'][$key]['id'] = $area->getId();
            $areaArray['data'][$key]['parentId'] = null;
            $areaArray['data'][$key]['type'] = 'country';
            $areaArray['data'][$key]['code'] = $area->getCode();
        }

        $this->assertApiResponse($data, $areaArray);
    }

    public function testGetCountriesNoToken()
    {
        $this->createApiRequest('GET', '/api/areas/countries')->withApiToken(null)->send();
        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetCountriesInvalidToken()
    {
        $this->createApiRequest('GET', '/api/areas/countries')->withApiToken('invalid')->send();
        $this->assertResponseStatusCodeSame(401);
    }

    public function provideValidation()
    {
        yield 'valid_country_name' => [
            'country' => 'France',
            'zipCode' => '92110',
            'expectedStatus' => 'ok',
        ];

        yield 'valid_country_code' => [
            'country' => 'FR',
            'zipCode' => '92110',
            'expectedStatus' => 'ok',
        ];

        yield 'valid_zip_code_spaced' => [
            'country' => 'fr',
            'zipCode' => '92 110 ',
            'expectedStatus' => 'ok',
        ];

        yield 'invalid_country' => [
            'country' => 'invalid',
            'zipCode' => '92110',
            'expectedStatus' => 'country_not_found',
        ];

        yield 'invalid_zip_code' => [
            'country' => 'FR',
            'zipCode' => 'invalid',
            'expectedStatus' => 'zip_code_not_found',
        ];
    }

    /**
     * @dataProvider provideValidation
     */
    public function testValidation(string $country, string $zipCode, ?string $expectedStatus)
    {
        $data = $this->createApiRequest('GET', '/api/areas/validate/'.$country.'/'.$zipCode)
            ->withApiToken(ApiRequestBuilder::TOKEN_CITIPO)
            ->toArray()
        ;

        $this->assertResponseStatusCodeSame(200);
        $this->assertSame('AreaValidation', $data['_resource']);
        $this->assertSame($expectedStatus, $data['status']);
    }

    public function testValidationNoToken()
    {
        $this->createApiRequest('GET', '/api/areas/validate/FR/92110')->withApiToken(null)->send();
        $this->assertResponseStatusCodeSame(401);
    }

    public function testValidationInvalidToken()
    {
        $this->createApiRequest('GET', '/api/areas/validate/FR/92110')->withApiToken('invalid')->send();
        $this->assertResponseStatusCodeSame(401);
    }
}
