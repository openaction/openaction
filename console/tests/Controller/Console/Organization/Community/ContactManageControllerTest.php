<?php

namespace App\Tests\Controller\Console\Organization\Community;

use App\Analytics\Consumer\RefreshContactStatsMessage;
use App\Bridge\Integromat\Consumer\IntegromatWebhookMessage;
use App\Bridge\Quorum\Consumer\QuorumMessage;
use App\Bridge\Sendgrid\Consumer\SendgridMessage;
use App\Entity\Community\Contact;
use App\Entity\Community\Tag;
use App\Entity\Organization;
use App\Repository\Community\ContactRepository;
use App\Repository\Community\EmailAutomationMessageRepository;
use App\Repository\Community\TagRepository;
use App\Repository\OrganizationRepository;
use App\Search\Consumer\RemoveCrmDocumentMessage;
use App\Search\Consumer\UpdateCrmDocumentsMessage;
use App\Tests\WebTestCase;
use App\Util\Json;
use libphonenumber\PhoneNumber;
use SendGrid\Mail\To;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Transport\TransportInterface;

class ContactManageControllerTest extends WebTestCase
{
    public function testCreate()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts/create');
        $this->assertResponseIsSuccessful();
        $this->assertNull(static::getContainer()->get(ContactRepository::class)->findOneBy(['email' => 'created@example.org']));

