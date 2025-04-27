<?php

namespace App\Tests\Controller\Console\Project\Community;

use App\Bridge\Quorum\Consumer\QuorumMessage;
use App\Entity\Community\Contact;
use App\Entity\Community\Tag;
use App\Repository\Community\ContactRepository;
use App\Repository\Community\TagRepository;
use App\Search\Consumer\RemoveCrmDocumentMessage;
use App\Search\Consumer\UpdateCrmDocumentsMessage;
use App\Tests\WebTestCase;
use App\Util\Json;
use libphonenumber\PhoneNumber;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Transport\TransportInterface;
use function PHPUnit\Framework\assertJsonStringEqualsJsonString;

class ContactManageControllerTest extends WebTestCase
{
    public function testViewEmailing()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/contacts/e90c2a1c-9504-497d-8354-c9dabc1ff7a2/view');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div:contains("Contact")');
        $this->assertSelectorExists('div:contains("Unsubscribed")');
        $this->assertSelectorExists('div:contains("92110")');
        $this->assertSelectorExists('div:contains("tchalut@yahoo.fr")');
        $this->assertSelectorExists('div:contains("Théodore Chalut")');
        $this->assertSelectorExists('div:contains("Received an email")');
        $this->assertSelectorExists('div:contains("Campaign with opens tracking")');
        $this->assertSelectorExists('div:contains("Opened on")');
        $this->assertSelectorExists('div:contains("Never clicked")');
    }

    public function testViewTexting()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/contacts/8d3323fd-e1a9-4eaa-9d4d-714abf1ff238/view');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div:contains("+55 61 99881-2130")');
        $this->assertSelectorExists('div:contains("Received a text")');
        $this->assertSelectorExists('div:contains("Sent campaign")');
    }

    public function testViewFormAnswer()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/contacts/75d245dd-c844-4ee7-8f12-a3d611a308b6/view');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div:contains("Answered a form")');
        $this->assertSelectorExists('div:contains(" Our Sustainable Europe")');
    }

    public function testCannotEditOutsideProject()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/contacts/20e51b91-bdec-495d-854d-85d6e74fc75e/edit');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testEdit()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/contacts/e90c2a1c-9504-497d-8354-c9dabc1ff7a2/edit');
        $this->assertResponseIsSuccessful();

        /** @var Contact $contact */
        $contact = static::getContainer()->get(ContactRepository::class)->findOneByUuid('e90c2a1c-9504-497d-8354-c9dabc1ff7a2');
        $this->assertSame('92110', $contact->getArea()->getName());
        $this->assertSame('tchalut@yahoo.fr', $contact->getEmail());
        $this->assertNull($contact->getProfileFormalTitle());
        $this->assertSame('Théodore', $contact->getProfileFirstName());
        $this->assertNull($contact->getProfileMiddleName());
        $this->assertSame('Chalut', $contact->getProfileLastName());
        $this->assertNull($contact->getProfileBirthdate());
        $this->assertNull($contact->getProfileGender());
        $this->assertNull($contact->getProfileNationality());
        $this->assertNull($contact->getProfileCompany());
        $this->assertNull($contact->getProfileJobTitle());
        $this->assertSame('+33 7 57 59 46 29', $contact->getContactPhone());
        $this->assertNull($contact->getContactWorkPhone());
        $this->assertNull($contact->getSocialFacebook());
        $this->assertSame('@theodorechalut', $contact->getSocialTwitter());
        $this->assertSame('theodore.chalut', $contact->getSocialLinkedIn());
        $this->assertNull($contact->getSocialTelegram());
        $this->assertSame('+33600000000', $contact->getSocialWhatsapp());
        $this->assertNull($contact->getAddressStreetLine1());
        $this->assertNull($contact->getAddressStreetLine2());
        $this->assertNull($contact->getAddressZipCode());
        $this->assertNull($contact->getAddressCity());
        $this->assertNull($contact->getAddressCountry());
        $this->assertSame('ContainsTagInside', $contact->getMetadataTagsList());
        $this->assertNull($contact->getMetadataComment());

        /** @var Tag $tag */
        $tag = static::getContainer()->get(TagRepository::class)->findOneBy(['slug' => 'startwithtag']);
        $tagsIds = [$tag->getId() => ['id' => $tag->getId(), 'name' => $tag->getName(), 'slug' => $tag->getSlug()]];

        $button = $crawler->selectButton('Save');
        $client->submit($button->form(), [
            'contact[email]' => 'email@example.org',
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
            'contact[addressCity]' => 'addressCity',
            'contact[metadataTags]' => Json::encode($tagsIds),
            'contact[metadataComment]' => 'Comment',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        /** @var Contact $contact */
        $contact = static::getContainer()->get(ContactRepository::class)->findOneByUuid('e90c2a1c-9504-497d-8354-c9dabc1ff7a2');
        $this->assertSame('92110', $contact->getArea()->getName());
        $this->assertSame('email@example.org', $contact->getEmail());
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
        $this->assertNull($contact->getAddressZipCode());
        $this->assertSame('ADDRESSCITY', $contact->getAddressCity());
        $this->assertNull($contact->getAddressCountry());
        $this->assertSame('StartWithTag', $contact->getMetadataTagsList());
        $this->assertSame('Comment', $contact->getMetadataComment());

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

        $messages = $transport->get();
        $this->assertCount(1, $messages);

        /** @var QuorumMessage $message */
        $message = $messages[0]->getMessage();
        $this->assertInstanceOf(QuorumMessage::class, $message);

        // Check the payload
        $expected = [
            'token' => 'quorum_token',
            'payload' => [
                'person' => [
                    'id' => $contact->getId(),
                    'first_name' => 'profileFirstName',
                    'last_name' => 'profileLastName',
                    'email' => 'email@example.org',
                    'email_opt_in' => false,
                    'mobile' => '+33 5 55 55 55 55',
                    'mobile_opt_in' => false,
                    'birthdate' => '1990-01-01',
                    'home_address' => [
                        'city' => 'addresscity',
                        'zip' => null,
                        'country_code' => null,
                        'street_name' => 'addressStreetLine1',
                    ],
                    'tags' => ['StartWithTag'],
                ],
            ],
        ];

        $this->assertSame($expected, $message->getPayload());
    }

    public function testDelete(): void
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        // See the view page
        $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/contacts/38dd80c0-b53e-4c29-806f-d2aeca8edb80/view');
        $this->assertResponseIsSuccessful();

        $client->clickLink('Delete');
        $this->assertResponseRedirects();

        /*
         * Check search engine update
         */

        /** @var TransportInterface $transport */
        $transport = static::getContainer()->get('messenger.transport.async_indexing');
        $this->assertCount(1, $messages = $transport->get());
        $this->assertInstanceOf(RemoveCrmDocumentMessage::class, $messages[0]->getMessage());

        // The view page should return 404
        $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/contacts/38dd80c0-b53e-4c29-806f-d2aeca8edb80/view');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testProjectGetContactJson()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $contactUuid = 'e90c2a1c-9504-497d-8354-c9dabc1ff7a2'; // tchalut@yahoo.fr

        $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/contacts/'.$contactUuid.'/data');
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = Json::decode($client->getResponse()->getContent());

        $expectedPayload = [
            '_resource' => 'Contact',
            'id' => '75klpBHn7DottVkejf0LDu', // Base62 of e90c2a1c-9504-497d-8354-c9dabc1ff7a2
            'organizationId' => self::ORGA_CITIPO_UUID,
            'email' => 'tchalut@yahoo.fr',
            'additionalEmails' => [],
            'picture' => 'https://www.gravatar.com/avatar/6a0ee01e6bb5653ed43ad71195571643?d=mp&s=800',
            'isMember' => false,
            'profileFormalTitle' => null,
            'profileFirstName' => 'Théodore',
            'profileMiddleName' => null,
            'profileLastName' => 'Chalut',
            'profileBirthdate' => null,
            'profileGender' => null,
            'profileNationality' => null,
            'profileCompany' => null,
            'profileJobTitle' => null,
            'accountLanguage' => 'en',
            'contactPhone' => '+33 7 57 59 46 29',
            'contactWorkPhone' => null,
            'parsedContactPhone' => '+33 7 57 59 46 29',
            'parsedContactWorkPhone' => null,
            'socialFacebook' => null,
            'socialTwitter' => '@theodorechalut',
            'socialLinkedIn' => 'theodore.chalut',
            'socialTelegram' => null,
            'socialWhatsapp' => '+33600000000',
            'addressStreetNumber' => null,
            'addressStreetLine1' => null,
            'addressStreetLine2' => null,
            'addressZipCode' => null,
            'addressCity' => null,
            'addressCountry' => null,
            'area' => [
                'id' => '39389989938296926', // 92110 area ID
                'name' => '92110',
            ],
            'settingsReceiveNewsletters' => false, // Fixture data
            'settingsReceiveSms' => false,         // Fixture data
            'settingsReceiveCalls' => false,         // Fixture data
            'settingsByProject' => [], // Project-specific settings not typically loaded by default here
            'metadataTags' => ['ContainsTagInside'],
            'metadataSource' => 'Api test',
            'metadataComment' => null,
            'metadataCustomFields' => [],
            'createdAt' => '2021-05-13T09:38:11+00:00', // Assuming fixture data timestamp
        ];

        // Remove fields that might change dynamically
        unset($responseData['picture']);
        unset($responseData['createdAt']);
        unset($expectedPayload['picture']);
        unset($expectedPayload['createdAt']);

        // Sort tags for consistent comparison
        sort($responseData['metadataTags']);
        sort($expectedPayload['metadataTags']);

        assertJsonStringEqualsJsonString(Json::encode($expectedPayload), Json::encode($responseData));
    }

    public function testProjectGetContactJsonForbidden()
    {
        $client = static::createClient();
        $this->authenticate($client);

        // Contact exists but is not in this project
        $contactUuid = '20e51b91-bdec-495d-854d-85d6e74fc75e'; // olivie.gregoire@gmail.com

        $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/contacts/'.$contactUuid.'/data');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND); // Check for not found as controller checks isInProject()
    }

    public function testProjectUpdateContactJson()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $contactUuid = 'e90c2a1c-9504-497d-8354-c9dabc1ff7a2'; // tchalut@yahoo.fr
        $contactRepo = static::getContainer()->get(ContactRepository::class);

        /** @var Contact $contact */
        $contact = $contactRepo->findOneByUuid($contactUuid);
        $this->assertSame('Théodore', $contact->getProfileFirstName());
        $this->assertNull($contact->getAddressZipCode());
        $this->assertNull($contact->getMetadataComment());

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/contacts');
        $token = $this->filterGlobalCsrfToken($crawler);

        $payload = [
            'profileFirstName' => 'Theo',
            'metadataComment' => 'Updated comment',
            'additionalEmails' => ['theo.new@example.com'],
            // Address/Area cannot be updated from project context
            // Settings cannot be updated from project context
        ];

        $client->request(
            'PATCH',
            '/console/project/'.self::PROJECT_IDF_UUID.'/community/contacts/'.$contactUuid.'?_token='.$token,
            [], [], ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $this->assertResponseIsSuccessful();
        $responseData = Json::decode($client->getResponse()->getContent());
        $this->assertTrue($responseData['success']);
        $this->assertSame($contactUuid, $responseData['contact_uuid']);

        // Refetch and assert changes
        static::getContainer()->get('doctrine')->getManager()->clear();
        $updatedContact = $contactRepo->findOneByUuid($contactUuid);
        $this->assertSame('Theo', $updatedContact->getProfileFirstName()); // Updated
        $this->assertSame('Updated comment', $updatedContact->getMetadataComment()); // Updated
        $this->assertSame('Chalut', $updatedContact->getProfileLastName()); // Unchanged
        $this->assertSame(['theo.new@example.com'], $updatedContact->getContactAdditionalEmails()); // Additional emails updated
    }

    public function testProjectUpdateContactJsonValidationError()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $contactUuid = 'e90c2a1c-9504-497d-8354-c9dabc1ff7a2'; // tchalut@yahoo.fr

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/community/contacts');
        $token = $this->filterGlobalCsrfToken($crawler);

        $payload = [
            'email' => 'invalid-email',
        ];

        $client->request(
            'PATCH',
            '/console/project/'.self::PROJECT_IDF_UUID.'/community/contacts/'.$contactUuid.'?_token='.$token,
            [], [], ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $responseData = Json::decode($client->getResponse()->getContent());
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertNotEmpty($responseData['errors']); // Check that some error was returned
    }
}
