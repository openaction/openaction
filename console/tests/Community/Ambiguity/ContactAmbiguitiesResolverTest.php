<?php

namespace App\Tests\Community\Ambiguity;

use App\Community\Ambiguity\ContactAmbiguitiesResolver;
use App\Entity\Community\Ambiguity;
use App\Entity\Community\Contact;
use App\Entity\Organization;
use App\Repository\Community\AmbiguityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContactAmbiguitiesResolverTest extends KernelTestCase
{
    private const ACME_UUID = 'cbeb774c-284c-43e3-923a-5a2388340f91';
    private const CITIPO_UUID = '219025aa-7fe2-4385-ad8f-31f386720d10';

    private EntityManagerInterface $em;
    private ContactAmbiguitiesResolver $resolver;

    public function setUp(): void
    {
        self::bootKernel();

        $this->em = self::getContainer()->get(EntityManagerInterface::class);
        $this->resolver = self::getContainer()->get(ContactAmbiguitiesResolver::class);
    }

    public function testFullNameAmbiguitySameOrganization()
    {
        $orga = $this->findOrga(self::ACME_UUID);

        $contactA = $this->createContact($orga, [
            'email' => 'contactA@gmail.com',
            'profileFirstName' => 'Titouan',
            'profileLastName' => 'Galopin',
            'createdAt' => new \DateTime('-2 days'),
        ]);

        $this->createContact($orga, [
            'email' => 'contactB@gmail.com',
            'profileFirstName' => 'Adrien',
            'profileLastName' => 'Duguet',
        ]);

        $contactC = $this->createContact($orga, [
            'email' => 'contactC@gmail.com',
            'profileFirstName' => 'Titouan',
            'profileLastName' => 'GALOPIN',
            'createdAt' => new \DateTime('-3 days'),
        ]);

        $this->assertSame(
            // Orga ID        Oldest ID           Newest ID
            [[$orga->getId(), $contactC->getId(), $contactA->getId()]],
            $this->resolver->resolveAmbiguities($orga)
        );
    }

    public function testFullNameAmbiguityDifferentOrganization()
    {
        $acme = $this->findOrga(self::ACME_UUID);
        $citipo = $this->findOrga(self::CITIPO_UUID);

        $this->createContact($acme, [
            'email' => 'contactA@gmail.com',
            'profileFirstName' => 'Titouan',
            'profileLastName' => 'Galopin',
            'createdAt' => new \DateTime('-2 days'),
        ]);

        $this->createContact($citipo, [
            'email' => 'contactC@gmail.com',
            'profileFirstName' => 'Titouan',
            'profileLastName' => 'GALOPIN',
            'createdAt' => new \DateTime('-3 days'),
        ]);

        $this->assertEmpty($this->resolver->resolveAmbiguities($acme));
    }

    public function testParsedPhoneAmbiguitySameOrganization()
    {
        $orga = $this->findOrga(self::ACME_UUID);

        $contactA = $this->createContact($orga, [
            'email' => 'contactA@gmail.com',
            'contactPhone' => '+33606060606',
            'createdAt' => new \DateTime('-2 days'),
        ]);

        $this->createContact($orga, [
            'email' => 'contactB@gmail.com',
            'contactPhone' => '+33707070707',
        ]);

        $contactC = $this->createContact($orga, [
            'email' => 'contactC@gmail.com',
            'contactPhone' => '+33606060606',
            'createdAt' => new \DateTime('-3 days'),
        ]);

        $this->assertSame(
            // Orga ID        Oldest ID           Newest ID
            [[$orga->getId(), $contactC->getId(), $contactA->getId()]],
            $this->resolver->resolveAmbiguities($orga)
        );
    }

    public function testParsedPhoneAmbiguityDifferentOrganization()
    {
        $acme = $this->findOrga(self::ACME_UUID);
        $citipo = $this->findOrga(self::CITIPO_UUID);

        $this->createContact($acme, [
            'email' => 'contactA@gmail.com',
            'contactPhone' => '+33606060606',
            'createdAt' => new \DateTime('-2 days'),
        ]);

        $this->createContact($citipo, [
            'email' => 'contactC@gmail.com',
            'contactPhone' => '+33606060606',
            'createdAt' => new \DateTime('-3 days'),
        ]);

        $this->assertEmpty($this->resolver->resolveAmbiguities($acme));
    }

    public function testFullResolving()
    {
        $orga = $this->findOrga(self::ACME_UUID);

        $contactA = $this->createContact($orga, [
            'email' => 'contactA@gmail.com',
            'profileFirstName' => 'Titouan',
            'profileLastName' => 'Galopin',
            'createdAt' => new \DateTime('-2 days'),
        ]);

        $contactC = $this->createContact($orga, [
            'email' => 'contactC@gmail.com',
            'profileFirstName' => 'Titouan',
            'profileLastName' => 'GALOPIN',
            'createdAt' => new \DateTime('-3 days'),
        ]);

        $ambiguities = $this->resolver->resolveAmbiguities();

        // The ambiguity coming from fixtures should be here
        $this->assertCount(2, $ambiguities);

        $this->assertSame(
            // Orga ID        Oldest ID           Newest ID
            [$orga->getId(), $contactC->getId(), $contactA->getId()],
            $ambiguities[0]
        );
    }

    public function testPersist()
    {
        $orga = $this->findOrga(self::ACME_UUID);

        $contactA = $this->createContact($orga, [
            'email' => 'contactA@gmail.com',
            'contactPhone' => '+33606060606',
            'createdAt' => new \DateTime('-2 days'),
        ]);

        $contactC = $this->createContact($orga, [
            'email' => 'contactC@gmail.com',
            'contactPhone' => '+33606060606',
            'createdAt' => new \DateTime('-3 days'),
        ]);

        $this->resolver->persistResolvedAmbiguities($this->resolver->resolveAmbiguities($orga), $orga);

        /** @var AmbiguityRepository $repo */
        $repo = $this->em->getRepository(Ambiguity::class);

        $this->assertSame(1, $repo->count(['organization' => $orga]));
        $this->assertInstanceOf(Ambiguity::class, $ambiguity = $repo->findOneBy([
            'organization' => $orga,
            'oldest' => $contactC,
            'newest' => $contactA,
        ]));
        $this->assertNull($ambiguity->getIgnoredAt());
    }

    private function createContact(Organization $orga, array $data): Contact
    {
        $this->em->persist($contact = Contact::createFixture(array_merge($data, ['orga' => $orga])));
        $this->em->flush();

        return $contact;
    }

    private function findOrga(string $uuid): Organization
    {
        return $this->em->getRepository(Organization::class)->findOneBy(['uuid' => $uuid]);
    }
}
