<?php

namespace App\Tests\Controller\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ForgotPasswordControllerTest extends WebTestCase
{
    public function testRedirectForgotPassword()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/security/login');
        $this->assertResponseIsSuccessful();

        $client->click($crawler->selectLink('Forgotten password?')->link());
        $this->assertResponseIsSuccessful();
    }

    public function provideValidForgotPasswordRequest()
    {
        yield ['titouan.galopin@citipo.com'];
    }

    /**
     * @dataProvider provideValidForgotPasswordRequest
     */
    public function testSuccessfulForgotPasswordRequest(string $email)
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/security/forgot-password-request');
        $button = $crawler->selectButton('Send');

        $this->assertResponseIsSuccessful();

        /** @var User $userNotUpdate */
        $userNotUpdate = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => $email]);

        $client->submit($button->form(), [
            'forgot_password[email]' => $email,
        ]);
        $this->assertTrue($client->getResponse()->isRedirect('/security/forgot-password-request-sent'));

        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        /** @var User $userUpdate */
        $userUpdate = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => $email]);

        $this->assertNull($userNotUpdate->getSecretResetPassword());
        $this->assertNull($userNotUpdate->getDueDateResetPassword());

        $this->assertIsString($userUpdate->getSecretResetPassword());
        $this->assertInstanceOf(\DateTimeImmutable::class, $userUpdate->getDueDateResetPassword());
    }

    /**
     * @dataProvider provideValidForgotPasswordRequest
     */
    public function testResendForgotPasswordRequest(string $email)
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/security/forgot-password-request');
        $button = $crawler->selectButton('Send');
        $this->assertResponseIsSuccessful();

        $client->submit($button->form(), [
            'forgot_password[email]' => $email,
        ]);

        /** @var User $user */
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => $email]);

        $clientResend = clone $client;
        $clientResend->submit($button->form(), [
            'forgot_password[email]' => $email,
        ]);

        /** @var User $userResend */
        $userResend = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => $email]);
        $this->assertEquals($user->getSecretResetPassword(), $userResend->getSecretResetPassword());
    }

    public function provideInvalidForgotPasswordRequest()
    {
        yield 'invalid_email' => ['invalid'];
        yield 'empty_email' => [''];
    }

    /**
     * @dataProvider provideInvalidForgotPasswordRequest
     */
    public function testUnsuccessfulForgotPasswordRequest(string $email)
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/security/forgot-password-request');
        $this->assertResponseIsSuccessful();
        $button = $crawler->selectButton('Send');

        $client->submit($button->form(), [
            'forgot_password[email]' => $email,
        ]);
        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('input.is-invalid');
        $this->assertSelectorExists('button:contains("Send")');
    }

    public function provideValidForgotPassword()
    {
        yield ['titouan.galopin@citipo.com', '12345678'];
    }

    /**
     * @dataProvider provideValidForgotPassword
     */
    public function testSuccessfulForgotPassword(string $email, string $newPassword)
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);

        /** @var User $user */
        $user = $userRepository->findOneBy(['email' => $email]);
        $user->createForgotPasswordSecret();

        static::getContainer()->get(EntityManagerInterface::class)->persist($user);
        static::getContainer()->get(EntityManagerInterface::class)->flush();

        $crawler = $client->request('GET', '/security/forgot-password/'.$user->getSecretResetPassword());
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Update');

        $client->submit($button->form(), [
            'reset_password[newPassword][first]' => $newPassword,
            'reset_password[newPassword][second]' => $newPassword,
        ]);

        $this->assertTrue($client->getResponse()->isRedirect('/security/login?reset=1'));

        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        /** @var User $userUpdate */
        $userUpdate = $userRepository->findOneBy(['email' => $email]);

        $this->assertNull($userUpdate->getSecretResetPassword());
        $this->assertNull($userUpdate->getDueDateResetPassword());
        $this->assertTrue(static::getContainer()->get(UserPasswordHasherInterface::class)->isPasswordValid($userUpdate, $newPassword));
    }

    public function testInvalidSecretForgotPassword()
    {
        $client = static::createClient();
        $client->request('GET', '/security/forgot-password/1');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * @dataProvider provideValidForgotPasswordRequest
     */
    public function testExpiresSecretForgotPassword(string $email)
    {
        $client = static::createClient();

        /** @var User $user */
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => $email]);
        $user->createForgotPasswordSecret('-1 second');

        static::getContainer()->get(EntityManagerInterface::class)->persist($user);
        static::getContainer()->get(EntityManagerInterface::class)->flush();

        $client->request('GET', '/security/forgot-password/'.$user->getSecretResetPassword());
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
