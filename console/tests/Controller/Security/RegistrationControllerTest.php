<?php

namespace App\Tests\Controller\Security;

use App\Repository\OrganizationMemberRepository;
use App\Repository\RegistrationRepository;
use App\Repository\UserRepository;
use App\Tests\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Mime\Email;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegistrationLink(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isRedirect('/security/login'));

        $crawler = $client->followRedirect();
        $this->assertCount(1, $link = $crawler->filter('a:contains("No account yet? Sign up for free!")'));

        $client->click($link->link());
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('label:contains("Your work email address")');
    }

    public function provideEmails(): iterable
    {
        yield ['john@new.com'];
    }

    /**
     * @dataProvider provideEmails
     */
    public function testRegistrationForm(string $email): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');
        $this->assertSelectorExists('form[name=registration]');

        $form = $crawler->selectButton('Sign up')->form();
        $client->submit($form, ['registration[name]' => $email]);
        $this->assertResponseRedirects();

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, static::getContainer()->get(RegistrationRepository::class)->findBy(['email' => $email]));
    }

    public function provideUsedEmails(): iterable
    {
        yield ['titouan.galopin@citipo.com'];
    }

    /**
     * @dataProvider provideUsedEmails
     */
    public function testRegistrationFormWithUsedEmail(string $email): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');
        $this->assertSelectorExists('form[name=registration]');
        $form = $crawler->selectButton('Sign up')->form();

        $client->submit($form, ['registration[name]' => $email]);
        $this->assertResponseRedirects();

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div:contains("This email already exists on Citipo: perhaps do you already have an account?")');
    }

    /**
     * @dataProvider provideEmails
     */
    public function testRegistrationFormHoneyPot(string $email): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');
        $this->assertSelectorExists('form[name=registration]');

        $form = $crawler->selectButton('Sign up')->form();
        $client->submit($form, [
            'registration[email]' => 'it should be empty',
            'registration[name]' => $email,
        ]);
        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('#registration_email');
    }

    /**
     * @dataProvider provideEmails
     */
    public function testSendEmailConfirmation(string $email): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');
        $form = $crawler->selectButton('Sign up')->form();

        $client->submit($form, ['registration[name]' => $email]);
        $this->assertResponseRedirects();

        $this->assertQueuedEmailCount(1);

        /** @var Email $message */
        $message = $this->getMailerMessage(0);
        $this->assertSame($message->getSubject(), 'You are almost there!');
        $this->assertEmailAddressContains($message, 'to', $email);
        $this->assertEmailTextBodyContains($message, 'Finalize my account');

        $registration = static::getContainer()->get(RegistrationRepository::class)->findOneBy(['email' => $email]);
        $this->assertEmailHtmlBodyContains($message, $registration->getUuid());
    }

    /**
     * @dataProvider provideEmails
     */
    public function testResendEmailConfirmation(string $email): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Sign up')->form();

        $client->submit($form, ['registration[name]' => $email]);
        $this->assertResponseRedirects();

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $link = $crawler->filter('a:contains("Resend the email")'));

        $client->click($link->link());
        $this->assertResponseRedirects();

        $this->assertQueuedEmailCount(1);

        /** @var Email $message */
        $message = $this->getMailerMessage(0);
        $this->assertSame($message->getSubject(), 'You are almost there!');
        $this->assertEmailAddressContains($message, 'to', $email);
        $this->assertEmailTextBodyContains($message, 'Finalize my account');

        $registration = static::getContainer()->get(RegistrationRepository::class)->findOneBy(['email' => $email]);
        $this->assertEmailHtmlBodyContains($message, $registration->getUuid());
    }

    public function testFinalizingForm(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');
        $form = $crawler->selectButton('Sign up')->form();

        $client->submit($form, ['registration[name]' => 'peter@email.com']);

        /** @var Email $message */
        $message = $this->getMailerMessage(0);
        $crawler = new Crawler($message->getHtmlBody());
        $this->assertCount(1, $link = $crawler->filter('a:contains("Finalize my account")'));

        $crawler = $client->click($link->link());
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Finalize')->form();
        $client->submit($form, [
            'registration_finalizing[firstName]' => 'Peter',
            'registration_finalizing[lastName]' => 'Parker',
            'registration_finalizing[password][first]' => '12345678',
            'registration_finalizing[password][second]' => '12345678',
            'registration_finalizing[agreeTerms]' => true,
        ]);

        $this->assertResponseRedirects();

        // RedirectController should display the "no-orga" message
        $client->followRedirect();
        $this->assertSelectorExists('div:contains("You are not associated to any organization on Citipo")');
        $this->assertCount(0, static::getContainer()->get(RegistrationRepository::class)->findBy(['email' => 'peter@email.com']));
        $this->assertCount(1, static::getContainer()->get(UserRepository::class)->findBy(['email' => 'peter@email.com']));
    }

    public function testAcceptInvite(): void
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        // send invite
        $crawler = $client->request('GET', '/console/organization/cbeb774c-284c-43e3-923a-5a2388340f91/team/invite/member');
        $button = $crawler->filter('button:contains("Send the invite")');
        $client->submit($button->form(), [
            'member_invite[email]' => 'john@email.com',
            'member_invite[isAdmin]' => true,
        ]);
        $this->assertResponseRedirects();

        // accept invite
        /** @var Email $message */
        $message = $this->getMailerMessage(0);
        $crawler = new Crawler($message->getHtmlBody());
        $accept = $crawler->filter('a:contains("Join my team")');

        $crawler = $client->click($accept->link());
        $this->assertResponseIsSuccessful();

        // create account
        $form = $crawler->selectButton('Finalize')->form();
        $client->submit($form, [
            'registration_finalizing[firstName]' => 'John',
            'registration_finalizing[lastName]' => 'Galopin',
            'registration_finalizing[password][first]' => '12345678',
            'registration_finalizing[password][second]' => '12345678',
            'registration_finalizing[agreeTerms]' => true,
        ]);
        $this->assertResponseRedirects();

        $client->followRedirect();
        $this->assertResponseRedirects();

        $crawler = $client->followRedirect();
        $this->assertCount(1, $crawler->filter('.list-group-item:contains("Acme")'));

        // Check tenant token created
        $member = self::getContainer()->get(OrganizationMemberRepository::class)->findOneBy([], ['createdAt' => 'DESC']);
        $this->assertNotEmpty($member->getCrmTenantToken());
    }
}
