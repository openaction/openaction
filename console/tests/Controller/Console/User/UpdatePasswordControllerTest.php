<?php

namespace App\Tests\Controller\Console\User;

use App\Repository\UserRepository;
use App\Tests\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UpdatePasswordControllerTest extends WebTestCase
{
    public function provideNewPasswords(): iterable
    {
        yield 'fisrt change' => ['titouan.galopin@citipo.com', 'password', 'p_ssw0rd1234'];
        yield 'second change' => ['titouan.galopin@citipo.com', 'password', 'q1We3R$1234'];
        yield 'third change' => ['titouan.galopin@citipo.com', 'password', 'c4$N2w_1234'];
    }

    /**
     * @dataProvider provideNewPasswords
     */
    public function testSuccessfulPasswordChange(string $email, string $oldPassword, string $newPassword): void
    {
        $client = static::createClient();
        $this->authenticate($client, $email);

        $client->request('GET', '/');
        $client->followRedirect();
        $this->assertSelectorExists('.world-header a:contains("Password")');

        $crawler = $client->clickLink('Password');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Change');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'change_password[oldPassword]' => $oldPassword,
            'change_password[newPassword][first]' => $newPassword,
            'change_password[newPassword][second]' => $newPassword,
        ]);
        $this->assertResponseRedirects();

        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => $email]);
        $this->assertTrue(static::getContainer()->get(UserPasswordHasherInterface::class)->isPasswordValid($user, $newPassword));
    }

    public function provideInvalidNewPasswords(): iterable
    {
        yield 'missing old password' => ['titouan.galopin@citipo.com', '', 'Q1w2E3r$', 'Q1w2E3r$'];
        yield 'wrong old password' => ['titouan.galopin@citipo.com', 'strawberry', 'Q1w2E3r$', 'Q1w2E3r$'];
        yield 'missing new password' => ['titouan.galopin@citipo.com', 'password', '', 'Q1w2E3r$'];
        yield 'wrong repeat new password' => ['titouan.galopin@citipo.com', 'password', 'Q1w2E3r$', 'p_ssw0rd'];
    }

    /**
     * @dataProvider provideInvalidNewPasswords
     */
    public function testUnsuccessfulPasswordChange(string $email, string $oldPassword, string $newPassword, string $repeatNewPassword): void
    {
        $client = static::createClient();
        $this->authenticate($client, $email);

        $client->request('GET', '/');
        $client->followRedirect();

        $crawler = $client->clickLink('Password');
        $button = $crawler->selectButton('Change');

        $client->submit($button->form(), [
            'change_password[oldPassword]' => $oldPassword,
            'change_password[newPassword][first]' => $newPassword,
            'change_password[newPassword][second]' => $repeatNewPassword,
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('input.is-invalid');
    }
}
