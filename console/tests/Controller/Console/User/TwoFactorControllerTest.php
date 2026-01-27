<?php

namespace App\Tests\Controller\Console\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorControllerTest extends WebTestCase
{
    public function testStatusFromMenu()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/');
        $client->followRedirect();

        $client->clickLink('Two-factor authentication');
        $this->assertResponseIsSuccessful();
    }

    public function provideConfirmPassword(): iterable
    {
        yield 'valid' => [
            'password' => 'password',
            'expectedRedirect' => '/console/user/two-factor/enable',
        ];

        yield 'invalid' => [
            'password' => 'invalid',
            'expectedRedirect' => null,
        ];
    }

    /**
     * @dataProvider provideConfirmPassword
     */
    public function testConfirmPassword(string $password, ?string $expectedRedirect)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/user/two-factor');
        $this->assertResponseIsSuccessful();

        $crawler = $client->clickLink('Enable');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Continue');

        $client->submit($button->form(), [
            'confirm_password[confirmPassword]' => $password,
        ]);

        if ($expectedRedirect) {
            $this->assertResponseRedirects($expectedRedirect);
        } else {
            $this->assertResponseIsSuccessful();
            $this->assertSelectorExists('input.is-invalid');
        }
    }

    public function testAccessDeniedTwoFactor()
    {
        $client = static::createClient();

        /** @var User $user */
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => static::USER_TGALOPIN_EMAIL]);
        $user->finishEnablingTwoFactor();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->persist($user);
        $em->flush();

        $this->authenticate($client);

        $client->request('GET', '/console/user/two-factor/enable');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testQrCode()
    {
        $client = static::createClient();

        /** @var User $user */
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => self::USER_TGALOPIN_EMAIL]);
        $user->startTwoFactorEnablingProcess(static::getContainer()->get(TotpAuthenticatorInterface::class)->generateSecret());

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->persist($user);
        $em->flush();

        $this->authenticate($client);

        $client->request('GET', '/console/user/two-factor/qr-code');
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'image/png');

        $content = $client->getResponse()->getContent();
        $this->assertNotFalse($content);

        $imageSize = getimagesizefromstring($content);
        $this->assertIsArray($imageSize);
        $this->assertSame('image/png', $imageSize['mime']);

        $expectedSize = 400 + 2 * 10;
        $this->assertSame($expectedSize, $imageSize[0]);
        $this->assertSame($expectedSize, $imageSize[1]);
    }

    public function testDownloadBackupCodes()
    {
        $client = static::createClient();

        /** @var User $user */
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => self::USER_TGALOPIN_EMAIL]);
        $user->finishEnablingTwoFactor(['009473', '883768', '588192', '499632', '179949', '319302', '372292', '934426']);

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->persist($user);
        $em->flush();

        $this->authenticate($client);

        $client->request('GET', '/console/user/two-factor/download-backup-codes');
        $this->assertResponseIsSuccessful();
        $this->assertSame(implode("\n", $user->getTwoFactorBackupCodes()), $client->getResponse()->getContent());
    }

    public function twoFactorValidCodes()
    {
        yield ['123456', static::USER_TGALOPIN_EMAIL];
    }

    /**
     * @dataProvider twoFactorValidCodes
     */
    public function testSuccessfulTwoFactorEnabled(string $code, string $email)
    {
        $client = static::createClient();

        /** @var User $user */
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => $email]);
        $user->startTwoFactorEnablingProcess(static::getContainer()->get(TotpAuthenticatorInterface::class)->generateSecret());

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->persist($user);
        $em->flush();

        $this->authenticate($client, $email);

        $crawler = $client->request('GET', '/console/user/two-factor/enable');
        $button = $crawler->selectButton('Enable');

        $this->assertSelectorExists('img');
        $this->assertSelectorExists('input[type=text]');
        $this->assertResponseIsSuccessful();

        $client->submit($button->form(), [
            'two_factor[code]' => $code,
        ]);

        $this->assertTrue($client->getResponse()->isRedirect('/console/user/two-factor/enabled'));

        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => static::USER_TGALOPIN_EMAIL]);

        $this->assertTrue($user->isTwoFactorEnabled());
        $this->assertCount(10, $user->getTwoFactorBackupCodes());
        foreach ($user->getTwoFactorBackupCodes() as $backupCode) {
            $this->assertMatchesRegularExpression('/^\\d{6}$/', $backupCode);
        }
    }

    public function twoFactorInValidCodes()
    {
        yield ['123458', static::USER_TGALOPIN_EMAIL];
    }

    /**
     * @dataProvider twoFactorInValidCodes
     */
    public function testUnsuccessfulTwoFactorEnabled(string $code, string $email)
    {
        $client = static::createClient();

        /** @var User $user */
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => $email]);
        $user->startTwoFactorEnablingProcess(static::getContainer()->get(TotpAuthenticatorInterface::class)->generateSecret());

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->persist($user);
        $em->flush();

        $this->authenticate($client, $email);

        $crawler = $client->request('GET', '/console/user/two-factor/enable');
        $button = $crawler->selectButton('Enable');

        $this->assertResponseIsSuccessful();

        $client->submit($button->form(), [
            'two_factor[code]' => $code,
        ]);

        $this->assertSelectorExists('input.is-invalid');
    }

    public function testTwoFactorLogin()
    {
        $client = static::createClient();

        // Enable 2FA
        /** @var User $user */
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => static::USER_TGALOPIN_EMAIL]);
        $user->startTwoFactorEnablingProcess(static::getContainer()->get(TotpAuthenticatorInterface::class)->generateSecret());
        $user->finishEnablingTwoFactor();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->persist($user);
        $em->flush();

        // Should require 2FA code
        $crawler = $client->request('GET', '/security/login');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Log in')->form();
        $client->submit($form, ['email' => static::USER_TGALOPIN_EMAIL, 'password' => 'password']);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertStringEndsWith('/', $client->getResponse()->headers->get('Location'));

        $client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertStringEndsWith('/2fa', $client->getResponse()->headers->get('Location'));

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('#_auth_code');
        $this->assertSelectorTextContains('button', 'Log in');
        $this->assertSelectorExists('a[href="/security/logout"]');

        // Log out
        $client->request('GET', '/security/logout');
        $this->assertResponseRedirects();

        // Disable 2FA
        /** @var User $user */
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => static::USER_TGALOPIN_EMAIL]);
        $user->disableTwoFactor();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->persist($user);
        $em->flush();

        // Should not require 2FA code
        $crawler = $client->request('GET', '/security/login');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Log in')->form();
        $client->submit($form, ['email' => static::USER_TGALOPIN_EMAIL, 'password' => 'password']);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertStringEndsWith('/', $client->getResponse()->headers->get('Location'));

        $client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertStringEndsWith('/start', $client->getResponse()->headers->get('Location'));
    }
}
