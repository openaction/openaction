<?php

namespace App\Tests\Community;

use App\Community\ContactLocator;
use App\Entity\Community\Contact;
use App\Entity\Organization;
use App\Form\Community\Model\ContactData;
use App\Repository\AreaRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContactLocatorTest extends KernelTestCase
{
    public function provideFindContactArea()
    {
        yield ['country' => null, 'zipCode' => null, 'expected' => null];
        yield ['country' => '36778547219895752', 'zipCode' => null, 'expected' => 'France'];
        yield ['country' => '36778547219895752', 'zipCode' => '92110', 'expected' => '92110'];
        yield ['country' => '36778547219895752', 'zipCode' => '92 110', 'expected' => '92110'];
        yield ['country' => '36778547219895752', 'zipCode' => ' 92 110 ', 'expected' => '92110'];
    }

    /**
     * @dataProvider provideFindContactArea
     */
    public function testFindContactArea(?string $country, ?string $zipCode, ?string $expectedAreaName)
    {
        self::bootKernel();

        $organization = new Organization('Citipo');

        $data = new ContactData($organization);
        $data->addressCountry = $country ? static::getContainer()->get(AreaRepository::class)->find($country) : null;
        $data->addressZipCode = $zipCode ?: null;

        $contact = new Contact($organization, 'example@citipo.com');
        $contact->applyDataUpdate($data);

        $area = static::getContainer()->get(ContactLocator::class)->findContactArea($contact);

        if (!$expectedAreaName) {
            $this->assertNull($area);
        } else {
            $this->assertSame($expectedAreaName, $area->getName());
        }
    }
}
