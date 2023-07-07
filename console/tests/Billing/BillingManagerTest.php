<?php

namespace App\Tests\Billing;

use App\Billing\BillingManager;
use App\Bridge\Mollie\MollieInterface;
use App\Entity\Organization;
use App\Form\Billing\Model\UpdateBillingDetailsData;
use App\Repository\OrganizationRepository;
use App\Util\Address;
use Mollie\Api\Resources\Customer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BillingManagerTest extends KernelTestCase
{
    public function providePersistMollieCustomer()
    {
        yield 'existing-customer' => [
            'orgaUuid' => 'cbeb774c-284c-43e3-923a-5a2388340f91',
            'mollieId' => 'cst_DKnSArGRCm',
            'details' => [
                'name' => 'Updated name',
                'email' => 'updated@email.com',
                'streetLine1' => 'Updated streetLine1',
                'streetLine2' => 'Updated streetLine2',
                'postalCode' => 'Updated postalCode',
                'city' => 'Updated city',
                'country' => 'GB',
            ],
        ];

        yield 'new-customer' => [
            'orgaUuid' => '219025aa-7fe2-4385-ad8f-31f386720d10',
            'mollieId' => null,
            'details' => [
                'name' => 'Created name',
                'email' => 'created@email.com',
                'streetLine1' => 'Created streetLine1',
                'streetLine2' => 'Created streetLine2',
                'postalCode' => 'Created postalCode',
                'city' => 'Created city',
                'country' => 'DE',
            ],
        ];
    }

    /**
     * @dataProvider providePersistMollieCustomer
     */
    public function testPersistMollieCustomer(string $orgaUuid, ?string $mollieId, array $details)
    {
        self::bootKernel();

        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneByUuid($orgaUuid);
        $this->assertInstanceOf(Organization::class, $orga);
        $this->assertSame($mollieId, $orga->getMollieCustomerId());

        // Apply changes
        $orga->applyBillingDetailsUpdate(UpdateBillingDetailsData::createFromArray($details));

        // Try persisting it
        static::getContainer()->get(BillingManager::class)->persistMollieCustomer($orga);

        // Ensure the customer didn't change
        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneByUuid($orgaUuid);

        if ($mollieId) {
            // Existing customer: ID shouldn't change
            $this->assertSame($mollieId, $orga->getMollieCustomerId());
        } else {
            // New customer: an ID should have been assigned
            $this->assertNotNull($mollieId = $orga->getMollieCustomerId());
        }

        // Check database data
        $this->assertSame($details['name'], $orga->getBillingName());
        $this->assertSame($details['email'], $orga->getBillingEmail());
        $this->assertSame($details['streetLine1'], $orga->getBillingAddressStreetLine1());
        $this->assertSame($details['streetLine2'], $orga->getBillingAddressStreetLine2());
        $this->assertSame($details['postalCode'], $orga->getBillingAddressPostalCode());
        $this->assertSame(Address::formatCityName($details['city']), $orga->getBillingAddressCity());
        $this->assertSame($details['country'], $orga->getBillingAddressCountry());

        // Check Mollie data
        /** @var Customer $customer */
        $customer = static::getContainer()->get(MollieInterface::class)->customers[$mollieId];

        $this->assertSame($orgaUuid, $customer->metadata->uuid);
        $this->assertSame($details['name'], $customer->name);
        $this->assertSame($details['email'], $customer->email);
        $this->assertSame($details['streetLine1'], $customer->metadata->streetLine1);
        $this->assertSame($details['streetLine2'], $customer->metadata->streetLine2);
        $this->assertSame($details['postalCode'], $customer->metadata->postalCode);
        $this->assertSame(Address::formatCityName($details['city']), $customer->metadata->city);
        $this->assertSame($details['country'], $customer->metadata->country);
    }
}