        $button = $crawler->selectButton('Create');
        $client->submit($button->form(), [
            'contact[email]' => 'created@example.org',
            'contact[picture]' => new UploadedFile(__DIR__.'/../../../../Fixtures/upload/mario.png', 'mario.png'),
            'contact[profileFormalTitle]' => 'profileFormalTitle',
            'contact[profileFirstName]' => 'profileFirstName',
            'contact[profileMiddleName]' => 'profileMiddleName',
            'contact[profileLastName]' => 'profileLastName',
            'contact[profileBirthdate]' => '1990-01-01',
            'contact[profileGender]' => 'female',
            'contact[profileNationality]' => 'FR',
            'contact[profileCompany]' => 'profileCompany',
            'contact[profileJobTitle]' => 'profileJobTitle',
            'contact[contactPhone]' => '+33555555555',
            'contact[contactWorkPhone]' => '+33666666666',
            'contact[socialFacebook]' => 'https://facebook.com/example',
            'contact[socialTwitter]' => 'https://twitter.com/example',
            'contact[socialLinkedIn]' => 'https://linkedin.com/example',
            'contact[socialTelegram]' => 'https://telegram.com/example',
            'contact[socialWhatsapp]' => '0606060606',
            'contact[addressStreetLine1]' => 'addressStreetLine1',
            'contact[addressStreetLine2]' => 'addressStreetLine2',
            'contact[addressZipCode]' => '92110',
            'contact[addressCity]' => 'addressCity',
            'contact[addressCountry]' => '36778547219895752',
            'contact[metadataComment]' => 'metadataComment',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        /** @var Contact $contact */
        $contact = static::getContainer()->get(ContactRepository::class)->findOneBy(['email' => 'created@example.org']);
        $this->assertNotNull($contact->getPicture());
        $this->assertSame('92110', $contact->getArea()->getName());
        $this->assertSame('created@example.org', $contact->getEmail());
        $this->assertSame('profileFormalTitle', $contact->getProfileFormalTitle());
        $this->assertSame('profileFirstName', $contact->getProfileFirstName());
        $this->assertSame('profileMiddleName', $contact->getProfileMiddleName());
        $this->assertSame('profileLastName', $contact->getProfileLastName());
        $this->assertSame('1990-01-01', $contact->getProfileBirthdate()->format('Y-m-d'));
        $this->assertSame('female', $contact->getProfileGender());
        $this->assertSame('FR', $contact->getProfileNationality());
        $this->assertSame('profileCompany', $contact->getProfileCompany());
        $this->assertSame('profileJobTitle', $contact->getProfileJobTitle());
        $this->assertSame('+33 5 55 55 55 55', $contact->getContactPhone());
        $this->assertSame('+33 6 66 66 66 66', $contact->getContactWorkPhone());
        $this->assertInstanceOf(PhoneNumber::class, $contact->getParsedContactPhone());
        $this->assertInstanceOf(PhoneNumber::class, $contact->getParsedContactWorkPhone());
        $this->assertSame('https://facebook.com/example', $contact->getSocialFacebook());
        $this->assertSame('https://twitter.com/example', $contact->getSocialTwitter());
        $this->assertSame('https://linkedin.com/example', $contact->getSocialLinkedIn());
        $this->assertSame('https://telegram.com/example', $contact->getSocialTelegram());
        $this->assertSame('0606060606', $contact->getSocialWhatsapp());
        $this->assertSame('addressStreetLine1', $contact->getAddressStreetLine1());
        $this->assertSame('addressStreetLine2', $contact->getAddressStreetLine2());
        $this->assertSame('92110', $contact->getAddressZipCode());
        $this->assertSame('ADDRESSCITY', $contact->getAddressCity());
        $this->assertSame('France', $contact->getAddressCountry()->getName());
        $this->assertTrue($contact->hasSettingsReceiveNewsletters());
        $this->assertTrue($contact->hasSettingsReceiveSms());
        $this->assertFalse($contact->hasSettingsReceiveCalls());
        $this->assertSame('metadataComment', $contact->getMetadataComment());

        /*
         * Check search engine update
         */

        /** @var TransportInterface $transport */
        $transport = static::getContainer()->get('messenger.transport.async_indexing');
        $this->assertCount(1, $messages = $transport->get());
        $this->assertInstanceOf(UpdateCrmDocumentsMessage::class, $messages[0]->getMessage());

        /*
         * Check integrations and stats refresh
         */

        /** @var TransportInterface $transport */
        $transport = static::getContainer()->get('messenger.transport.async_priority_low');

        $messages = $transport->get();
        $this->assertCount(3, $messages);

        // Integromat
        /* @var IntegromatWebhookMessage $message */
        $this->assertInstanceOf(IntegromatWebhookMessage::class, $message = $messages[0]->getMessage());
        $this->assertSame('https://hook.integromat.com/swme2wu7n735qcmhbeyfj595c2ey2aju', $message->getUrl());

        $expectedPayload = [
            '_resource' => 'Contact',
            'email' => 'created@example.org',
            'isMember' => false,
            'profileFormalTitle' => 'profileFormalTitle',
            'profileFirstName' => 'profileFirstName',
            'profileMiddleName' => 'profileMiddleName',
            'profileLastName' => 'profileLastName',
            'profileBirthdate' => '1990-01-01',
            'profileGender' => 'female',
            'profileNationality' => 'FR',
            'profileCompany' => 'profileCompany',
            'profileJobTitle' => 'profileJobTitle',
            'contactPhone' => '+33 5 55 55 55 55',
            'contactWorkPhone' => '+33 6 66 66 66 66',
            'parsedContactPhone' => '+33 5 55 55 55 55',
            'parsedContactWorkPhone' => '+33 6 66 66 66 66',
            'socialFacebook' => 'https://facebook.com/example',
            'socialTwitter' => 'https://twitter.com/example',
            'socialLinkedIn' => 'https://linkedin.com/example',
            'socialTelegram' => 'https://telegram.com/example',
            'socialWhatsapp' => '0606060606',
            'addressStreetLine1' => 'addressStreetLine1',
            'addressStreetLine2' => 'addressStreetLine2',
            'addressZipCode' => '92110',
            'addressCity' => 'ADDRESSCITY',
            'addressCountry' => 'FR',
            'settingsReceiveNewsletters' => true,
            'settingsReceiveSms' => true,
            'settingsReceiveCalls' => false,
            'settingsByProject' => [],
            'metadataTags' => [],
            'metadataCustomFields' => [],
        ];

        $payload = $message->getPayload();
        $this->assertNotNull($payload['id']);
        $this->assertNotNull($payload['picture']);
        unset($payload['id'], $payload['picture']);

        $this->assertSame($expectedPayload, $payload);

        // Quorum
        $this->assertInstanceOf(QuorumMessage::class, $messages[1]->getMessage());

        // Payload mapping already checked by
        // App\Tests\Controller\Console\Project\Community\ContactControllerTest::testEdit

        $this->assertInstanceOf(RefreshContactStatsMessage::class, $messages[2]->getMessage());

        /*
         * Check automation
         */

        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => '219025aa-7fe2-4385-ad8f-31f386720d10']);

