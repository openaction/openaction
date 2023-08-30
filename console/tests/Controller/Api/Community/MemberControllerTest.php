<?php

namespace App\Tests\Controller\Api\Community;

use App\Analytics\Consumer\RefreshContactStatsMessage;
use App\Bridge\Integromat\Consumer\IntegromatWebhookMessage;
use App\Bridge\Quorum\Consumer\QuorumMessage;
use App\Community\MemberAuthenticator;
use App\Entity\Community\Contact;
use App\Repository\Community\ContactRepository;
use App\Repository\OrganizationRepository;
use App\Tests\ApiTestCase;
use App\Util\Json;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MemberControllerTest extends ApiTestCase
{
    public function testRegisterProcess()
    {
        $client = self::createClient();

        /*
         * Register
         */

        $this->apiRequest($client, 'POST', '/api/community/contacts', self::CITIPO_TOKEN, Response::HTTP_OK, Json::encode([
            'email' => 'john.doe@citipo.com',
            'accountPassword' => 'password',
        ]));

        // Member should have been created
        /** @var Contact $member */
        $member = static::getContainer()->get(ContactRepository::class)->findOneBy([
            'organization' => static::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => self::CITIPO_ORG]),
            'email' => 'john.doe@citipo.com',
        ]);

        // Password should be valid
        $this->assertTrue(static::getContainer()->get(UserPasswordHasherInterface::class)->isPasswordValid($member, 'password'));

        // Confirmation email should have been sent
        $this->assertFalse($member->isAccountConfirmed());
        $this->assertNotNull($member->getAccountConfirmToken());

        $messages = static::getContainer()->get('messenger.transport.async_priority_high')->get();
        $this->assertCount(1, $messages);

        /** @var TemplatedEmail $mail */
        $mail = $messages[0]->getMessage()->getMessage();
        $this->assertSame('Confirm your email address', $mail->getSubject());

        $confirmReference = $mail->getContext()['reference'] ?? null;
        $this->assertNotNull($confirmReference);

        // Check stats
        $this->assertCount(3, $messages = static::getContainer()->get('messenger.transport.async_priority_low')->get());
        $this->assertInstanceOf(RefreshContactStatsMessage::class, $messages[0]->getMessage());
        $this->assertInstanceOf(IntegromatWebhookMessage::class, $messages[1]->getMessage());
        $this->assertInstanceOf(QuorumMessage::class, $messages[2]->getMessage());

        /*
         * Confirm registration
         */

        $this->apiRequest($client, 'POST', '/api/community/members/register/confirm/'.$confirmReference, self::CITIPO_TOKEN);

        // Member should have been confirmed
        /** @var Contact $member */
        $member = static::getContainer()->get(ContactRepository::class)->findOneBy([
            'organization' => static::getContainer()->get(OrganizationRepository::class)->findOneBy(['uuid' => self::CITIPO_ORG]),
            'email' => 'john.doe@citipo.com',
        ]);

        $this->assertTrue($member->isAccountConfirmed());
        $this->assertNull($member->getAccountConfirmToken());
    }

    public function testRegisterNoToken()
    {
        $this->apiRequest(self::createClient(), 'POST', '/api/community/contacts', null, 401);
    }

    public function testRegisterInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'POST', '/api/community/contacts', 'invalid', 401);
    }

    public function testRegisterConfirmInvalidContact()
    {
        $this->apiRequest(
            self::createClient(),
            'POST',
            '/api/community/members/register/confirm/invalid/invalid',
            self::CITIPO_TOKEN,
            404
        );
    }

    public function testRegisterConfirmInvalidContactToken()
    {
        $this->apiRequest(
            self::createClient(),
            'POST',
            '/api/community/members/register/confirm/43MAmriw076a19EWSo79m9/invalid',
            self::CITIPO_TOKEN,
            404
        );
    }

    public function testLoginValidMemberAuthorized()
    {
        $client = self::createClient();

        /*
         * Login
         */

        $token = $this->apiRequest($client, 'POST', '/api/community/members/login', self::CITIPO_TOKEN, 200, Json::encode([
            'email' => 'brunella.courtemanche2@orange.fr',
            'password' => 'password',
        ]));

        $this->assertSame('Brunella', $token['firstName']);
        $this->assertSame('Courtemanche', $token['lastName']);
        $this->assertNotNull($token['nonce']);
        $this->assertNotNull($token['encrypted']);

        /*
         * Authorize
         */

        $contact = $this->createApiRequest('POST', '/api/community/members/authorize', $client)
            ->withApiToken(self::CITIPO_TOKEN)
            ->withAuthToken(Json::encode($token))
            ->toArray()
        ;

        $this->assertResponseStatusCodeSame(200);

        // Test mapping
        $this->assertApiResponse($contact, [
            '_resource' => 'Contact',
            'id' => '43MAmriw076a19EWSo79m9',
            'email' => 'brunella.courtemanche2@orange.fr',
            'isMember' => true,
            'profileFormalTitle' => null,
            'profileFirstName' => 'Brunella',
            'profileMiddleName' => null,
            'profileLastName' => 'Courtemanche',
            'profileBirthdate' => null,
            'profileGender' => null,
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
        ]);
    }

    public function testLoginNoToken()
    {
        $this->apiRequest(self::createClient(), 'POST', '/api/community/members/login', null, 401);
    }

    public function testLoginInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'POST', '/api/community/members/login', 'invalid', 401);
    }

    public function testLoginNotJsonPayload()
    {
        $this->apiRequest(self::createClient(), 'POST', '/api/community/members/login', self::CITIPO_TOKEN, 400, 'invalid');
    }

    public function testLoginInvalidPayload()
    {
        $this->apiRequest(self::createClient(), 'POST', '/api/community/members/login', self::CITIPO_TOKEN, 400, Json::encode([
            'invalid' => 'payload',
        ]));
    }

    public function provideLoginInvalidCredentials()
    {
        yield 'invalid_email' => ['invalid@orange.fr', 'password'];
        yield 'invalid_password' => ['brunella.courtemanche2@orange.fr', 'invalid'];
        yield 'inactive' => ['a.compagnon@protonmail.com', 'password'];
    }

    /**
     * @dataProvider provideLoginInvalidCredentials
     */
    public function testLoginInvalidCredentials(string $email, string $password)
    {
        $client = self::createClient();

        $this->apiRequest($client, 'POST', '/api/community/members/login', self::CITIPO_TOKEN, 401, Json::encode([
            'email' => $email,
            'password' => $password,
        ]));
    }

    public function testAuthorizeInvalidPayload()
    {
        $client = self::createClient();

        // Invalid JSON
        $this->apiRequest($client, 'POST', '/api/community/members/authorize', self::CITIPO_TOKEN, 400);
        $this->apiRequest($client, 'POST', '/api/community/members/authorize', self::CITIPO_TOKEN, 400, 'invalid');

        // No encrypted
        $this->apiRequest($client, 'POST', '/api/community/members/authorize', self::CITIPO_TOKEN, 400, Json::encode([
            'nonce' => 'xxx',
        ]));

        // No nonce
        $this->apiRequest($client, 'POST', '/api/community/members/authorize', self::CITIPO_TOKEN, 400, Json::encode([
            'encrypted' => 'xxx',
        ]));
    }

    public function testAuthorizeExpiredToken()
    {
        $client = self::createClient();

        $contact = static::getContainer()->get(ContactRepository::class)->findOneBy(['uuid' => '8534f120-0342-46c9-aa3b-83f317335f35']);
        $token = static::getContainer()->get(MemberAuthenticator::class)->createAuthorizationToken($contact, '-30 minutes');

        $this->createApiRequest('POST', '/api/community/members/authorize', $client)
            ->withApiToken(self::CITIPO_TOKEN)
            ->withAuthToken(Json::encode([
                '_resource' => 'AuthorizationToken',
                'firstName' => $token->getFirstName(),
                'lastName' => $token->getLastName(),
                'nonce' => $token->getNonce(),
                'encrypted' => $token->getEncrypted(),
            ]))
            ->send()
        ;

        $this->assertResponseStatusCodeSame(401);
    }

    public function testAuthorizeNoToken()
    {
        $this->apiRequest(self::createClient(), 'POST', '/api/community/members/authorize', null, 401);
    }

    public function testAuthorizeInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'POST', '/api/community/members/authorize', 'invalid', 401);
    }

    public function testResetProcess()
    {
        $client = self::createClient();

        /*
         * Reset request
         */

        $this->apiRequest($client, 'POST', '/api/community/members/reset/request/43MAmriw076a19EWSo79m9', self::CITIPO_TOKEN);

        // Request should have been persisted
        /** @var Contact $member */
        $member = static::getContainer()->get(ContactRepository::class)->findOneByBase62Uid('43MAmriw076a19EWSo79m9');

        // Old password should still be valid
        $this->assertTrue(static::getContainer()->get(UserPasswordHasherInterface::class)->isPasswordValid($member, 'password'));

        // Confirmation email should have been sent
        $this->assertNotNull($member->getAccountResetToken());
        $this->assertNotNull($member->getAccountResetRequestedAt());

        $messages = static::getContainer()->get('messenger.transport.async_priority_high')->get();
        $this->assertCount(1, $messages);

        /** @var TemplatedEmail $mail */
        $mail = $messages[0]->getMessage()->getMessage();
        $this->assertSame('Confirm your password reset request', $mail->getSubject());

        $confirmReference = $mail->getContext()['reference'] ?? null;
        $this->assertNotNull($confirmReference);

        /*
         * Confirm reset
         */

        $this->apiRequest($client, 'POST', '/api/community/members/reset/confirm/'.$confirmReference, self::CITIPO_TOKEN, 200, Json::encode([
            'password' => 'updated_password',
        ]));

        // Request should have been cleared
        /** @var Contact $member */
        $member = static::getContainer()->get(ContactRepository::class)->findOneByBase62Uid('43MAmriw076a19EWSo79m9');

        $this->assertNull($member->getAccountResetToken());
        $this->assertNull($member->getAccountResetRequestedAt());

        // New password should be valid but not previous one
        $this->assertTrue(static::getContainer()->get(UserPasswordHasherInterface::class)->isPasswordValid($member, 'updated_password'));
        $this->assertFalse(static::getContainer()->get(UserPasswordHasherInterface::class)->isPasswordValid($member, 'password'));
    }

    public function testResetNoToken()
    {
        $this->apiRequest(self::createClient(), 'POST', '/api/community/members/reset/request/43MAmriw076a19EWSo79m9', null, 401);
    }

    public function testResetInvalidToken()
    {
        $this->apiRequest(self::createClient(), 'POST', '/api/community/members/reset/request/43MAmriw076a19EWSo79m9', 'invalid', 401);
    }

    public function testResetInvalidContact()
    {
        $this->apiRequest(self::createClient(), 'POST', '/api/community/members/reset/request/invalid', self::CITIPO_TOKEN, 404);
    }

    public function testResetConfirmInvalidContact()
    {
        $this->apiRequest(
            self::createClient(),
            'POST',
            '/api/community/members/reset/confirm/invalid/invalid',
            self::CITIPO_TOKEN,
            404,
        );
    }

    public function testResetConfirmInvalidContactToken()
    {
        $this->apiRequest(
            self::createClient(),
            'POST',
            '/api/community/members/reset/confirm/43MAmriw076a19EWSo79m9/invalid',
            self::CITIPO_TOKEN,
            404,
            Json::encode(['password' => 'updated_password']),
        );
    }

    public function testResetConfirmInvalidPayload()
    {
        $this->apiRequest(
            self::createClient(),
            'POST',
            '/api/community/members/reset/confirm/43MAmriw076a19EWSo79m9/invalid',
            self::CITIPO_TOKEN,
            400,
            'invalid',
        );
    }
}
