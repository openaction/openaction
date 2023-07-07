<?php

namespace App\Tests\Controller\Console\Organization;

use App\Bridge\Mollie\MockMollie;
use App\Bridge\Mollie\MollieInterface;
use App\Entity\Organization;
use App\Repository\OrganizationRepository;
use App\Tests\WebTestCase;
use Mollie\Api\Resources\Customer;
use Symfony\Component\HttpFoundation\Response;

class BillingControllerTest extends WebTestCase
{
    public function testUpdateDetailsCreateMollieCustomer()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/billing/details');
        $this->assertResponseIsSuccessful();

        $client->submit($crawler->selectButton('Save')->form(), [
            'update_billing_details[name]' => 'CPGT SAS',
            'update_billing_details[email]' => 'billing@citipo.com',
            'update_billing_details[streetLine1]' => '48 Rue de Ponthieu',
            'update_billing_details[streetLine2]' => 'Etage 1',
            'update_billing_details[postalCode]' => '75008',
            'update_billing_details[city]' => 'PARIS',
            'update_billing_details[country]' => 'FR',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // Check customer created with Mollie
        /** @var MockMollie $mollie */
        $mollie = static::getContainer()->get(MollieInterface::class);
        $this->assertArrayHasKey('cst_2c267514', $mollie->customers);

        /** @var Customer $customer */
        $customer = $mollie->customers['cst_2c267514'];
        $this->assertSame('CPGT SAS', $customer->name);
        $this->assertSame('billing@citipo.com', $customer->email);
        $this->assertEquals(
            (object) [
                'uuid' => self::ORGA_CITIPO_UUID,
                'streetLine1' => '48 Rue de Ponthieu',
                'streetLine2' => 'Etage 1',
                'postalCode' => '75008',
                'city' => 'PARIS',
                'country' => 'FR',
            ],
            $customer->metadata
        );

        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneByUuid(self::ORGA_CITIPO_UUID);
        $this->assertSame('CPGT SAS', $orga->getBillingName());
        $this->assertSame('billing@citipo.com', $orga->getBillingEmail());
        $this->assertSame('48 Rue de Ponthieu', $orga->getBillingAddressStreetLine1());
        $this->assertSame('Etage 1', $orga->getBillingAddressStreetLine2());
        $this->assertSame('75008', $orga->getBillingAddressPostalCode());
        $this->assertSame('PARIS', $orga->getBillingAddressCity());
        $this->assertSame('FR', $orga->getBillingAddressCountry());
    }

    public function testUpdateDetailsUpdateMollieCustomer()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_ACME_UUID.'/billing/details');
        $this->assertResponseIsSuccessful();

        $client->submit($crawler->selectButton('Save')->form(), [
            'update_billing_details[name]' => 'CPGT SAS',
            'update_billing_details[email]' => 'billing@citipo.com',
            'update_billing_details[streetLine1]' => '48 Rue de Ponthieu',
            'update_billing_details[streetLine2]' => 'Etage 1',
            'update_billing_details[postalCode]' => '75008',
            'update_billing_details[city]' => 'Paris',
            'update_billing_details[country]' => 'FR',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // Check customer created with Mollie
        /** @var MockMollie $mollie */
        $mollie = static::getContainer()->get(MollieInterface::class);
        $this->assertArrayHasKey('cst_DKnSArGRCm', $mollie->customers);

        /** @var Customer $customer */
        $customer = $mollie->customers['cst_DKnSArGRCm'];
        $this->assertSame('CPGT SAS', $customer->name);
        $this->assertSame('billing@citipo.com', $customer->email);
        $this->assertEquals(
            (object) [
                'uuid' => self::ORGA_ACME_UUID,
                'streetLine1' => '48 Rue de Ponthieu',
                'streetLine2' => 'Etage 1',
                'postalCode' => '75008',
                'city' => 'PARIS',
                'country' => 'FR',
            ],
            $customer->metadata
        );

        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneByUuid(self::ORGA_ACME_UUID);
        $this->assertSame('CPGT SAS', $orga->getBillingName());
        $this->assertSame('billing@citipo.com', $orga->getBillingEmail());
        $this->assertSame('48 Rue de Ponthieu', $orga->getBillingAddressStreetLine1());
        $this->assertSame('Etage 1', $orga->getBillingAddressStreetLine2());
        $this->assertSame('75008', $orga->getBillingAddressPostalCode());
        $this->assertSame('PARIS', $orga->getBillingAddressCity());
        $this->assertSame('FR', $orga->getBillingAddressCountry());
    }

    public function testHistory()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/billing/history');
        $this->assertResponseIsSuccessful();
    }

    public function testPay()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/organization/'.self::ORGA_ACME_UUID.'/billing/7698f242-0a05-496c-b542-34236e9de12c/pay');
        $this->assertResponseRedirects('https://mollie.com/checkout');
    }

    public function testProcessed()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/organization/'.self::ORGA_ACME_UUID.'/billing/7698f242-0a05-496c-b542-34236e9de12c/processed');
        $this->assertResponseIsSuccessful();
    }

    public function testDownload()
    {
        $client = static::createClient();
        $this->authenticate($client);

        // Create CDN file
        static::getContainer()->get('cdn.storage')->write(
            'invoice.pdf',
            file_get_contents(__DIR__.'/../../../Fixtures/upload/document.pdf')
        );

        // Check file is downloaded properly
        $client->request('GET', '/console/organization/'.self::ORGA_ACME_UUID.'/billing/b1e80c11-ca03-4e11-858a-dc00b05c5527/download');
        $this->assertResponseIsSuccessful();
        $this->assertSame('application/pdf', $client->getInternalResponse()->getHeader('Content-Type'));
        $this->assertStringStartsWith('attachment; filename=citipo-invoice-156-', $client->getInternalResponse()->getHeader('Content-Disposition'));
        $this->assertStringEqualsFile(__DIR__.'/../../../Fixtures/upload/document.pdf', $client->getInternalResponse()->getContent());
    }
}
