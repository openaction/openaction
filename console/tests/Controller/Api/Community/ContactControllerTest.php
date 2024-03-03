<?php

namespace App\Tests\Controller\Api\Community;

use App\Analytics\Consumer\RefreshContactStatsMessage;
use App\Bridge\Integromat\Consumer\IntegromatWebhookMessage;
use App\Bridge\Quorum\Consumer\QuorumMessage;
use App\Bridge\Sendgrid\Consumer\SendgridMessage;
use App\Entity\Community\Contact;
use App\Entity\Community\ContactUpdate;
use App\Entity\Community\Tag;
use App\Entity\Organization;
use App\Repository\Community\ContactRepository;
use App\Repository\Community\ContactUpdateRepository;
use App\Repository\Community\EmailAutomationMessageRepository;
use App\Repository\Community\TagRepository;
use App\Repository\OrganizationRepository;
use App\Repository\ProjectRepository;
use App\Tests\ApiTestCase;
use App\Util\Address;
use App\Util\Json;
use App\Util\Uid;
use Doctrine\ORM\EntityManagerInterface;
use libphonenumber\PhoneNumber;
use SendGrid\Mail\To;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\Messenger\Transport\TransportInterface;

class ContactControllerTest extends ApiTestCase
{
    public function testListNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/community/contacts', null, 401);
    }

    public function testListInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/community/contacts', 'invalid', 401);
    }

    public function testListAllContacts()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/community/contacts', self::CITIPO_TOKEN);
        $this->assertCount(6, $result['data']);

        // Test the payload is the one expected
        $this->assertApiResponse($result, [
            'data' => [
                // Test full mapping
                [
                    '_resource' => 'Contact',
                    'id' => '104SKb9m0xnYyt8OiWn3ks',
                    'email' => 'olivie.gregoire@gmail.com',
                    'picture' => 'http://localhost/serve/contact-picture.jpg',
                    'isMember' => false,
                    'profileFormalTitle' => null,
                    'profileFirstName' => 'Olivie',
                    'profileMiddleName' => null,
                    'profileLastName' => 'Gregoire',
                    'profileBirthdate' => null,
                    'profileGender' => null,
                    'profileNationality' => null,
                    'profileCompany' => null,
                    'profileJobTitle' => null,
                    'contactPhone' => '+33 7 57 59 46 25',
                    'contactWorkPhone' => null,
                    'parsedContactPhone' => '+33 7 57 59 46 25',
                    'parsedContactWorkPhone' => null,
                    'socialFacebook' => 'olivie.gregoire',
                    'socialTwitter' => '@golivie92',
                    'socialLinkedIn' => null,
                    'socialTelegram' => null,
                    'socialWhatsapp' => null,
                    'addressStreetLine1' => null,
                    'addressStreetLine2' => null,
                    'addressZipCode' => null,
                    'addressCity' => null,
                    'addressCountry' => null,
                    'settingsReceiveNewsletters' => true,
                    'settingsReceiveSms' => true,
                    'settingsReceiveCalls' => true,
                    'metadataTags' => [],
                    'metadataCustomFields' => [
                        'externalId' => '2485c2e31af5',
                        'donations' => [
                            ['amount' => 1000, 'date' => '2021-04-28 15:03:42'],
                            ['amount' => 2000, 'date' => '2021-05-13 09:38:11'],
                        ],
                    ],
                ],

                // Test full list order (created at DESC)
                [
                    'id' => '75klpBHn7DottVkejf0LDu',
                    'email' => 'tchalut@yahoo.fr',
                    'picture' => 'https://www.gravatar.com/avatar/6a0ee01e6bb5653ed43ad71195571643?d=mp&s=800',
                ],
                [
                    'id' => '11dGXM5xrWWBanrAgRGB6f',
                    'email' => null,
                ],
                [
                    'id' => '43MAmriw076a19EWSo79m9',
                    'email' => 'brunella.courtemanche2@orange.fr',
                ],
                [
                    'id' => '1jInRLnYMgt9oAuQVQbTGK',
                    'email' => 'a.compagnon@protonmail.com',
                ],
                [
                    'id' => 'whHmA7YSvN9ccbnGigJnx',
                    'email' => 'apolline.mousseau@rpr.fr',
                ],
            ],
        ]);
    }

    public function testListContactsTagsFilter()
    {
        $client = self::createClient();

        /** @var Tag $tag */
        $tag = static::getContainer()->get(TagRepository::class)->findOneBy(['name' => 'ExampleTag']);

        $result = $this->apiRequest($client, 'GET', '/api/community/contacts?tags_filter[]='.$tag->getId(), self::CITIPO_TOKEN);
        $this->assertCount(3, $result['data']);

        // Test the payload is the one expected
        $this->assertApiResponse($result, [
            'data' => [
                // Full mapping already tested in previous test
                ['email' => 'olivie.gregoire@gmail.com'],
                ['email' => null],
                ['email' => 'a.compagnon@protonmail.com'],
            ],
        ]);
    }

    public function testListContactsAreaFilter()
    {
        $client = self::createClient();

        // Ile-de-France
        $result = $this->apiRequest($client, 'GET', '/api/community/contacts?areas_filter[]=64795327863947811', self::CITIPO_TOKEN);
        $this->assertCount(5, $result['data']);

        // Test the payload is the one expected
        $this->assertApiResponse($result, [
            'data' => [
                // Full mapping already tested in previous test
                ['email' => 'tchalut@yahoo.fr'],
                ['email' => null],
                ['email' => 'brunella.courtemanche2@orange.fr'],
                ['email' => 'a.compagnon@protonmail.com'],
                ['email' => 'apolline.mousseau@rpr.fr'],
            ],
        ]);
    }

    public function testSearchFirstNameLastName()
    {
        $client = self::createClient();

        $result = $this->apiRequest(
            client: $client,
            method: 'POST',
            endpoint: '/api/community/contacts/search',
            token: self::CITIPO_TOKEN,
            content: Json::encode([
                'filter' => [
                    'profile_first_name_slug = "apolline"',
                    'profile_last_name_slug = "mousseau"',
                ],
            ]),
        );

        $this->assertCount(1, $result['hits']);

        $this->assertApiResponse($result, [
            'hits' => [
                // Full mapping testing in indexer tests
                ['email' => 'apolline.mousseau@rpr.fr'],
            ],
        ]);
    }

    public function testSearchEmail()
    {
        $client = self::createClient();

        $result = $this->apiRequest(
            client: $client,
            method: 'POST',
            endpoint: '/api/community/contacts/search',
            token: self::CITIPO_TOKEN,
            content: Json::encode([
                'filter' => [
                    'email = "olivie.gregoire@gmail.com"',
                ],
            ]),
        );

        $this->assertCount(1, $result['hits']);

        $this->assertApiResponse($result, [
            'hits' => [
                // Full mapping testing in indexer tests
                ['email' => 'olivie.gregoire@gmail.com'],
            ],
        ]);
    }

    public function testSearchPhone()
    {
        $client = self::createClient();

        $result = $this->apiRequest(
            client: $client,
            method: 'POST',
            endpoint: '/api/community/contacts/search',
            token: self::CITIPO_TOKEN,
            content: Json::encode([
                'filter' => [
                    'parsed_contact_phone = "+33757594629"',
                ],
            ]),
        );

        $this->assertCount(1, $result['hits']);

        $this->assertApiResponse($result, [
            'hits' => [
                // Full mapping testing in indexer tests
                ['email' => 'tchalut@yahoo.fr'],
            ],
        ]);
    }

    public function testSearchNotInProject()
    {
        $client = self::createClient();

        $result = $this->apiRequest(
            client: $client,
            method: 'POST',
            endpoint: '/api/community/contacts/search',
            token: '3a4683898cdd75936c94475d55049c07c407b64f18e23d6f726894fc0cc79f4f', // IDF
            content: Json::encode([
                'filter' => [
                    'email = "olivie.gregoire@gmail.com"',
                ],
            ]),
        );

        $this->assertCount(0, $result['hits']);
    }

    public function testCreateEditNoToken()
    {
        $this->apiRequest(self::createClient(), 'POST', '/api/community/contacts', null, 401);
    }

    public function testCreateEditInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'POST', '/api/community/contacts', 'invalid', 401);
    }

    public function provideCreateContact()
    {
        yield 'newContactEmail' => [
            'json' => Json::encode([
                'email' => 'New@Email.com',
                'profileFormalTitle' => 'Mr',
                'profileFirstName' => 'John',
                'profileMiddleName' => 'J',
                'profileLastName' => 'Doe',
                'profileBirthdate' => '2020-01-02',
                'profileGender' => 'male',
                'profileNationality' => 'FR',
                'profileCompany' => 'ECorp',
                'profileJobTitle' => 'CEO',
                'accountLanguage' => 'fr',
                'contactAdditionalEmails' => ['johndoe@gmail.com', 'johndoe@orange.fr'],
                'contactPhone' => '01 23 45 67 89',
                'contactWorkPhone' => '01.00.00.00.01',
                'socialFacebook' => 'https://facebook.com/ecorp.ceo',
                'socialTwitter' => 'https://twitter.com/ecorp.ceo',
                'socialLinkedIn' => 'https://linkedin.com/ecorp.ceo',
                'socialTelegram' => 'ec0rp_ce0',
                'socialWhatsapp' => '+33666666666',
                'addressStreetNumber' => '1',
                'addressStreetLine1' => 'First avenue',
                'addressStreetLine2' => 'Suite 1',
                'addressZipCode' => '92110',
                'addressCity' => 'Clichy',
                'addressCountry' => 'FR',
                'settingsReceiveNewsletters' => false,
                'settingsReceiveSms' => false,
                'settingsReceiveCalls' => false,
                'metadataCustomFields' => ['hello' => 'moto'],
                'metadataTags' => ['ExampleTag', 'NewTagShouldBeCreated'],
                'metadataSource' => 'API test',
                'metadataComment' => 'a comment',
            ]),
            'expectedParsedContactPhone' => '+33 1 23 45 67 89',
            'expectedParsedContactWorkPhone' => '+33 1 00 00 00 01',
            'expectedCode' => Response::HTTP_OK,
            'expectedArea' => '39389989938296926',
            'expectedAdminAutomation' => true,
            'expectedWelcomeAutomation' => false,
            'expectedRegisterConfirm' => false,
            'expectedTagAutomation' => true,
        ];

        yield 'newContactNoEmail' => [
            'json' => Json::encode([
                'profileFormalTitle' => 'Mr',
                'profileFirstName' => 'John',
                'profileMiddleName' => 'J',
                'profileLastName' => 'Doe',
                'profileBirthdate' => '2020-01-02',
                'profileGender' => 'male',
                'profileNationality' => 'FR',
                'profileCompany' => 'ECorp',
                'profileJobTitle' => 'CEO',
                'accountLanguage' => 'fr',
                'contactAdditionalEmails' => ['johndoe@gmail.com', 'johndoe@orange.fr'],
                'contactPhone' => '01 23 45 67 89',
                'contactWorkPhone' => '01.00.00.00.01',
                'socialFacebook' => 'https://facebook.com/ecorp.ceo',
                'socialTwitter' => 'https://twitter.com/ecorp.ceo',
                'socialLinkedIn' => 'https://linkedin.com/ecorp.ceo',
                'socialTelegram' => 'ec0rp_ce0',
                'socialWhatsapp' => '+33666666666',
                'addressStreetNumber' => '1',
                'addressStreetLine1' => 'First avenue',
                'addressStreetLine2' => 'Suite 1',
                'addressZipCode' => '92110',
                'addressCity' => 'Clichy',
                'addressCountry' => 'FR',
                'settingsReceiveNewsletters' => false,
                'settingsReceiveSms' => false,
                'settingsReceiveCalls' => false,
                'metadataCustomFields' => ['hello' => 'moto'],
                'metadataTags' => ['ExampleTag', 'NewTagShouldBeCreated'],
                'metadataSource' => 'API test',
                'metadataComment' => 'a comment',
            ]),
            'expectedParsedContactPhone' => '+33 1 23 45 67 89',
            'expectedParsedContactWorkPhone' => '+33 1 00 00 00 01',
            'expectedCode' => Response::HTTP_OK,
            'expectedArea' => '39389989938296926',
            'expectedAdminAutomation' => true,
            'expectedWelcomeAutomation' => false,
            'expectedRegisterConfirm' => false,
            'expectedTagAutomation' => true,
        ];

        yield 'newMember' => [
            'json' => Json::encode([
                'email' => 'New@Email.com',
                'profileFormalTitle' => 'Mr',
                'profileFirstName' => 'John',
                'profileMiddleName' => 'J',
                'profileLastName' => 'Doe',
                'profileBirthdate' => '2020-01-02',
                'profileGender' => 'male',
                'profileNationality' => 'FR',
                'profileCompany' => 'ECorp',
                'profileJobTitle' => 'CEO',
                'accountLanguage' => 'fr',
                'accountPassword' => 'password',
                'contactAdditionalEmails' => ['johndoe@gmail.com', 'johndoe@orange.fr'],
                'contactPhone' => '1',
                'contactWorkPhone' => '2',
                'socialFacebook' => 'https://facebook.com/ecorp.ceo',
                'socialTwitter' => 'https://twitter.com/ecorp.ceo',
                'socialLinkedIn' => 'https://linkedin.com/ecorp.ceo',
                'socialTelegram' => 'ec0rp_ce0',
                'socialWhatsapp' => '+33666666666',
                'addressStreetNumber' => '1',
                'addressStreetLine1' => 'First avenue',
                'addressStreetLine2' => 'Suite 1',
                'addressZipCode' => '92110',
                'addressCity' => 'Clichy',
                'addressCountry' => 'FR',
                'settingsReceiveNewsletters' => false,
                'settingsReceiveSms' => false,
                'settingsReceiveCalls' => false,
                'metadataCustomFields' => ['hello' => 'moto'],
                'metadataTags' => ['ExampleTag', 'NewTagShouldBeCreated'],
                'metadataSource' => 'API test',
                'metadataComment' => 'a comment',
            ]),
            'expectedParsedContactPhone' => null,
            'expectedParsedContactWorkPhone' => null,
            'expectedCode' => Response::HTTP_OK,
            'expectedArea' => '39389989938296926',
            'expectedAdminAutomation' => true,
            'expectedWelcomeAutomation' => true,
            'expectedRegisterConfirm' => true,
            'expectedTagAutomation' => true,
        ];

        yield 'newTaggedContactEmail' => [
            'json' => Json::encode([
                'email' => 'New@Email.com',
                'profileFormalTitle' => 'Mr',
                'profileFirstName' => 'John',
                'profileMiddleName' => 'J',
                'profileLastName' => 'Doe',
                'profileBirthdate' => '2020-01-02',
                'profileGender' => 'male',
                'profileNationality' => 'FR',
                'profileCompany' => 'ECorp',
                'profileJobTitle' => 'CEO',
                'accountLanguage' => 'fr',
                'contactAdditionalEmails' => ['johndoe@gmail.com', 'johndoe@orange.fr'],
                'contactPhone' => '01 23 45 67 89',
                'contactWorkPhone' => '01.00.00.00.01',
                'socialFacebook' => 'https://facebook.com/ecorp.ceo',
                'socialTwitter' => 'https://twitter.com/ecorp.ceo',
                'socialLinkedIn' => 'https://linkedin.com/ecorp.ceo',
                'socialTelegram' => 'ec0rp_ce0',
                'socialWhatsapp' => '+33666666666',
                'addressStreetNumber' => '1',
                'addressStreetLine1' => 'First avenue',
                'addressStreetLine2' => 'Suite 1',
                'addressZipCode' => '92110',
                'addressCity' => 'Clichy',
                'addressCountry' => 'FR',
                'settingsReceiveNewsletters' => false,
                'settingsReceiveSms' => false,
                'settingsReceiveCalls' => false,
                'metadataCustomFields' => ['hello' => 'moto'],
                'metadataTags' => ['ExampleTag', 'NewTagShouldBeCreated'],
                'metadataSource' => 'API test',
                'metadataComment' => 'a comment',
            ]),
            'expectedParsedContactPhone' => '+33 1 23 45 67 89',
            'expectedParsedContactWorkPhone' => '+33 1 00 00 00 01',
            'expectedCode' => Response::HTTP_OK,
            'expectedArea' => '39389989938296926',
            'expectedAdminAutomation' => true,
            'expectedWelcomeAutomation' => false,
            'expectedRegisterConfirm' => false,
            'expectedTagAutomation' => true,
        ];
    }

    /**
     * @dataProvider provideCreateContact
     */
    public function testCreateContact(
        string $json,
        ?string $expectedParsedContactPhone,
        ?string $expectedParsedContactWorkPhone,
        int $expectedCode,
        string $expectedArea,
        bool $expectedAdminAutomation,
        bool $expectedWelcomeAutomation,
        bool $expectedRegisterConfirm,
        bool $expectedTagAutomation,
    ) {
        $client = self::createClient();

        $response = $this->apiRequest($client, 'POST', '/api/community/contacts', self::CITIPO_TOKEN, $expectedCode, $json);

        $expectedData = Json::decode($json);

        /** @var Contact $contact */
        $contact = static::getContainer()->get(ContactRepository::class)->findOneBy([
            'metadataSource' => 'API test',
            'organization' => static::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => self::CITIPO_ORG]),
        ]);

        // Fetch the tags association changes
        static::getContainer()->get(EntityManagerInterface::class)->refresh($contact);

        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertSame(!empty($expectedData['email']) ? strtolower($expectedData['email']) : null, $contact->getEmail());
        $this->assertSame($expectedData['profileFormalTitle'], $contact->getProfileFormalTitle());
        $this->assertSame($expectedData['profileFirstName'], $contact->getProfileFirstName());
        $this->assertSame($expectedData['profileMiddleName'], $contact->getProfileMiddleName());
        $this->assertSame($expectedData['profileLastName'], $contact->getProfileLastName());
        $this->assertSame($expectedData['profileBirthdate'], $contact->getProfileBirthdate()->format('Y-m-d'));
        $this->assertSame($expectedData['profileGender'], $contact->getProfileGender());
        $this->assertSame($expectedData['profileNationality'], $contact->getProfileNationality());
        $this->assertSame($expectedData['profileCompany'], $contact->getProfileCompany());
        $this->assertSame($expectedData['profileJobTitle'], $contact->getProfileJobTitle());
        $this->assertSame($expectedData['accountLanguage'], $contact->getAccountLanguage());
        $this->assertSame(isset($expectedData['accountPassword']), $contact->isMember());
        $this->assertSame($expectedData['contactAdditionalEmails'], $contact->getContactAdditionalEmails());
        $this->assertSame($expectedData['socialFacebook'], $contact->getSocialFacebook());
        $this->assertSame($expectedData['socialTwitter'], $contact->getSocialTwitter());
        $this->assertSame($expectedData['socialLinkedIn'], $contact->getSocialLinkedIn());
        $this->assertSame($expectedData['socialTelegram'], $contact->getSocialTelegram());
        $this->assertSame($expectedData['socialWhatsapp'], $contact->getSocialWhatsapp());
        $this->assertSame($expectedData['addressStreetNumber'], $contact->getAddressStreetNumber());
        $this->assertSame($expectedData['addressStreetLine1'], $contact->getAddressStreetLine1());
        $this->assertSame($expectedData['addressStreetLine2'], $contact->getAddressStreetLine2());
        $this->assertSame((string) $expectedData['addressZipCode'], $contact->getAddressZipCode());
        $this->assertSame(Address::formatCityName($expectedData['addressCity']), $contact->getAddressCity());
        $this->assertSame(36778547219895752, $contact->getAddressCountry()->getId());
        $this->assertSame($expectedData['settingsReceiveNewsletters'], $contact->hasSettingsReceiveNewsletters());
        $this->assertSame($expectedData['settingsReceiveSms'], $contact->hasSettingsReceiveSms());
        $this->assertSame($expectedData['settingsReceiveCalls'], $contact->hasSettingsReceiveCalls());
        $this->assertSame($expectedData['metadataCustomFields'], $contact->getMetadataCustomFields());
        $this->assertSame($expectedData['metadataSource'], $contact->getMetadataSource());
        $this->assertSame($expectedData['metadataComment'], $contact->getMetadataComment());
        $this->assertSame($expectedArea, (string) $contact->getArea()->getId());

        /*
         * Check tags
         */
        $contactTags = array_values($contact->getMetadataTagsNames());
        sort($contactTags);

        $expectedTags = array_values(array_merge($expectedData['metadataTags'], ['Citipo']));
        sort($expectedTags);

        $this->assertSame($expectedTags, $contactTags);

        /*
         * Check parsed phone
         */
        if ($expectedParsedContactPhone) {
            $this->assertInstanceof(PhoneNumber::class, $contact->getParsedContactPhone());
            $this->assertSame($expectedParsedContactPhone, $contact->getContactPhone());
        } else {
            $this->assertNull($contact->getParsedContactPhone());
        }

        if ($expectedParsedContactWorkPhone) {
            $this->assertInstanceof(PhoneNumber::class, $contact->getParsedContactWorkPhone());
            $this->assertSame($expectedParsedContactWorkPhone, $contact->getContactWorkPhone());
        } else {
            $this->assertNull($contact->getParsedContactWorkPhone());
        }

        $this->assertSame($response['parsedContactPhone'], $expectedParsedContactPhone);
        $this->assertSame($response['parsedContactWorkPhone'], $expectedParsedContactWorkPhone);

        /*
         * Check automation
         */

        // Check credits usage
        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => self::CITIPO_ORG]);

        $this->assertSame(
            1000000
                - ($expectedAdminAutomation ? 1 : 0)
                - ($expectedWelcomeAutomation ? 1 : 0)
                - ($expectedRegisterConfirm ? 1 : 0)
                - ($expectedTagAutomation ? 1 : 0),
            $orga->getCreditsBalance()
        );

        // Check messages
        /** @var EmailAutomationMessageRepository $automationMessageRepo */
        $automationMessageRepo = static::getContainer()->get(EmailAutomationMessageRepository::class);

        /** @var TransportInterface $transport */
        $transport = static::getContainer()->get('messenger.transport.async_emailing');

        $this->assertCount(
            ($expectedAdminAutomation ? 1 : 0) + ($expectedWelcomeAutomation ? 1 : 0) + ($expectedTagAutomation ? 1 : 0),
            $messages = $transport->get()
        );

        $expectedMailsSent = [];

        if ($expectedAdminAutomation) {
            $expectedMailsSent[] = [
                'subject' => 'New contact alert',
                'fromEmail' => 'contact@citipo.com',
                'fromName' => 'Jacques BAUER',
                'content' => 'Contact [fullName]',
                'to' => ['contact@citipo.com'],
            ];
        }

        if ($expectedWelcomeAutomation) {
            $expectedMailsSent[] = [
                'subject' => 'Welcome !',
                'fromEmail' => 'contact@citipo.com',
                'fromName' => 'Jacques BAUER',
                'content' => 'Welcome [fullName]',
                'to' => [$contact->getEmail()],
            ];
        }

        if ($expectedTagAutomation) {
            $expectedMailsSent[] = [
                'subject' => 'Filtered tag alert',
                'fromEmail' => 'contact@citipo.com',
                'fromName' => 'Jacques BAUER',
                'content' => 'Tag alert [fullName]',
                'to' => ['contact@citipo.com'],
            ];
        }

        foreach ($expectedMailsSent as $k => $expectedMail) {
            /** @var SendgridMessage $message */
            $message = $messages[$k]->getMessage();
            $this->assertInstanceOf(SendgridMessage::class, $message);

            // Check the mail
            $mail = $message->getMail();
            $this->assertSame($expectedMail['subject'], $mail->getGlobalSubject()->getSubject());
            $this->assertSame($expectedMail['fromEmail'], $mail->getFrom()->getEmail());
            $this->assertSame($expectedMail['fromName'], $mail->getFrom()->getName());
            $this->assertCount(1, $contents = $mail->getContents());
            $this->assertStringContainsString($expectedMail['content'], $contents[0]->getValue());

            $to = [];
            foreach ($mail->getPersonalizations() as $personalization) {
                $to[] = array_map(fn (To $to) => $to->getEmail(), $personalization->getTos());
            }

            $to = array_merge(...$to);
            sort($to);

            $this->assertSame($expectedMail['to'], $to);
        }

        /*
         * Check integrations and stats refresh
         */

        /** @var TransportInterface $transport */
        $transport = static::getContainer()->get('messenger.transport.async_priority_low');
        $this->assertCount(3, $messages = $transport->get());
        $this->assertInstanceOf(RefreshContactStatsMessage::class, $messages[0]->getMessage());

        // Integromat
        // Payload mapping already checked by
        // App\Tests\Controller\Console\Organization\Community\ContactControllerTest::testCreate
        /* @var IntegromatWebhookMessage $message */
        $this->assertInstanceOf(IntegromatWebhookMessage::class, $message = $messages[1]->getMessage());
        $this->assertSame('https://hook.integromat.com/swme2wu7n735qcmhbeyfj595c2ey2aju', $message->getUrl());

        // Quorum
        // Payload mapping already checked by
        // App\Tests\Controller\Console\Project\Community\ContactControllerTest::testEdit
        $this->assertInstanceOf(QuorumMessage::class, $messages[2]->getMessage());

        /*
         * Check email
         */

        /** @var TransportInterface $transport */
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');
        $messages = $transport->get();

        if ($expectedRegisterConfirm) {
            $this->assertCount(1, $messages);
            $this->assertInstanceOf(SendEmailMessage::class, $messages[0]->getMessage());
        } else {
            $this->assertCount(0, $messages);
        }
    }

    public function provideEditContact()
    {
        yield 'normal' => [
            'json' => Json::encode([
                'email' => 'olivie.gregoire@gmail.com',
                'picture' => base64_encode(file_get_contents(__DIR__.'/../../../Fixtures/upload/picture.jpg')),
                'profileLastName' => 'GREGOIRE',
                'profileNationality' => 'GB',
                'addressZipCode' => '92110',
                'metadataTags' => ['UpdatedTagShouldBeCreated'],
                'metadataCustomFields' => [
                    'donations' => [
                        ['amount' => 1000, 'date' => '2021-04-28 15:03:42'],
                        ['amount' => 2000, 'date' => '2021-05-13 09:38:11'],
                        ['amount' => 3000, 'date' => '2021-05-13 18:26:57'],
                    ],
                    'hello' => 'moto',
                ],
                'contactPhone' => '+33555555555',
                'contactWorkPhone' => '+33666666666',
            ]),
            'expectedParsedContactPhone' => '+33 5 55 55 55 55',
            'expectedParsedContactWorkPhone' => '+33 6 66 66 66 66',
            'expectedCode' => Response::HTTP_OK,
        ];

        yield 'additional-email' => [
            'json' => Json::encode([
                'email' => 'olivie.gregoire@outlook.com',
                'picture' => base64_encode(file_get_contents(__DIR__.'/../../../Fixtures/upload/picture.jpg')),
                'profileLastName' => 'GREGOIRE',
                'profileNationality' => 'GB',
                'addressZipCode' => '92110',
                'metadataTags' => ['UpdatedTagShouldBeCreated'],
                'metadataCustomFields' => [
                    'donations' => [
                        ['amount' => 1000, 'date' => '2021-04-28 15:03:42'],
                        ['amount' => 2000, 'date' => '2021-05-13 09:38:11'],
                        ['amount' => 3000, 'date' => '2021-05-13 18:26:57'],
                    ],
                    'hello' => 'moto',
                ],
                'contactPhone' => '+33555555555',
                'contactWorkPhone' => '+33666666666',
            ]),
            'expectedParsedContactPhone' => '+33 5 55 55 55 55',
            'expectedParsedContactWorkPhone' => '+33 6 66 66 66 66',
            'expectedCode' => Response::HTTP_OK,
        ];

        yield 'invalid-phone' => [
            'json' => Json::encode([
                'email' => 'olivie.gregoire@gmail.com',
                'profileLastName' => 'GREGOIRE',
                'profileNationality' => 'GB',
                'addressZipCode' => '92110',
                'metadataTags' => ['UpdatedTagShouldBeCreated'],
                'metadataCustomFields' => [
                    'donations' => [
                        ['amount' => 1000, 'date' => '2021-04-28 15:03:42'],
                        ['amount' => 2000, 'date' => '2021-05-13 09:38:11'],
                        ['amount' => 3000, 'date' => '2021-05-13 18:26:57'],
                    ],
                    'hello' => 'moto',
                ],
                'contactPhone' => '+3355',
                'contactWorkPhone' => '+3366',
            ]),
            'expectedParsedContactPhone' => null,
            'expectedParsedContactWorkPhone' => null,
            'expectedCode' => Response::HTTP_OK,
        ];
    }

    /**
     * @dataProvider provideEditContact
     */
    public function testEditContact(string $json, ?string $expectedParsedContactPhone, ?string $expectedParsedContactWorkPhone, int $expectedCode)
    {
        $client = self::createClient();

        $expectedData = Json::decode($json);

        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => static::CITIPO_ORG]);

        /** @var Contact $oldContact */
        $oldContact = clone static::getContainer()->get(ContactRepository::class)->findOneByAnyEmail($orga, $expectedData['email']);

        $response = $this->apiRequest($client, 'POST', '/api/community/contacts', self::CITIPO_TOKEN, $expectedCode, $json);

        /** @var Contact $newContact */
        $newContact = static::getContainer()->get(ContactRepository::class)->findOneByAnyEmail($orga, $expectedData['email']);

        // Fetch the tags association changes
        static::getContainer()->get(EntityManagerInterface::class)->refresh($newContact);

        $this->assertNotSame($oldContact->getProfileLastName(), $newContact->getProfileLastName());
        $this->assertNotSame($oldContact->getProfileNationality(), $newContact->getProfileNationality());
        $this->assertNotSame($oldContact->getAddressZipCode(), $newContact->getAddressZipCode());
        $this->assertNotSame($oldContact->getArea(), $newContact->getArea());

        // Check tags were added
        $contactTags = $newContact->getMetadataTags()->getValues();
        $this->assertCount(2 + count($expectedData['metadataTags']), $contactTags);

        foreach (array_merge(['ExampleTag', 'StartWithTag'], $expectedData['metadataTags']) as $key => $tag) {
            $this->assertSame($tag, $contactTags[$key]->getName());
        }

        // Check custom fields were merged
        $expected = [
            'externalId' => '2485c2e31af5',
            'donations' => [
                ['amount' => 1000, 'date' => '2021-04-28 15:03:42'],
                ['amount' => 2000, 'date' => '2021-05-13 09:38:11'],
                ['amount' => 3000, 'date' => '2021-05-13 18:26:57'],
            ],
            'hello' => 'moto',
        ];
        $this->assertSame($expected, $newContact->getMetadataCustomFields());

        $this->assertSame($expectedParsedContactPhone, $response['parsedContactPhone']);
        $this->assertSame($expectedParsedContactWorkPhone, $response['parsedContactWorkPhone']);

        if ($response['parsedContactPhone']) {
            $this->assertSame($response['parsedContactPhone'], $newContact->getContactPhone());
            $this->assertSame($response['parsedContactWorkPhone'], $newContact->getContactWorkPhone());
            $this->assertInstanceOf(PhoneNumber::class, $newContact->getParsedContactPhone());
            $this->assertInstanceOf(PhoneNumber::class, $newContact->getParsedContactWorkPhone());
        } else {
            $this->assertNull($newContact->getParsedContactPhone());
            $this->assertNull($newContact->getParsedContactWorkPhone());
        }

        /** @var TransportInterface $transport */
        $transport = static::getContainer()->get('messenger.transport.async_priority_low');
        $this->assertCount(2, $messages = $transport->get());
        $this->assertInstanceOf(RefreshContactStatsMessage::class, $messages[0]->getMessage());
        $this->assertInstanceOf(QuorumMessage::class, $messages[1]->getMessage());
    }

    public function testEditContactTagsOverride()
    {
        $client = self::createClient();

        $this->apiRequest($client, 'POST', '/api/community/contacts', self::CITIPO_TOKEN, 200, Json::encode([
            'email' => 'olivie.gregoire@gmail.com',
            'metadataTagsOverride' => ['ShouldOverride'],
            'metadataTags' => ['ShouldBeAdded'],
        ]));

        /** @var Contact $contact */
        $contact = static::getContainer()->get(ContactRepository::class)->findOneBy(['email' => 'olivie.gregoire@gmail.com']);

        // Fetch the tags association changes
        static::getContainer()->get(EntityManagerInterface::class)->refresh($contact);

        // Check tags were overriden / added
        $this->assertSame(['ShouldBeAdded', 'ShouldOverride'], $contact->getMetadataTagsNames());
    }

    public function testGdprSettings()
    {
        $client = self::createClient();
        $contact = static::getContainer()->get(ContactRepository::class)->findOneBy(['email' => 'brunella.courtemanche2@orange.fr']);
        $this->assertCount(0, $contact->getSettingsByProject());
        $this->assertTrue($contact->hasSettingsReceiveNewsletters());
        $this->assertTrue($contact->hasSettingsReceiveSms());
        $this->assertFalse($contact->hasSettingsReceiveCalls());

        $result = $this->apiRequest($client, 'GET', '/api/community/contacts/43MAmriw076a19EWSo79m9', self::CITIPO_TOKEN, 200);
        $this->assertCount(4, $result['settingsByProject']);
        foreach ($result['settingsByProject'] as $settingByProject) {
            $this->assertSame($settingByProject['settingsReceiveNewsletters'], $contact->hasSettingsReceiveNewsletters());
            $this->assertSame($settingByProject['settingsReceiveSms'], $contact->hasSettingsReceiveSms());
            $this->assertSame($settingByProject['settingsReceiveCalls'], $contact->hasSettingsReceiveCalls());
        }

        // settingsByProject are always refreshed when displayed
        $result = $this->apiRequest($client, 'POST', '/api/community/contacts', self::CITIPO_TOKEN, 200, Json::encode([
            'email' => 'brunella.courtemanche2@orange.fr',
            'settingsReceiveNewsletters' => true,
            'settingsReceiveSms' => false,
            'settingsReceiveCalls' => false,
            'settingsByProject' => [],
        ]));
        $contact = static::getContainer()->get(ContactRepository::class)->findOneBy(['email' => 'brunella.courtemanche2@orange.fr']);
        $this->assertTrue($result['settingsReceiveNewsletters']);
        $this->assertFalse($result['settingsReceiveSms']);
        $this->assertFalse($result['settingsReceiveCalls']);
        $this->assertSame([], $result['settingsByProject']);

        foreach ($result['settingsByProject'] as $settingByProject) {
            $this->assertSame($settingByProject['settingsReceiveNewsletters'], $contact->hasSettingsReceiveNewsletters());
            $this->assertSame($settingByProject['settingsReceiveSms'], $contact->hasSettingsReceiveSms());
            $this->assertSame($settingByProject['settingsReceiveCalls'], $contact->hasSettingsReceiveCalls());
        }

        // settingsByProject are always refreshed when displayed
        $result = $this->apiRequest($client, 'POST', '/api/community/contacts', self::CITIPO_TOKEN, 200, Json::encode([
            'email' => 'brunella.courtemanche2@orange.fr',
            'settingsReceiveNewsletters' => false,
            'settingsReceiveCalls' => false,
            'settingsReceiveSms' => true,
            'settingsByProject' => [
                [
                    'projectId' => '73wbsDqmHaMWAizkLPKOSF',
                    'projectName' => 'Citipo',
                    'settingsReceiveNewsletters' => true,
                    'settingsReceiveCalls' => false,
                    'settingsReceiveSms' => false,
                ],
                [
                    'projectId' => 'Bejht60OqDGdVNPKhkGRt',
                    'projectName' => 'ExampleTag',
                    'settingsReceiveNewsletters' => true,
                    'settingsReceiveCalls' => false,
                    'settingsReceiveSms' => false,
                ],
                [
                    'projectId' => 'dr2UodGsKnDo8ewJvox3X',
                    'projectName' => 'Île-de-France',
                    'settingsReceiveNewsletters' => true,
                    'settingsReceiveCalls' => false,
                    'settingsReceiveSms' => false,
                ],
                [
                    'projectId' => '2zBjyjR0XXlk2Jpmpa7BYZ',
                    'projectName' => 'Trial',
                    'settingsReceiveNewsletters' => true,
                    'settingsReceiveCalls' => false,
                    'settingsReceiveSms' => false,
                ],
            ],
        ]));

        // true should be enforced by global settingsReceiveSms
        foreach ($result['settingsByProject'] as $settingByProject) {
            $this->assertTrue($settingByProject['settingsReceiveSms']);
            $this->assertTrue($settingByProject['settingsReceiveNewsletters']);
        }

        // Should be persisted, and true should be enforced by global settingsReceiveSms
        $contact = static::getContainer()->get(ContactRepository::class)->findOneBy(['email' => 'brunella.courtemanche2@orange.fr']);
        $this->assertSame(
            [
                [
                    'projectId' => '73wbsDqmHaMWAizkLPKOSF',
                    'projectName' => 'Citipo',
                    'settingsReceiveNewsletters' => true,
                    'settingsReceiveCalls' => false,
                    'settingsReceiveSms' => true,
                ],
                [
                    'projectId' => 'Bejht60OqDGdVNPKhkGRt',
                    'projectName' => 'ExampleTag',
                    'settingsReceiveNewsletters' => true,
                    'settingsReceiveCalls' => false,
                    'settingsReceiveSms' => true,
                ],
                [
                    'projectId' => 'dr2UodGsKnDo8ewJvox3X',
                    'projectName' => 'Île-de-France',
                    'settingsReceiveNewsletters' => true,
                    'settingsReceiveCalls' => false,
                    'settingsReceiveSms' => true,
                ],
                [
                    'projectId' => '2zBjyjR0XXlk2Jpmpa7BYZ',
                    'projectName' => 'Trial',
                    'settingsReceiveNewsletters' => true,
                    'settingsReceiveCalls' => false,
                    'settingsReceiveSms' => true,
                ],
            ],
            $contact->getSettingsByProject()
        );

        // Updating a project name should be reflected in contacts settings
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['name' => 'Île-de-France']);
        $project->setName('Île-de-France-Modified');
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->persist($project);
        $em->flush();

        $result = $this->apiRequest($client, 'GET', '/api/community/contacts/43MAmriw076a19EWSo79m9', self::CITIPO_TOKEN, 200);
        $this->assertCount(3, $result['settingsByProject']);
        foreach ($result['settingsByProject'] as $settingByProject) {
            if ('dr2UodGsKnDo8ewJvox3X' === $settingByProject['projectId']) {
                $this->assertSame('Île-de-France-Modified', $settingByProject['projectName']);
                break;
            }
        }
    }

    public function provideInvalidContact()
    {
        yield 'invalidEmail' => [
            'json' => Json::encode(['email' => 'invalid', 'profileFirstName' => 'John', 'profileLastName' => 'DOE']),
            'expectedCode' => 400,
        ];

        yield 'invalidJSON' => [
            'json' => 'ninJSON',
            'expectedCode' => 400,
        ];

        yield 'wrongBirthdate' => [
            'json' => Json::encode(['email' => 'new@email.com', 'profileBirthdate' => 'The same day as mine']),
            'expectedCode' => 400,
        ];

        yield 'wrongTags' => [
            'json' => Json::encode(['email' => 'new@email.com', 'metadataTags' => 'MyTag']),
            'expectedCode' => 400,
        ];

        yield 'wrongAdditionalEmails' => [
            'json' => Json::encode(['email' => 'new@email.com', 'contactAdditionalEmails' => 'john@somewhere.com, doe@somehting.com']),
            'expectedCode' => 400,
        ];

        yield 'invalidAdditionalEmails' => [
            'json' => Json::encode(['email' => 'new@email.com', 'contactAdditionalEmails' => ['invalid']]),
            'expectedCode' => 400,
        ];

        yield 'wrongCustomFields' => [
            'json' => Json::encode(['email' => 'new@email.com', 'metadataCustomFields' => 'this is not an array']),
            'expectedCode' => 400,
        ];
    }

    /**
     * @dataProvider provideInvalidContact
     */
    public function testInvalidContact(string $json, int $expectedCode)
    {
        $client = self::createClient();
        $this->apiRequest($client, 'POST', '/api/community/contacts', self::CITIPO_TOKEN, $expectedCode, $json);
    }

    public function testStatusNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/community/contacts/status/olivie.gregoire@gmail.com', null, 401);
    }

    public function testStatusInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/community/contacts/status/olivie.gregoire@gmail.com', 'invalid', 401);
    }

    public function provideStatus()
    {
        yield 'valid_contact' => [
            'email' => 'olivie.gregoire@gmail.com',
            'expectedStatus' => 'contact',
            'expectedId' => '104SKb9m0xnYyt8OiWn3ks',
        ];

        yield 'valid_contact_case_insensitive' => [
            'email' => 'Olivie.Gregoire@gmail.com ',
            'expectedStatus' => 'contact',
            'expectedId' => '104SKb9m0xnYyt8OiWn3ks',
        ];

        yield 'valid_member' => [
            'email' => 'a.compagnon@protonmail.com',
            'expectedStatus' => 'member',
            'expectedId' => '1jInRLnYMgt9oAuQVQbTGK',
        ];

        yield 'invalid' => [
            'email' => 'invalid@gmail.com',
            'expectedStatus' => 'not_found',
            'expectedId' => null,
        ];
    }

    /**
     * @dataProvider provideStatus
     */
    public function testStatus(string $email, string $expectedStatus, ?string $expectedId)
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/community/contacts/status/'.$email, self::CITIPO_TOKEN);
        $this->assertSame('ContactStatus', $result['_resource']);
        $this->assertSame($expectedStatus, $result['status']);
        $this->assertSame($expectedId, $result['id']);
    }

    public function testViewNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/community/contacts/43MAmriw076a19EWSo79m9', null, 401);
    }

    public function testViewInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/community/contacts/43MAmriw076a19EWSo79m9', 'invalid', 401);
    }

    public function testViewNotFound()
    {
        $client = self::createClient();

        $this->apiRequest($client, 'GET', '/api/community/contacts/invalid', self::CITIPO_TOKEN, 404);
        $this->apiRequest($client, 'GET', '/api/community/contacts/7rnedzqzqk0hv5ktdm3a1m', self::CITIPO_TOKEN, 404);
    }

    public function testView()
    {
        $client = self::createClient();

        $contact = $this->apiRequest($client, 'GET', '/api/community/contacts/43MAmriw076a19EWSo79m9', self::CITIPO_TOKEN);

        // Test mapping
        $this->assertApiResponse($contact, [
            '_resource' => 'Contact',
            'id' => '43MAmriw076a19EWSo79m9',
            'email' => 'brunella.courtemanche2@orange.fr',
            'picture' => 'https://www.gravatar.com/avatar/695e8c488e4bccae0e09e977a68e77ce?d=mp&s=800',
            'isMember' => true,
            'profileFormalTitle' => null,
            'profileFirstName' => 'Brunella',
            'profileMiddleName' => null,
            'profileLastName' => 'Courtemanche',
            'profileBirthdate' => null,
            'profileGender' => null,
            'profileNationality' => null,
            'profileCompany' => null,
            'profileJobTitle' => null,
            'contactPhone' => '+33 7 57 59 20 64',
            'contactWorkPhone' => null,
            'parsedContactPhone' => '+33 7 57 59 20 64',
            'parsedContactWorkPhone' => null,
            'socialFacebook' => null,
            'socialTwitter' => null,
            'socialLinkedIn' => 'brunella.courtemanche',
            'socialTelegram' => 'someid',
            'socialWhatsapp' => '+33600000001',
            'addressStreetLine1' => null,
            'addressStreetLine2' => null,
            'addressZipCode' => null,
            'addressCity' => null,
            'addressCountry' => null,
            'settingsReceiveNewsletters' => true,
            'settingsReceiveSms' => true,
            'settingsReceiveCalls' => false,
            'settingsByProject' => [],
            'metadataTags' => ['StartWithTag'],
            'metadataCustomFields' => [],
        ]);
    }

    public function testUpdatePicture()
    {
        $this->createApiRequest('POST', '/api/community/contacts/43MAmriw076a19EWSo79m9/picture')
            ->withFile('picture', new UploadedFile(__DIR__.'/../../../Fixtures/upload/picture.jpg', 'picture.jpg'))
            ->withApiToken(self::CITIPO_TOKEN)
            ->send()
        ;

        $this->assertResponseIsSuccessful();

        $contact = self::getContainer()->get(ContactRepository::class)->findOneByBase62Uid('43MAmriw076a19EWSo79m9');
        $this->assertNotNull($contact->getPicture());
    }

    public function provideUpdateEmail()
    {
        yield ['20e51b91-bdec-495d-854d-85d6e74fc75e', 'olivie.gregoire@gmail.com', 'maureenroussel@rhyta.com'];
    }

    /**
     * @dataProvider provideUpdateEmail
     */
    public function testUpdateEmail(string $id, string $oldEmail, string $newEmail)
    {
        $client = self::createClient();

        $contactRepo = static::getContainer()->get(ContactRepository::class);

        /** @var Contact $contact */
        $contact = $contactRepo->findOneBy(['uuid' => $id]);

        $this->assertSame($contact->getEmail(), $oldEmail);

        $this->apiRequest(
            $client,
            'POST',
            '/api/community/contacts/'.Uid::toBase62($contact->getUuid()).'/email',
            self::CITIPO_TOKEN,
            200,
            Json::encode(['newEmail' => $newEmail])
        );

        $contact = $contactRepo->findOneBy(['uuid' => $id]);
        $this->assertSame($contact->getEmail(), $newEmail);
    }

    public function provideUnregister()
    {
        yield ['20e51b91-bdec-495d-854d-85d6e74fc75e'];
    }

    /**
     * @dataProvider provideUnregister
     */
    public function testUnregister(string $id)
    {
        $client = self::createClient();

        $contactRepo = static::getContainer()->get(ContactRepository::class);

        /** @var Contact $contact */
        $contact = $contactRepo->findOneBy(['uuid' => $id]);

        $this->assertSame('219025aa-7fe2-4385-ad8f-31f386720d10', (string) $contact->getOrganization()->getUuid());
        $this->assertSame('olivie.gregoire@gmail.com', $contact->getEmail());
        $this->assertSame('contact-picture.jpg', $contact->getPicture()->getPathname());
        $this->assertSame('+33 7 57 59 46 25', $contact->getContactPhone());
        $this->assertSame('Olivie', $contact->getProfileFirstName());
        $this->assertSame('Gregoire', $contact->getProfileLastName());
        $this->assertTrue($contact->hasSettingsReceiveNewsletters());
        $this->assertTrue($contact->hasSettingsReceiveSms());
        $this->assertSame('olivie.gregoire', $contact->getSocialFacebook());
        $this->assertSame('@golivie92', $contact->getSocialTwitter());

        $this->apiRequest(
            $client,
            'POST',
            '/api/community/contacts/'.Uid::toBase62($contact->getUuid()).'/unregister',
            self::CITIPO_TOKEN,
            200
        );

        $contact = $contactRepo->findOneBy(['uuid' => $id]);

        $this->assertSame('219025aa-7fe2-4385-ad8f-31f386720d10', (string) $contact->getOrganization()->getUuid());
        $this->assertSame('olivie.gregoire@gmail.com', $contact->getEmail());
        $this->assertNull($contact->getPicture());
        $this->assertSame('+33 7 57 59 46 25', $contact->getContactPhone());
        $this->assertNull($contact->getProfileFirstName());
        $this->assertNull($contact->getProfileLastName());
        $this->assertFalse($contact->hasSettingsReceiveNewsletters());
        $this->assertFalse($contact->hasSettingsReceiveSms());
        $this->assertNull($contact->getSocialFacebook());
        $this->assertNull($contact->getSocialTwitter());
    }

    public function provideUnregisterMember()
    {
        yield ['da362047-7abd-40c9-8537-3d3506cb5cdb'];
    }

    /**
     * @dataProvider provideUnregisterMember
     */
    public function testUnregisterMember(string $id)
    {
        $client = self::createClient();

        $contactRepo = static::getContainer()->get(ContactRepository::class);

        /** @var Contact $contact */
        $contact = $contactRepo->findOneBy(['uuid' => $id]);
        $contactUpdateRepo = static::getContainer()->get(ContactUpdateRepository::class);

        $this->assertSame('cbeb774c-284c-43e3-923a-5a2388340f91', (string) $contact->getOrganization()->getUuid());
        $this->assertSame('troycovillon@teleworm.us', $contact->getEmail());
        $this->assertSame('password', $contact->getAccountPassword());
        $this->assertSame(0, $contactUpdateRepo->count([]));

        $this->apiRequest(
            $client,
            'POST',
            '/api/community/contacts/'.Uid::toBase62($contact->getUuid()).'/unregister',
            '31cf08f5e0354198a3b26b5b08f59a4ed871cbaec6e4eb8b158fab57a7193b7a',
            200
        );

        $contact = $contactRepo->findOneBy(['uuid' => $id]);

        $this->assertSame('cbeb774c-284c-43e3-923a-5a2388340f91', (string) $contact->getOrganization()->getUuid());
        $this->assertSame('troycovillon@teleworm.us', $contact->getEmail());
        $this->assertSame('password', $contact->getAccountPassword());
        $this->assertSame(1, $contactUpdateRepo->count([]));
    }

    public function provideUpdateEmailMember()
    {
        yield ['da362047-7abd-40c9-8537-3d3506cb5cdb', 'troycovillon@teleworm.us', 'fabiennelacasse@teleworm.us'];
    }

    /**
     * @dataProvider provideUpdateEmailMember
     */
    public function testUpdateEmailMember(string $id, string $email, string $newEmail)
    {
        $client = self::createClient();

        $contactRepo = static::getContainer()->get(ContactRepository::class);
        $contactUpdateRepo = static::getContainer()->get(ContactUpdateRepository::class);

        /** @var Contact $contact */
        $contact = $contactRepo->findOneBy(['uuid' => $id]);

        $this->assertSame($email, $contact->getEmail());
        $this->assertSame(0, $contactUpdateRepo->count([]));

        $this->apiRequest(
            $client,
            'POST',
            '/api/community/contacts/'.Uid::toBase62($contact->getUuid()).'/email',
            '31cf08f5e0354198a3b26b5b08f59a4ed871cbaec6e4eb8b158fab57a7193b7a',
            200,
            Json::encode(['newEmail' => $newEmail])
        );

        $contact = $contactRepo->findOneBy(['uuid' => $id]);
        $contactUpdate = $contactUpdateRepo->findOneBy([]);

        $this->assertSame(1, $contactUpdateRepo->count([]));
        $this->assertSame($contact->getEmail(), $email);
        $this->assertSame($contactUpdate->getEmail(), $newEmail);
    }

    public function testConfirmUpdateEmail()
    {
        $client = self::createClient();

        $contactUuid = '20e51b91-bdec-495d-854d-85d6e74fc75e';
        $contactUpdate = $this->createContactUpdate([
            'contact' => $contactUuid,
            'email' => 'confirm.email@citipo.com',
            'token' => 'token',
            'requestedAt' => new \DateTime(),
        ]);

        static::getContainer()->get(EntityManagerInterface::class)->flush();

        $contactRepo = static::getContainer()->get(ContactRepository::class);
        $contactUpdateRepo = static::getContainer()->get(ContactUpdateRepository::class);

        $contact = $contactRepo->findOneBy(['uuid' => $contactUuid]);

        $this->assertSame(1, $contactUpdateRepo->count([]));
        $this->assertSame('olivie.gregoire@gmail.com', $contact->getEmail());

        $id = Uid::toBase62($contactUpdate->getUuid());
        $this->apiRequest($client, 'POST', "/api/community/contacts/confirm/$id/{$contactUpdate->getToken()}/email", self::CITIPO_TOKEN, 200);

        $contact = $contactRepo->findOneBy(['uuid' => $contactUuid]);

        $this->assertSame(0, $contactUpdateRepo->count([]));
        $this->assertSame('confirm.email@citipo.com', $contact->getEmail());
    }

    public function testConfirmUnregister()
    {
        $client = self::createClient();

        $contactUuid = '20e51b91-bdec-495d-854d-85d6e74fc75e';
        $contactUpdate = $this->createContactUpdate([
            'contact' => $contactUuid,
            'token' => 'token',
            'requestedAt' => new \DateTime(),
            'type' => 'unregister',
        ]);

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->persist($contactUpdate);
        $em->flush();

        $contactRepo = static::getContainer()->get(ContactRepository::class);
        $contactUpdateRepo = static::getContainer()->get(ContactUpdateRepository::class);

        $contact = $contactRepo->findOneBy(['uuid' => $contactUuid]);
        $contactUpdate = $contactUpdateRepo->findOneBy([]);

        $this->assertSame('219025aa-7fe2-4385-ad8f-31f386720d10', (string) $contact->getOrganization()->getUuid());
        $this->assertSame('olivie.gregoire@gmail.com', $contact->getEmail());
        $this->assertSame('contact-picture.jpg', $contact->getPicture()->getPathname());
        $this->assertSame('+33 7 57 59 46 25', $contact->getContactPhone());
        $this->assertSame('Olivie', $contact->getProfileFirstName());
        $this->assertSame('Gregoire', $contact->getProfileLastName());
        $this->assertTrue($contact->hasSettingsReceiveNewsletters());
        $this->assertTrue($contact->hasSettingsReceiveSms());
        $this->assertSame('olivie.gregoire', $contact->getSocialFacebook());
        $this->assertSame('@golivie92', $contact->getSocialTwitter());
        $this->assertSame(1, $contactUpdateRepo->count([]));

        $id = Uid::toBase62($contactUpdate->getUuid());
        $this->apiRequest($client, 'POST', "/api/community/contacts/confirm/$id/{$contactUpdate->getToken()}/unregister", self::CITIPO_TOKEN, 200);

        $contact = $contactRepo->findOneBy(['uuid' => $contactUuid]);

        $this->assertSame('219025aa-7fe2-4385-ad8f-31f386720d10', (string) $contact->getOrganization()->getUuid());
        $this->assertSame('olivie.gregoire@gmail.com', $contact->getEmail());
        $this->assertNull($contact->getPicture());
        $this->assertSame('+33 7 57 59 46 25', $contact->getContactPhone());
        $this->assertNull($contact->getProfileFirstName());
        $this->assertNull($contact->getProfileLastName());
        $this->assertFalse($contact->hasSettingsReceiveNewsletters());
        $this->assertFalse($contact->hasSettingsReceiveSms());
        $this->assertNull($contact->getSocialFacebook());
        $this->assertNull($contact->getSocialTwitter());
        $this->assertSame(0, $contactUpdateRepo->count([]));
    }

    public function provideInvalidUpdateEmailConfirm()
    {
        yield 'contact_not_found' => [
            'exceptedCode' => '404',
            'contactUpdate' => [
                'contact' => '20e51b91-bdec-495d-854d-85d6e74fc75e',
                'email' => 'contact@email.com',
                'token' => 'token',
            ],
            'pathParams' => ['id' => '1SdcpWIGWADp8JrAKAMBvc'],
        ];

        yield 'contact_another_organization' => [
            'exceptedCode' => '403',
            'contactUpdate' => [
                'contact' => '75d245dd-c844-4ee7-8f12-a3d611a308b6',
                'email' => 'contact@email.com',
                'token' => 'token',
            ],
            'pathParams' => [],
        ];

        yield 'contact_another_type' => [
            'exceptedCode' => '403',
            'contactUpdate' => [
                'contact' => '20e51b91-bdec-495d-854d-85d6e74fc75e',
                'email' => 'contact@email.com',
                'type' => 'unregister',
                'token' => 'token',
            ],
            'pathParams' => [],
        ];

        yield 'contact_invalid_token' => [
            'exceptedCode' => '403',
            'contactUpdate' => [
                'contact' => '20e51b91-bdec-495d-854d-85d6e74fc75e',
                'email' => 'contact@email.com',
                'type' => 'email',
                'token' => 'token',
            ],
            'pathParams' => ['token' => 'token_invalid'],
        ];

        $dateTimeToken = new \DateTime();
        $dateTimeToken->sub(new \DateInterval('P3D'));

        yield 'contact_token_expired' => [
            'exceptedCode' => '403',
            'contactUpdate' => [
                'contact' => '20e51b91-bdec-495d-854d-85d6e74fc75e',
                'email' => 'contact@email.com',
                'type' => 'email',
                'token' => 'token',
                'requestedAt' => $dateTimeToken,
            ],
            'pathParams' => [],
        ];
    }

    /**
     * @dataProvider provideInvalidUpdateEmailConfirm
     */
    public function testUpdateEmailConfirmException($exceptedCode, array $contactUpdate, array $pathParams = [])
    {
        $client = self::createClient();

        $contactUpdate = $this->createContactUpdate($contactUpdate);

        static::getContainer()->get(EntityManagerInterface::class)->flush();

        $id = $pathParams['id'] ?? Uid::toBase62($contactUpdate->getUuid());
        $token = $pathParams['token'] ?? $contactUpdate->getToken();

        $this->apiRequest($client, 'POST', "/api/community/contacts/confirm/$id/$token/email", self::CITIPO_TOKEN, $exceptedCode);
    }

    private function createContactUpdate(array $fixture)
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $repositoryContact = static::getContainer()->get(ContactRepository::class);

        $fixture['contact'] = $repositoryContact->findOneBy(['uuid' => $fixture['contact']]);
        $contactUpdate = ContactUpdate::createFixture($fixture);

        $entityManager->persist($contactUpdate);

        return $contactUpdate;
    }
}
