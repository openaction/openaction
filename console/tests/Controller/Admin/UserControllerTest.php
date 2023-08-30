<?php

namespace App\Tests\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    public function testSimpleUsersCantImpersonate()
    {
        $client = static::createClient();
        $this->authenticate($client, 'ema.anderson@away.com');

        /** @var User $user */
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'arianneverreau@example.com']);

        $client->request('GET', '/admin/users/'.$user->getId().'/impersonate');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testImpersonate()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        /** @var User $user */
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'arianneverreau@example.com']);

        $client->request('GET', '/admin/users/'.$user->getId().'/impersonate');
        $this->assertResponseRedirects('/?_switch_user=arianneverreau@example.com');

        $client->followRedirect();
        $this->assertResponseRedirects();

        $client->followRedirect();
        $this->assertResponseRedirects('/console/project/5767c01d-e6c1-4a29-a1d3-194ccd14a93f/start');

        $crawler = $client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_PAYMENT_REQUIRED);
        $this->assertCount(1, $crawler->filter('[data-user]:contains("Arianne Verreau")'));
    }
}
