<?php

namespace App\Tests\Controller\Api\Website;

use App\Analytics\Consumer\RefreshContactStatsMessage;
use App\Bridge\Quorum\Consumer\QuorumMessage;
use App\Bridge\Sendgrid\Consumer\SendgridMessage;
use App\Entity\Community\Contact;
use App\Entity\Organization;
use App\Entity\Website\FormAnswer;
use App\Repository\Community\ContactRepository;
use App\Repository\Community\EmailAutomationMessageRepository;
use App\Repository\Community\EmailAutomationRepository;
use App\Repository\OrganizationRepository;
use App\Repository\Website\FormAnswerRepository;
use App\Tests\ApiTestCase;
use App\Util\Json;
use Doctrine\ORM\EntityManagerInterface;
use SendGrid\Mail\To;
use Symfony\Component\Messenger\Transport\TransportInterface;

class FormControllerTest extends ApiTestCase
{
    public function testListAll()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/forms', self::ACME_TOKEN);
        $this->assertCount(4, $result['data']);

        // Test blocks are not included in the payload
        $this->assertArrayNotHasKey('blocks', $result['data'][0]);

        // Test the payload is the one expected
        $this->assertApiResponse($result, [
            'data' => [
                [
                    '_resource' => 'Form',
                    '_links' => [
                        'self' => 'http://localhost/api/website/forms/4x0UjLrg8RJYHWTY9ZyCWs',
                    ],
                    'id' => '4x0UjLrg8RJYHWTY9ZyCWs',
                    'title' => 'Our Sustainable Europe',
                    'slug' => 'our-sustainable-europe',
                    'description' => '15 questions for a greener Europe',
                    'proposeNewsletter' => true,
                    'phoningCampaignId' => null,
                ],
                [
                    'title' => 'Form propose newsletter without mail',
                    'proposeNewsletter' => true,
                ],
                [
                    'title' => 'Form don\'t propose newsletter',
                    'proposeNewsletter' => false,
                ],
                [
                    'title' => 'Form with filtered alert',
                    'proposeNewsletter' => false,
                ],
            ],
        ]);
    }

    public function testListNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/forms', null, 401);
    }

    public function testListInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/forms', 'invalid', 401);
    }

    public function testViewFullMapping()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/forms/4x0UjLrg8RJYHWTY9ZyCWs', self::ACME_TOKEN);

        // Test the payload is the one expected, including post content
        $this->assertApiResponse($result, [
            '_resource' => 'Form',
            '_links' => [
                'self' => 'http://localhost/api/website/forms/4x0UjLrg8RJYHWTY9ZyCWs',
            ],
            'id' => '4x0UjLrg8RJYHWTY9ZyCWs',
            'title' => 'Our Sustainable Europe',
            'slug' => 'our-sustainable-europe',
            'description' => '15 questions for a greener Europe',
            'proposeNewsletter' => true,
            'phoningCampaignId' => null,
            'redirectUrl' => 'https://example.com',
            'blocks' => [
                'data' => [
                    [
                        '_resource' => 'FormBlock',
                        'type' => 'html',
                        'content' => '<iframe width="560" height="315" src="https://www.youtube.com/embed/R0FSDrTjSuw" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
                    ],
                    [
                        'type' => 'paragraph',
                        'content' => 'To gather the views of young people on environmental challenges as well as on Europe’s ecological policy',
                    ],
                    [
                        'type' => 'paragraph',
                        'content' => 'To assess the youth’s readiness to undertake meaningful changes in order to increase the sustainability of their way of life',
                    ],
                    [
                        'type' => 'paragraph',
                        'content' => 'To shape policy proposals based upon data gathered',
                    ],
                    [
                        'type' => 'header',
                        'content' => 'Introductory section',
                    ],
                    ['type' => 'text', 'content' => 'Age', 'required' => true],
                    [
                        'type' => 'radio',
                        'content' => 'Occupation',
                        'required' => true,
                        'config' => [
                            'choices' => ['Student', 'Employed', 'Unemployed'],
                        ],
                    ],
                    [
                        'type' => 'radio',
                        'content' => 'Gender',
                        'required' => true,
                        'config' => [
                            'choices' => ['Male', 'Female', 'Other'],
                        ],
                    ],

                    ['type' => 'header', 'content' => 'Survey'],
                    [
                        'type' => 'rating',
                        'content' => 'Would you be ready to change your transport habits for environmental reasons, even if this means a higher price or a longer commute ?',
                        'required' => true,
                        'config' => [
                            'choices' => [
                                'Strongly reluctant',
                                'Somewhat reluctant',
                                'Indifferent',
                                'Somewhat ready',
                                'Strongly ready',
                            ],
                        ],
                    ],
                    ['type' => 'textarea', 'content' => 'Do you have other ideas?'],

                    ['type' => 'header', 'content' => 'Topics'],
                    [
                        'type' => 'checkbox',
                        'content' => 'What topics interest you the most?',
                        'config' => [
                            'choices' => [
                                'Transport',
                                'Taxation',
                                'Energy',
                                'Agriculture',
                                'Education',
                            ],
                        ],
                    ],

                    ['type' => 'header', 'content' => 'When do you want to be contacted back?'],
                    ['type' => 'email', 'content' => 'Email', 'required' => true],
                    ['type' => 'formal_title', 'content' => 'Formal title', 'required' => true],
                    ['type' => 'firstname', 'content' => 'First name', 'required' => true],
                    ['type' => 'middlename', 'content' => 'Middle name', 'required' => true],
                    ['type' => 'lastname', 'content' => 'Last name', 'required' => true],
                    ['type' => 'birthdate', 'content' => 'Birthdate', 'required' => true],
                    ['type' => 'gender', 'content' => 'Gender', 'required' => true],
                    ['type' => 'nationality', 'content' => 'Nationality', 'required' => true],
                    ['type' => 'company', 'content' => 'Company'],
                    ['type' => 'job_title', 'content' => 'Job title'],
                    ['type' => 'phone', 'content' => 'Phone number'],
                    ['type' => 'work_phone', 'content' => 'Work phone number'],
                    ['type' => 'social_facebook', 'content' => 'Facebook URL'],
                    ['type' => 'social_twitter', 'content' => 'Twitter username'],
                    ['type' => 'social_linkedin', 'content' => 'LinkedIn URL'],
                    ['type' => 'social_telegram', 'content' => 'Telegram username'],
                    ['type' => 'social_whatsapp', 'content' => 'Whatsapp phone'],
                    ['type' => 'street_address', 'content' => 'Street address'],
                    ['type' => 'street_address_2', 'content' => 'Street address 2'],
                    ['type' => 'city', 'content' => 'City'],
                    ['type' => 'zip_code', 'content' => 'Zip code'],
                    ['type' => 'country', 'content' => 'Country'],
                    ['type' => 'tag_radio', 'content' => 'Automatic tags radio'],
                    ['type' => 'tag_checkbox', 'content' => 'Automatic tags checkbox'],
                    ['type' => 'tag_hidden', 'content' => 'Automatic tag (hidden)'],
                    ['type' => 'date', 'content' => 'Date'],
                    ['type' => 'time', 'content' => 'Time'],
                    ['type' => 'newsletter'],
                ],
            ],
        ]);
    }

    public function testViewNewsletterWithoutMail()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/forms/4m7erjV7BCZuVPMyrY2a6H', self::ACME_TOKEN);

        // Test the payload is the one expected, including post content
        $this->assertApiResponse($result, [
            'proposeNewsletter' => true,
            'blocks' => [
                'data' => [
                    ['type' => 'text', 'content' => 'Question'],
                    ['type' => 'email', 'required' => true],
                    ['type' => 'newsletter'],
                ],
            ],
        ]);
    }

    public function testViewNoNewsletter()
    {
        $client = self::createClient();

        $result = $this->apiRequest($client, 'GET', '/api/website/forms/3LdNrguFZQxHjHqYsYiBlr', self::ACME_TOKEN);

        // Test the payload is the one expected, including post content
        $this->assertApiResponse($result, [
            'proposeNewsletter' => false,
            'blocks' => [
                'data' => [
                    ['type' => 'text', 'content' => 'Question'],
                ],
            ],
        ]);
    }

    public function testViewNotFound()
    {
        $client = self::createClient();

        $this->apiRequest($client, 'GET', '/api/website/forms/invalid', self::EXAMPLECO_TOKEN, 404);
        $this->apiRequest($client, 'GET', '/api/website/forms/7rnedzqzqk0hv5ktdm3a1m', self::EXAMPLECO_TOKEN, 404);
    }

    public function testViewNoToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/forms/4m7erjV7BCZuVPMyrY2a6H', null, 401);
    }

    public function testViewInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'GET', '/api/website/forms/4m7erjV7BCZuVPMyrY2a6H', 'invalid', 401);
    }

    public function testViewOnlyForMembers()
    {
        $client = self::createClient();

        $this->apiRequest($client, 'GET', '/api/website/forms/6dNF6RigTfqLug4ojUrhMQ', self::ACME_TOKEN, 404);
    }

    public function testAnswerNewContactFullMapping()
    {
        $client = self::createClient();

        $response = $this->apiRequest($client, 'POST', '/api/website/forms/4x0UjLrg8RJYHWTY9ZyCWs/answer', self::ACME_TOKEN, 201, Json::encode([
            'fields' => [
                '25',
                'Employed',
                'Male',
                'Indifferent',
                'Test textarea',
                'Transport, Agriculture',
                'New@Email.com',
                'M.',
                'Titouan',
                'Antoine',
                'Galopin',
                '2000-06-01',
                'male',
                'be',
                'Citipo',
                'President',
                '06 06 06 06 06',
                '06 06 06 06 07',
                'https://facebook.com',
                'titouangalopin',
                'https://linkedin.com',
                'tgalopin',
                '06 06 06 06 06',
                '49 Rue de Ponthieu',
                'Etage 1',
                'CLICHY',
                '92110',
                'FR',
                'TagB',
                'TagB, TagF',
                '2020-01-01',
                '13:10',
                'ok',
                '1',
            ],
        ]));

        $this->assertArrayHasKey('id', $response);
        $this->assertNotEmpty($response['id']);

        /*
         * Check the contact
         */

        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => self::ACME_ORG]);

        /** @var Contact $contact */
        $contact = static::getContainer()->get(ContactRepository::class)->findOneBy([
            'email' => 'new@email.com',
            'organization' => $orga,
        ]);
        $this->assertInstanceOf(Contact::class, $contact);

        // Fetch the tags association changes
        static::getContainer()->get(EntityManagerInterface::class)->refresh($contact);

        $this->assertSame('new@email.com', $contact->getEmail());
        $this->assertSame('M.', $contact->getProfileFormalTitle());
        $this->assertSame('Titouan', $contact->getProfileFirstName());
        $this->assertSame('Antoine', $contact->getProfileMiddleName());
        $this->assertSame('Galopin', $contact->getProfileLastName());
        $this->assertSame('2000-06-01', $contact->getProfileBirthdate()->format('Y-m-d'));
        $this->assertSame('male', $contact->getProfileGender());
        $this->assertSame('BE', $contact->getProfileNationality());
        $this->assertSame('Citipo', $contact->getProfileCompany());
        $this->assertSame('President', $contact->getProfileJobTitle());
        $this->assertSame('+33 6 06 06 06 06', $contact->getContactPhone());
        $this->assertSame('+33 6 06 06 06 07', $contact->getContactWorkPhone());
        $this->assertSame('https://facebook.com', $contact->getSocialFacebook());
        $this->assertSame('titouangalopin', $contact->getSocialTwitter());
        $this->assertSame('https://linkedin.com', $contact->getSocialLinkedIn());
        $this->assertSame('tgalopin', $contact->getSocialTelegram());
        $this->assertSame('06 06 06 06 06', $contact->getSocialWhatsapp());
        $this->assertSame('49 Rue de Ponthieu', $contact->getAddressStreetLine1());
        $this->assertSame('Etage 1', $contact->getAddressStreetLine2());
        $this->assertSame('92110', $contact->getAddressZipCode());
        $this->assertSame('CLICHY', $contact->getAddressCity());
        $this->assertSame('36778547219895752', (string) $contact->getAddressCountry()->getId());
        $this->assertSame('39389989938296926', (string) $contact->getArea()->getId());
        $this->assertTrue($contact->hasSettingsReceiveNewsletters());

        /*
         * Check tags
         */
        $contactTags = array_values($contact->getMetadataTagsNames());
        sort($contactTags);

        $this->assertSame(['Acme Inc', 'TagB', 'TagF', 'TagG', 'TagH'], $contact->getMetadataTagsNames());

        /*
         * Check Quorum sync and stats refresh
         */

        $this->assertCount(2, $messages = static::getContainer()->get('messenger.transport.async_priority_low')->get());
        $this->assertInstanceOf(RefreshContactStatsMessage::class, $messages[0]->getMessage());
        $this->assertInstanceOf(QuorumMessage::class, $messages[1]->getMessage());

        // Payload mapping already checked by
        // App\Tests\Controller\Console\Project\Community\ContactControllerTest::testEdit

        /*
         * Check automation
         */

        /** @var EmailAutomationMessageRepository $automationMessageRepo */
        $automationMessageRepo = static::getContainer()->get(EmailAutomationMessageRepository::class);

        /** @var TransportInterface $transport */
        $transport = static::getContainer()->get('messenger.transport.async_emailing');

        // Check credits usage
        $this->assertSame(4998, $orga->getCreditsBalance()); // 5000 - 1 for contact - 1 for form

        // Check email automation messages
        $this->assertSame(2, $automationMessageRepo->count(['email' => 'contact@citipo.com']));

        $messages = $transport->get();
        $this->assertCount(2, $messages);

        // Check first message: new contact
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
            $to[] = array_map(static fn (To $to) => $to->getEmail(), $personalization->getTos());
        }

        $to = array_merge(...$to);
        sort($to);

        $this->assertSame(['contact@citipo.com'], $to);

        // Check first message: new form answer
        /** @var SendgridMessage $message */
        $message = $messages[1]->getMessage();
        $this->assertInstanceOf(SendgridMessage::class, $message);

        // Check the mail
        $mail = $message->getMail();
        $this->assertSame('New form answer alert', $mail->getGlobalSubject()->getSubject());
        $this->assertSame('contact@citipo.com', $mail->getFrom()->getEmail());
        $this->assertSame('Jacques BAUER', $mail->getFrom()->getName());
        $this->assertCount(1, $contents = $mail->getContents());
        $this->assertStringContainsString('Title: -form-title-', $contents[0]->getValue());

        $to = [];
        foreach ($mail->getPersonalizations() as $personalization) {
            $to[] = array_map(static fn (To $to) => $to->getEmail(), $personalization->getTos());
        }

        $to = array_merge(...$to);
        sort($to);

        $this->assertSame(['contact@citipo.com'], $to);

        /*
         * Check the answer
         */

        /** @var FormAnswer $answer */
        $answer = static::getContainer()->get(FormAnswerRepository::class)->findOneBy(['contact' => $contact]);

        $this->assertInstanceOf(FormAnswer::class, $answer);
        $this->assertSame($contact->getId(), $answer->getContact()->getId());

        $this->assertSame([
            'Age' => '25',
            'Occupation' => 'Employed',
            'Gender' => 'male',
            'Would you be ready to change your transport habits for environmental reasons, even if this means a higher price or a longer commute ?' => 'Indifferent',
            'Do you have other ideas?' => 'Test textarea',
            'What topics interest you the most?' => 'Transport, Agriculture',
            'Email' => 'New@Email.com',
            'Formal title' => 'M.',
            'First name' => 'Titouan',
            'Middle name' => 'Antoine',
            'Last name' => 'Galopin',
            'Birthdate' => '2000-06-01',
            'Nationality' => 'be',
            'Company' => 'Citipo',
            'Job title' => 'President',
            'Phone number' => '06 06 06 06 06',
            'Work phone number' => '06 06 06 06 07',
            'Facebook URL' => 'https://facebook.com',
            'Twitter username' => 'titouangalopin',
            'LinkedIn URL' => 'https://linkedin.com',
            'Telegram username' => 'tgalopin',
            'Whatsapp phone' => '06 06 06 06 06',
            'Street address' => '49 Rue de Ponthieu',
            'Street address 2' => 'Etage 1',
            'City' => 'CLICHY',
            'Zip code' => '92110',
            'Country' => 'FR',
            'Automatic tags radio' => 'TagB',
            'Automatic tags checkbox' => 'TagB, TagF',
            'Automatic tag (hidden)' => 'TagG, TagH',
            'Date' => '13:10',
            'Time' => 'ok',
            'Newsletter' => 'Yes',
        ], $answer->getAnswers());
    }

    public function testAnswerNewContactNewsletterWithoutMailDontSubscribe()
    {
        $client = self::createClient();

        $this->apiRequest($client, 'POST', '/api/website/forms/4m7erjV7BCZuVPMyrY2a6H/answer', self::ACME_TOKEN, 201, Json::encode([
            'fields' => [
                'Test',
                'New@Email.com',
                '0',
            ],
        ]));

        /*
         * Check the contact
         */

        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => self::ACME_ORG]);

        /** @var Contact $contact */
        $contact = static::getContainer()->get(ContactRepository::class)->findOneBy([
            'email' => 'new@email.com',
            'organization' => $orga,
        ]);

        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertSame('new@email.com', $contact->getEmail());
        $this->assertNull($contact->getProfileFirstName());
        $this->assertNull($contact->getProfileLastName());
        $this->assertNull($contact->getProfileCompany());
        $this->assertNull($contact->getProfileJobTitle());
        $this->assertNull($contact->getContactPhone());
        $this->assertNull($contact->getAddressZipCode());
        $this->assertNull($contact->getAddressCountry());
        $this->assertNull($contact->getArea());

        // Shouldn't update newsletter status
        $this->assertTrue($contact->hasSettingsReceiveNewsletters());

        /*
         * Check the answer
         */

        /** @var FormAnswer $answer */
        $answer = static::getContainer()->get(FormAnswerRepository::class)->findOneBy(['contact' => $contact]);

        $this->assertInstanceOf(FormAnswer::class, $answer);
        $this->assertSame($contact->getId(), $answer->getContact()->getId());
        $this->assertSame([
            'Question' => 'Test',
            'Email' => 'New@Email.com',
            'Newsletter' => 'No',
        ], $answer->getAnswers());
    }

    public function testAnswerNoContactNoNewsletter()
    {
        $client = self::createClient();

        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => self::ACME_ORG]);

        $countBefore = static::getContainer()->get(ContactRepository::class)->count([
            'organization' => $orga,
        ]);

        $this->apiRequest($client, 'POST', '/api/website/forms/3LdNrguFZQxHjHqYsYiBlr/answer', self::ACME_TOKEN, 201, Json::encode([
            'fields' => ['Test'],
        ]));

        /*
         * Check no contact was created
         */

        $this->assertSame($countBefore, static::getContainer()->get(ContactRepository::class)->count([
            'organization' => $orga,
        ]));

        /*
         * Check the answer
         */

        /** @var FormAnswer $answer */
        $answer = static::getContainer()->get(FormAnswerRepository::class)->findOneBy(['contact' => null]);

        $this->assertInstanceOf(FormAnswer::class, $answer);
        $this->assertNull($answer->getContact());
        $this->assertSame(['Question' => 'Test'], $answer->getAnswers());

        /*
         * Check automation for form answer
         */

        $automation = static::getContainer()->get(EmailAutomationRepository::class)->findOneBy(['uuid' => 'f2185892-9b3a-4eab-8a81-3520cded8571']);

        /** @var EmailAutomationMessageRepository $automationMessageRepo */
        $automationMessageRepo = static::getContainer()->get(EmailAutomationMessageRepository::class);

        /** @var TransportInterface $transport */
        $transport = static::getContainer()->get('messenger.transport.async_emailing');

        // Check credits usage
        $this->assertSame(4999, $orga->getCreditsBalance()); // 5000 - 1 for form

        // Check email automation messages
        $this->assertSame(1, $automationMessageRepo->count(['automation' => $automation]));

        $messages = $transport->get();
        $this->assertCount(1, $messages);

        /** @var SendgridMessage $message */
        $message = $messages[0]->getMessage();
        $this->assertInstanceOf(SendgridMessage::class, $message);

        // Check the mail
        $mail = $message->getMail();
        $this->assertSame('New form answer alert', $mail->getGlobalSubject()->getSubject());
        $this->assertSame('contact@citipo.com', $mail->getFrom()->getEmail());
        $this->assertSame('Jacques BAUER', $mail->getFrom()->getName());
        $this->assertCount(1, $contents = $mail->getContents());
        $this->assertStringContainsString('Title: -form-title-', $contents[0]->getValue());

        $to = [];
        foreach ($mail->getPersonalizations() as $personalization) {
            $to[] = array_map(static fn (To $to) => $to->getEmail(), $personalization->getTos());
        }

        $to = array_merge(...$to);
        sort($to);

        $this->assertSame(['contact@citipo.com'], $to);
    }

    public function testAnswerFilteredFormAlert()
    {
        $client = self::createClient();

        /** @var Organization $orga */
        $orga = static::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => self::ACME_ORG]);

        $countBefore = static::getContainer()->get(ContactRepository::class)->count([
            'organization' => $orga,
        ]);

        $this->apiRequest($client, 'POST', '/api/website/forms/sPcb6g4fGs7yDNuRsXte2/answer', self::ACME_TOKEN, 201, Json::encode([
            'fields' => ['Test'],
        ]));

        /*
         * Check no contact was created
         */

        $this->assertSame($countBefore, static::getContainer()->get(ContactRepository::class)->count([
            'organization' => $orga,
        ]));

        /*
         * Check the answer
         */

        /** @var FormAnswer $answer */
        $answer = static::getContainer()->get(FormAnswerRepository::class)->findOneBy(['contact' => null]);

        $this->assertInstanceOf(FormAnswer::class, $answer);
        $this->assertNull($answer->getContact());
        $this->assertSame(['Question' => 'Test'], $answer->getAnswers());

        /*
         * Check automation for form answer
         */

        $repo = static::getContainer()->get(EmailAutomationRepository::class);
        $globalAutomation = $repo->findOneBy(['uuid' => 'f2185892-9b3a-4eab-8a81-3520cded8571']);
        $specificAutomation = $repo->findOneBy(['uuid' => 'fc796193-d9ab-41b3-b19c-9f296b1f36a8']);

        /** @var EmailAutomationMessageRepository $automationMessageRepo */
        $automationMessageRepo = static::getContainer()->get(EmailAutomationMessageRepository::class);

        /** @var TransportInterface $transport */
        $transport = static::getContainer()->get('messenger.transport.async_emailing');

        // Check credits usage
        $this->assertSame(4998, $orga->getCreditsBalance()); // 5000 - 1 for global form - 1 for filtered form

        // Check email automation messages
        $this->assertSame(1, $automationMessageRepo->count(['automation' => $globalAutomation]));
        $this->assertSame(1, $automationMessageRepo->count(['automation' => $specificAutomation]));

        $messages = $transport->get();
        $this->assertCount(2, $messages);

        // Check the 1st mail
        /** @var SendgridMessage $message */
        $message = $messages[0]->getMessage();
        $this->assertInstanceOf(SendgridMessage::class, $message);

        $mail = $message->getMail();
        $this->assertSame('New form answer alert', $mail->getGlobalSubject()->getSubject());
        $this->assertSame('contact@citipo.com', $mail->getFrom()->getEmail());
        $this->assertSame('Jacques BAUER', $mail->getFrom()->getName());
        $this->assertCount(1, $contents = $mail->getContents());
        $this->assertStringContainsString('Title: -form-title-', $contents[0]->getValue());

        $to = [];
        foreach ($mail->getPersonalizations() as $personalization) {
            $to[] = array_map(static fn (To $to) => $to->getEmail(), $personalization->getTos());
        }

        $to = array_merge(...$to);
        sort($to);

        $this->assertSame(['contact@citipo.com'], $to);

        // Check the 2nd mail
        /** @var SendgridMessage $message */
        $message = $messages[1]->getMessage();
        $this->assertInstanceOf(SendgridMessage::class, $message);

        $mail = $message->getMail();
        $this->assertSame('Filtered form alert', $mail->getGlobalSubject()->getSubject());
        $this->assertSame('contact@citipo.com', $mail->getFrom()->getEmail());
        $this->assertSame('Jacques BAUER', $mail->getFrom()->getName());
        $this->assertCount(1, $contents = $mail->getContents());
        $this->assertStringContainsString('Title: -form-title-, answer: -form-answer-1-', $contents[0]->getValue());

        $to = [];
        foreach ($mail->getPersonalizations() as $personalization) {
            $to[] = array_map(static fn (To $to) => $to->getEmail(), $personalization->getTos());
        }

        $to = array_merge(...$to);
        sort($to);

        $this->assertSame(['contact@citipo.com'], $to);
    }

    public function testAnswerNotFound()
    {
        $client = self::createClient();

        $this->apiRequest($client, 'POST', '/api/website/forms/invalid/answer', self::ACME_TOKEN, 404, Json::encode([
            'fields' => [
                'Titouan',
                'Galopin',
                '25',
                'Employed',
                'Male',
                'Indifferent',
                'Test textarea',
                'Transport, Agriculture',
                'New@Email.com',
                '06 06 06 06 06',
                'FR',
                '92110',
                'Citipo',
                'President',
                '2020-01-01',
                '13:10',
                '1',
            ],
        ]));
    }

    public function testAnswerNoToken()
    {
        $this->apiRequest(self::createClient(), 'POST', '/api/website/forms/3LdNrguFZQxHjHqYsYiBlr/answer', null, 401);
    }

    public function testAnswerInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'POST', '/api/website/forms/3LdNrguFZQxHjHqYsYiBlr/answer', 'invalid', 401);
    }
}