        /** @var EmailAutomationMessageRepository $automationMessageRepo */
        $automationMessageRepo = static::getContainer()->get(EmailAutomationMessageRepository::class);

        /** @var TransportInterface $transport */
        $transport = static::getContainer()->get('messenger.transport.async_emailing');

        // Check credits usage
        $this->assertSame(999999, $orga->getCreditsBalance());

        // Check email automation messages
        $this->assertSame(1, $automationMessageRepo->count(['email' => 'contact@citipo.com']));

        $messages = $transport->get();
        $this->assertCount(1, $messages);

        /** @var SendgridMessage $message */
        $message = $messages[0]->getMessage();
        $this->assertInstanceOf(SendgridMessage::class, $message);

        // Check the mail
        $mail = $message->getMail();
        $this->assertSame('New contact alert', $mail->getGlobalSubject()->getSubject());
        $this->assertSame('contact@citipo.com', $mail->getFrom()->getEmail());
        $this->assertSame('Jacques BAUER', $mail->getFrom()->getName());
        $this->assertCount(1, $contents = $mail->getContents());
        $this->assertStringContainsString('Contact [fullName]', $contents[0]->getValue());

        $to = [];
        foreach ($mail->getPersonalizations() as $personalization) {
            $to[] = array_map(fn (To $to) => $to->getEmail(), $personalization->getTos());
        }

        $to = array_merge(...$to);
        sort($to);

