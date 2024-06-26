<?php

namespace App\Tests\Community\Ambiguity;

use App\Community\Ambiguity\ContactMerger;
use App\Entity\Analytics\Community\ContactCreation;
use App\Entity\Area;
use App\Entity\Community\Ambiguity;
use App\Entity\Community\Contact;
use App\Entity\Community\EmailingCampaignMessage;
use App\Entity\Community\PhoningCampaignCall;
use App\Entity\Community\PhoningCampaignTarget;
use App\Entity\Community\TextingCampaignMessage;
use App\Entity\Organization;
use App\Entity\Website\FormAnswer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContactMergerTest extends KernelTestCase
{
    private const CITIPO_ORG = '219025aa-7fe2-4385-ad8f-31f386720d10';

    private EntityManagerInterface $em;
    private ContactMerger $contactMerger;

    public function setUp(): void
    {
        $this->markTestSkipped();

        self::bootKernel();

        $this->em = self::getContainer()->get(EntityManagerInterface::class);
        $this->contactMerger = self::getContainer()->get(ContactMerger::class);
    }

    public function provideMergeEmail()
    {
        yield [
            ['email' => 'oldest1@citipo.email', 'contactAdditionalEmails' => []],
            ['email' => 'newest2@citipo.email'],
        ];

        yield [
            ['email' => 'oldest3@citipo.email', 'contactAdditionalEmails' => ['oldest.additional@citipo.email']],
            ['email' => 'newest4@citipo.email'],
        ];
    }

    /**
     * @dataProvider provideMergeEmail
     */
    public function testMergeEmail(array $oldest, array $newest)
    {
        [$ambiguity, $oldestEntity] = $this->createContactsAndAmbiguity($oldest, $newest, static::CITIPO_ORG);

        $this->contactMerger->merge($ambiguity, 'newest');

        $quantityAdditionalEmails = count($oldest['contactAdditionalEmails']) + 1;
        $additionalEmails = $oldestEntity->getContactAdditionalEmails();

        $this->assertCount($quantityAdditionalEmails, $additionalEmails);

        $email = array_pop($additionalEmails);
        $this->assertSame($oldest['email'], $email);
        $this->assertSame($newest['email'], $oldestEntity->getEmail());
    }

    public function provideMergeSettings()
    {
        yield [
            ['email' => 'oldest5@citipo.email'],
            [
                'email' => 'newest6@citipo.email',
                'settingsReceiveNewsletters' => false,
                'settingsReceiveSms' => false,
                'settingsReceiveCalls' => false,
            ],
        ];

        yield [
            ['email' => 'oldest7@citipo.email'],
            [
                'email' => 'newest8@citipo.email',
                'settingsReceiveNewsletters' => true,
                'settingsReceiveSms' => true,
                'settingsReceiveCalls' => true,
            ],
        ];
    }

    /**
     * @dataProvider provideMergeSettings
     */
    public function testMergeSettings(array $oldest, array $newest)
    {
        [$ambiguity, $oldestEntity] = $this->createContactsAndAmbiguity($oldest, $newest, static::CITIPO_ORG);

        $this->contactMerger->merge($ambiguity, 'newest');

        $this->assertSame($newest['settingsReceiveNewsletters'], $oldestEntity->hasSettingsReceiveNewsletters());
        $this->assertSame($newest['settingsReceiveSms'], $oldestEntity->hasSettingsReceiveSms());
        $this->assertSame($newest['settingsReceiveCalls'], $oldestEntity->hasSettingsReceiveCalls());
    }

    public function provideMergeArea()
    {
        yield [
            ['email' => 'oldest9@citipo.email', 'area' => 36778547219895752],
            ['email' => 'newest10@citipo.email', 'area' => 64795327863947811],
            64795327863947811,
        ];

        yield [
            ['email' => 'oldest11@citipo.email', 'area' => 39389989938296926],
            ['email' => 'newest12@citipo.email', 'area' => 36778547219895752],
            39389989938296926,
        ];
    }

    /**
     * @dataProvider provideMergeArea
     */
    public function testMergeArea(array $oldest, array $newest, int $area)
    {
        $oldest['area'] = $this->em->getRepository(Area::class)->find($oldest['area']);
        $newest['area'] = $this->em->getRepository(Area::class)->find($newest['area']);
        [$ambiguity, $oldestEntity] = $this->createContactsAndAmbiguity($oldest, $newest, static::CITIPO_ORG);

        $this->contactMerger->merge($ambiguity, 'newest');

        $this->assertSame($area, $oldestEntity->getArea()->getId());
    }

    public function provideMergeTags()
    {
        yield [
            '20e51b91-bdec-495d-854d-85d6e74fc75e',
            'e90c2a1c-9504-497d-8354-c9dabc1ff7a2',
            ['ContainsTagInside', 'ExampleTag', 'StartWithTag'],
        ];
    }

    /**
     * @dataProvider provideMergeTags
     */
    public function testMergeTags(string $oldestUuid, string $newestUuid, array $expectedTagsOldest)
    {
        $oldest = $this->em->getRepository(Contact::class)->findOneBy(['uuid' => $oldestUuid]);
        $newest = $this->em->getRepository(Contact::class)->findOneBy(['uuid' => $newestUuid]);

        $ambiguity = $this->createAmbiguity($oldest, $newest, static::CITIPO_ORG, true);

        $this->contactMerger->merge($ambiguity, 'newest');

        $newest = $this->em->getRepository(Contact::class)->findOneBy(['uuid' => $newestUuid]);

        $this->assertSame($expectedTagsOldest, $oldest->getMetadataTagsNames());
        $this->assertNull($newest);
    }

    public function provideMergeComment()
    {
        yield [
            ['email' => 'oldest13@citipo.email', 'metadataComment' => 'Comment 1'],
            ['email' => 'newest14@citipo.email', 'metadataComment' => 'Comment 2'],
        ];
    }

    /**
     * @dataProvider provideMergeComment
     */
    public function testMergeComment(array $oldest, array $newest)
    {
        [$ambiguity, $oldestEntity] = $this->createContactsAndAmbiguity($oldest, $newest, static::CITIPO_ORG);

        $this->contactMerger->merge($ambiguity, 'newest');

        $comment = "{$oldest['metadataComment']}\n{$newest['metadataComment']}";
        $this->assertSame($comment, $oldestEntity->getMetadataComment());
    }

    public function provideMergeCustomFields()
    {
        yield [
            ['email' => 'oldest15@citipo.email', 'metadataCustomFields' => ['hello' => 'moto', 'key' => 'value']],
            ['email' => 'newest16@citipo.email', 'metadataCustomFields' => ['hello' => 'merge_moto']],
        ];
    }

    /**
     * @dataProvider provideMergeCustomFields
     */
    public function testMergeCustomFields(array $oldest, array $newest)
    {
        [$ambiguity, $oldestEntity] = $this->createContactsAndAmbiguity($oldest, $newest, static::CITIPO_ORG);

        $this->contactMerger->merge($ambiguity, 'newest');

        $this->assertSame('value', $oldestEntity->getMetadataCustomFields()['key']);
        $this->assertSame('merge_moto', $oldestEntity->getMetadataCustomFields()['hello']);
    }

    private function createContactsAndAmbiguity(array $oldestData, array $newestData, string $orga)
    {
        $repo = $this->em->getRepository(Contact::class);
        $orga = $this->em->getRepository(Organization::class)->findOneBy(['uuid' => $orga]);

        if (isset($oldestData['email']) && $c = $repo->findOneBy(['email' => $oldestData['email']])) {
            $this->em->remove($c);
            $this->em->flush();
        }

        if (isset($newestData['email']) && $c = $repo->findOneBy(['email' => $newestData['email']])) {
            $this->em->remove($c);
            $this->em->flush();
        }

        $oldest = Contact::createFixture($oldestData + ['orga' => $orga]);
        $newest = Contact::createFixture($newestData + ['orga' => $orga]);

        $this->em->persist($oldest);
        $this->em->persist($newest);
        $ambiguity = $this->createAmbiguity($oldest, $newest, $orga);

        $this->em->flush();

        return [$ambiguity, $oldest, $newest];
    }

    public function provideMergeOtherFields()
    {
        yield [
            '20e51b91-bdec-495d-854d-85d6e74fc75e',
            '1f1b67f0-f77d-425c-9195-861b33f19695',
        ];
    }

    /**
     * @dataProvider provideMergeOtherFields
     */
    public function testMergeOtherFields(string $oldestUuid, string $newestUuid)
    {
        $oldest = $this->em->getRepository(Contact::class)->findOneBy(['uuid' => $oldestUuid]);
        $newest = $this->em->getRepository(Contact::class)->findOneBy(['uuid' => $newestUuid]);

        $ambiguity = $this->createAmbiguity($oldest, $newest, static::CITIPO_ORG, true);

        $oldest = $ambiguity->getOldest();

        $this->assertSame('+33 7 57 59 46 25', $oldest->getContactPhone());
        $this->assertSame('Olivie', $oldest->getProfileFirstName());
        $this->assertSame('Gregoire', $oldest->getProfileLastName());
        $this->assertSame('olivie.gregoire', $oldest->getSocialFacebook());
        $this->assertSame('@golivie92', $oldest->getSocialTwitter());
        $this->assertNull($oldest->getSocialLinkedIn());
        $this->assertNull($oldest->getSocialWhatsapp());

        $this->contactMerger->merge($ambiguity, 'newest');

        $this->assertSame('+33 2 76 86 36 41', $oldest->getContactPhone());
        $this->assertSame('Apolline', $oldest->getProfileFirstName());
        $this->assertSame('Mousseau', $oldest->getProfileLastName());
        $this->assertSame('apolline.mousseau', $oldest->getSocialFacebook());
        $this->assertSame('@amousseau', $oldest->getSocialTwitter());
        $this->assertSame('apollinemousseau', $oldest->getSocialLinkedIn());
        $this->assertSame('+33601020304', $oldest->getSocialWhatsapp());
    }

    public function provideMergeUpdateRelationships()
    {
        yield [
            ['email' => 'newest@citipo.email'],
            '20e51b91-bdec-495d-854d-85d6e74fc75e',
            1,
            1,
            0,
            1,
            0,
            0,
        ];

        yield [
            ['email' => 'newest@citipo.email'],
            '75d245dd-c844-4ee7-8f12-a3d611a308b6',
            0,
            1,
            0,
            1,
            1,
            1,
        ];
    }

    /**
     * @dataProvider provideMergeUpdateRelationships
     */
    public function testMergeUpdateRelationships(
        array $oldest,
        string $newestUuid,
        int $countContactCreation,
        int $countEmailingCampaignMessage,
        int $countPhoningCampaignCall,
        int $countPhoningCampaignTarget,
        int $countTextingCampaignMessage,
        int $countFormAnswer,
    ) {
        $orga = $this->em->getRepository(Organization::class)->findOneBy(['uuid' => static::CITIPO_ORG]);
        $newest = $this->em->getRepository(Contact::class)->findOneBy(['uuid' => $newestUuid]);
        $oldest = Contact::createFixture($oldest + ['orga' => $orga]);

        $this->em->persist($oldest);
        $ambiguity = $this->createAmbiguity($oldest, $newest, static::CITIPO_ORG, true);

        $this->assertSame($countContactCreation, $this->em->getRepository(ContactCreation::class)->count(['contact' => $newest]));
        $this->assertSame($countEmailingCampaignMessage, $this->em->getRepository(EmailingCampaignMessage::class)->count(['contact' => $newest]));
        $this->assertSame($countPhoningCampaignCall, $this->em->getRepository(PhoningCampaignCall::class)->count(['author' => $newest]));
        $this->assertSame($countPhoningCampaignTarget, $this->em->getRepository(PhoningCampaignTarget::class)->count(['contact' => $newest]));
        $this->assertSame($countTextingCampaignMessage, $this->em->getRepository(TextingCampaignMessage::class)->count(['contact' => $newest]));
        $this->assertSame($countFormAnswer, $this->em->getRepository(FormAnswer::class)->count(['contact' => $newest]));

        $this->contactMerger->merge($ambiguity, 'newest');

        // check update newest contact
        $this->assertSame(0, $this->em->getRepository(ContactCreation::class)->count(['contact' => $newest]));
        $this->assertSame(0, $this->em->getRepository(EmailingCampaignMessage::class)->count(['contact' => $newest]));
        $this->assertSame(0, $this->em->getRepository(PhoningCampaignCall::class)->count(['author' => $newest]));
        $this->assertSame(0, $this->em->getRepository(PhoningCampaignTarget::class)->count(['contact' => $newest]));
        $this->assertSame(0, $this->em->getRepository(TextingCampaignMessage::class)->count(['contact' => $newest]));
        $this->assertSame(0, $this->em->getRepository(FormAnswer::class)->count(['contact' => $newest]));

        // check update oldest contact
        $this->assertSame(0, $this->em->getRepository(ContactCreation::class)->count(['contact' => $oldest]));
        $this->assertSame($countEmailingCampaignMessage, $this->em->getRepository(EmailingCampaignMessage::class)->count(['contact' => $oldest]));
        $this->assertSame($countPhoningCampaignCall, $this->em->getRepository(PhoningCampaignCall::class)->count(['author' => $oldest]));
        $this->assertSame($countPhoningCampaignTarget, $this->em->getRepository(PhoningCampaignTarget::class)->count(['contact' => $oldest]));
        $this->assertSame($countTextingCampaignMessage, $this->em->getRepository(TextingCampaignMessage::class)->count(['contact' => $oldest]));
        $this->assertSame($countFormAnswer, $this->em->getRepository(FormAnswer::class)->count(['contact' => $oldest]));
    }

    private function createAmbiguity(Contact $oldest, Contact $newest, $orga, bool $flush = false)
    {
        if (!$orga instanceof Organization) {
            $orga = $this->em->getRepository(Organization::class)->findOneBy(['uuid' => $orga]);
        }

        $ambiguity = Ambiguity::createFixture(['oldest' => $oldest, 'newest' => $newest, 'orga' => $orga]);

        $this->em->persist($ambiguity);

        if ($flush) {
            $this->em->flush();
        }

        return $ambiguity;
    }
}