        $this->assertSame(['contact@citipo.com'], $to);
    }

    public function testCannotEditOutsideOrganization()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/organization/682746ea-3e2f-4e5b-983b-6548258a2033/community/contacts/20e51b91-bdec-495d-854d-85d6e74fc75e/edit');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testViewEmailing()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts/e90c2a1c-9504-497d-8354-c9dabc1ff7a2/view');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div:contains("Contact")');
        $this->assertSelectorExists('div:contains("Unsubscribed")');
        $this->assertSelectorExists('div:contains("92110")');
        $this->assertSelectorExists('div:contains("tchalut@yahoo.fr")');
        $this->assertSelectorExists('div:contains("Théodore Chalut")');
        $this->assertSelectorExists('div:contains("Campaign with opens tracking")');
        $this->assertSelectorExists('div:contains("Opened on")');
        $this->assertSelectorExists('div:contains("Never clicked")');
    }

    public function testViewTexting()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/organization/cbeb774c-284c-43e3-923a-5a2388340f91/community/contacts/8d3323fd-e1a9-4eaa-9d4d-714abf1ff238/view');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div:contains("+55 61 99881-2130")');
        $this->assertSelectorExists('div:contains("Received a text")');
        $this->assertSelectorExists('div:contains("Sent campaign")');
    }

    public function testViewFormAnswer()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/organization/cbeb774c-284c-43e3-923a-5a2388340f91/community/contacts/75d245dd-c844-4ee7-8f12-a3d611a308b6/view');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div:contains("Answered a form")');
        $this->assertSelectorExists('div:contains(" Our Sustainable Europe")');
    }

    public function testHistoryEmailing()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts/e90c2a1c-9504-497d-8354-c9dabc1ff7a2/history');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div:contains("Campaign with opens tracking")');
        $this->assertSelectorExists('div:contains("Opened on")');
        $this->assertSelectorExists('div:contains("Never clicked")');
    }

    public function testHistoryTexting()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/organization/cbeb774c-284c-43e3-923a-5a2388340f91/community/contacts/8d3323fd-e1a9-4eaa-9d4d-714abf1ff238/history');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div:contains("Received a text")');
        $this->assertSelectorExists('div:contains("Sent campaign")');
    }

    public function testHistoryFormAnswer()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/organization/cbeb774c-284c-43e3-923a-5a2388340f91/community/contacts/75d245dd-c844-4ee7-8f12-a3d611a308b6/history');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div:contains("Answered a form")');
        $this->assertSelectorExists('div:contains(" Our Sustainable Europe")');
    }

    public function testEdit()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts/20e51b91-bdec-495d-854d-85d6e74fc75e/edit');
        $this->assertResponseIsSuccessful();

        /** @var Contact $contact */
        $contact = static::getContainer()->get(ContactRepository::class)->findOneByUuid('20e51b91-bdec-495d-854d-85d6e74fc75e');
        $this->assertSame('France', $contact->getArea()->getName());
        $this->assertNotNull($contact->getPicture());
        $this->assertSame('olivie.gregoire@gmail.com', $contact->getEmail());
        $this->assertSame(['olivie.gregoire@outlook.com'], $contact->getContactAdditionalEmails());
        $this->assertNull($contact->getProfileFormalTitle());
        $this->assertSame('Olivie', $contact->getProfileFirstName());
        $this->assertNull($contact->getProfileMiddleName());
        $this->assertSame('Gregoire', $contact->getProfileLastName());
        $this->assertNull($contact->getProfileGender());
        $this->assertNull($contact->getProfileNationality());
        $this->assertNull($contact->getProfileBirthdate());
        $this->assertNull($contact->getProfileCompany());
        $this->assertNull($contact->getProfileJobTitle());
        $this->assertSame('+33 7 57 59 46 25', $contact->getContactPhone());
        $this->assertNull($contact->getContactWorkPhone());
        $this->assertSame('olivie.gregoire', $contact->getSocialFacebook());
        $this->assertSame('@golivie92', $contact->getSocialTwitter());
        $this->assertNull($contact->getSocialLinkedIn());
        $this->assertNull($contact->getSocialTelegram());
        $this->assertNull($contact->getSocialWhatsapp());
        $this->assertNull($contact->getAddressStreetLine1());
        $this->assertNull($contact->getAddressStreetLine2());
        $this->assertNull($contact->getAddressZipCode());
        $this->assertNull($contact->getAddressCity());
        $this->assertNull($contact->getAddressCountry());
        $this->assertTrue($contact->hasSettingsReceiveNewsletters());
        $this->assertTrue($contact->hasSettingsReceiveSms());
        $this->assertTrue($contact->hasSettingsReceiveCalls());
        $this->assertNull($contact->getMetadataComment());
        $this->assertSame('ExampleTag, StartWithTag', $contact->getMetadataTagsList());

        $previousPicturePathname = $contact->getPicture()->getPathname();

        /** @var Tag $tag */
        $tag = static::getContainer()->get(TagRepository::class)->findOneBy(['slug' => 'startwithtag']);
        $tagsIds = [['id' => $tag->getId(), 'name' => $tag->getName(), 'slug' => $tag->getSlug()]];

        $button = $crawler->selectButton('Save');

        // Set global and per-project settings
        $form = $button->form();
        $form['contact[settingsReceiveNewsletters]']->tick();
        $form['contact[settingsReceiveSms]']->untick();
        $form['contact[settingsReceiveCalls]']->untick();
        $form['contact[settingsByProject][0][settingsReceiveNewsletters]']->untick();
        $form['contact[settingsByProject][1][settingsReceiveSms]']->untick();
        $form['contact[settingsByProject][2][settingsReceiveCalls]']->untick();

        $client->submit($form, [
            'contact[email]' => 'email@example.org',
            'contact[additionalEmails]' => ['olivie.gregoire@outlook.fr'],
            'contact[picture]' => new UploadedFile(__DIR__.'/../../../../Fixtures/upload/mario.png', 'mario.png'),
            'contact[profileFormalTitle]' => 'profileFormalTitle',
            'contact[profileFirstName]' => 'profileFirstName',
            'contact[profileMiddleName]' => 'profileMiddleName',
            'contact[profileLastName]' => 'profileLastName',
            'contact[profileBirthdate]' => '1990-01-01',
            'contact[profileGender]' => 'female',
            'contact[profileNationality]' => 'FR',
            'contact[profileCompany]' => 'profileCompany',
            'contact[profileJobTitle]' => 'profileJobTitle',
            'contact[contactPhone]' => '+33555555555',
            'contact[contactWorkPhone]' => '+33666666666',
            'contact[socialFacebook]' => 'https://facebook.com/example',
            'contact[socialTwitter]' => 'https://twitter.com/example',
            'contact[socialLinkedIn]' => 'https://linkedin.com/example',
            'contact[socialTelegram]' => 'https://telegram.com/example',
            'contact[socialWhatsapp]' => '0606060606',
            'contact[addressStreetLine1]' => 'addressStreetLine1',
            'contact[addressStreetLine2]' => 'addressStreetLine2',
            'contact[addressZipCode]' => '92110',
            'contact[addressCity]' => 'addressCity',
            'contact[addressCountry]' => '36778547219895752',
            'contact[metadataComment]' => 'metadataComment',
            'contact[metadataTags]' => Json::encode($tagsIds),
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        /** @var Contact $contact */
        $contact = static::getContainer()->get(ContactRepository::class)->findOneByUuid('20e51b91-bdec-495d-854d-85d6e74fc75e');
        $this->assertNotNull($contact->getPicture());
        $this->assertNotSame($previousPicturePathname, $contact->getPicture()->getPathname());
        $this->assertSame('92110', $contact->getArea()->getName());
        $this->assertSame('email@example.org', $contact->getEmail());
        $this->assertSame(['olivie.gregoire@outlook.fr'], $contact->getContactAdditionalEmails());
        $this->assertSame('profileFormalTitle', $contact->getProfileFormalTitle());
        $this->assertSame('profileFirstName', $contact->getProfileFirstName());
        $this->assertSame('profileMiddleName', $contact->getProfileMiddleName());
        $this->assertSame('profileLastName', $contact->getProfileLastName());
        $this->assertSame('1990-01-01', $contact->getProfileBirthdate()->format('Y-m-d'));
        $this->assertSame('female', $contact->getProfileGender());
        $this->assertSame('FR', $contact->getProfileNationality());
        $this->assertSame('profileCompany', $contact->getProfileCompany());
        $this->assertSame('profileJobTitle', $contact->getProfileJobTitle());
        $this->assertSame('+33 5 55 55 55 55', $contact->getContactPhone());
        $this->assertSame('+33 6 66 66 66 66', $contact->getContactWorkPhone());
        $this->assertInstanceOf(PhoneNumber::class, $contact->getParsedContactPhone());
        $this->assertInstanceOf(PhoneNumber::class, $contact->getParsedContactWorkPhone());
        $this->assertSame('https://facebook.com/example', $contact->getSocialFacebook());
        $this->assertSame('https://twitter.com/example', $contact->getSocialTwitter());
        $this->assertSame('https://linkedin.com/example', $contact->getSocialLinkedIn());
        $this->assertSame('https://telegram.com/example', $contact->getSocialTelegram());
        $this->assertSame('0606060606', $contact->getSocialWhatsapp());
        $this->assertSame('addressStreetLine1', $contact->getAddressStreetLine1());
        $this->assertSame('addressStreetLine2', $contact->getAddressStreetLine2());
        $this->assertSame('92110', $contact->getAddressZipCode());
        $this->assertSame('ADDRESSCITY', $contact->getAddressCity());
        $this->assertSame('France', $contact->getAddressCountry()->getName());
        $this->assertSame('metadataComment', $contact->getMetadataComment());
        $this->assertSame('StartWithTag', $contact->getMetadataTagsList());

        // Check settings
        $this->assertTrue($contact->hasSettingsReceiveNewsletters());
        $this->assertFalse($contact->hasSettingsReceiveSms());
        $this->assertFalse($contact->hasSettingsReceiveCalls());
        $this->assertSame(
            [
                [
                    'projectName' => 'Citipo',
                    'projectId' => '73wbsDqmHaMWAizkLPKOSF',
                    'settingsReceiveNewsletters' => true, // Overriden by global setting
                    'settingsReceiveSms' => true,
                    'settingsReceiveCalls' => true,
                ],
                [
                    'projectName' => 'ExampleTag',
                    'projectId' => 'Bejht60OqDGdVNPKhkGRt',
                    'settingsReceiveNewsletters' => true,
                    'settingsReceiveSms' => false,
                    'settingsReceiveCalls' => true,
                ],
                [
                    'projectName' => 'Trial',
                    'projectId' => '2zBjyjR0XXlk2Jpmpa7BYZ',
                    'settingsReceiveNewsletters' => true,
                    'settingsReceiveSms' => true,
                    'settingsReceiveCalls' => false,
                ],
            ],
            $contact->getSettingsByProject()
        );

        /*
         * Check search engine update
         */

        /** @var TransportInterface $transport */
        $transport = static::getContainer()->get('messenger.transport.async_indexing');
        $this->assertCount(1, $messages = $transport->get());
        $this->assertInstanceOf(UpdateCrmDocumentsMessage::class, $messages[0]->getMessage());

        /*
         * Check Quorum sync and stats refresh
         */

        /** @var TransportInterface $transport */
        $transport = static::getContainer()->get('messenger.transport.async_priority_low');
        $this->assertCount(2, $messages = $transport->get());
        $this->assertInstanceOf(QuorumMessage::class, $message = $messages[0]->getMessage());

        // Check the payload
        $expected = [
            'token' => 'quorum_token',
            'payload' => [
                'person' => [
                    'id' => $contact->getId(),
                    'first_name' => 'profileFirstName',
                    'last_name' => 'profileLastName',
                    'email' => 'email@example.org',
                    'email_opt_in' => true,
                    'mobile' => '+33 5 55 55 55 55',
                    'mobile_opt_in' => false,
                    'birthdate' => '1990-01-01',
                    'home_address' => [
                        'city' => 'addresscity',
                        'zip' => '92110',
                        'country_code' => 'fr',
                        'street_name' => 'addressStreetLine1',
                    ],
                    'tags' => ['StartWithTag'],
                ],
            ],
        ];
        $this->assertSame($expected, $message->getPayload());

        // Check refresh
        $this->assertInstanceOf(RefreshContactStatsMessage::class, $messages[1]->getMessage());
    }

    public function testDelete(): void
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        // See the view page
        $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts/20e51b91-bdec-495d-854d-85d6e74fc75e/view');
        $this->assertResponseIsSuccessful();

        $client->clickLink('Delete');
        $this->assertResponseRedirects();

        /*
         * Check search engine removal
         */

        /** @var TransportInterface $transport */
        $transport = static::getContainer()->get('messenger.transport.async_indexing');
        $this->assertCount(1, $messages = $transport->get());
        $this->assertInstanceOf(RemoveCrmDocumentMessage::class, $messages[0]->getMessage());

        /*
         * Check database removal
         */
        $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/contacts/20e51b91-bdec-495d-854d-85d6e74fc75e/view');
        $this->assertResponseStatusCodeSame(404);
    }
}
