<?php

namespace App\Tests\Controller\Console\Partner;

use App\Entity\Theme\WebsiteTheme;
use App\Repository\Theme\WebsiteThemeRepository;
use App\Tests\WebTestCase;
use App\Theme\Consumer\SyncThemeMessage;

class ThemeControllerTest extends WebTestCase
{
    public function testManage()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/partner/themes/manage');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.world-block:contains("citipo/theme-bold")');
        $this->assertSelectorExists('.world-block:contains("citipo/theme-structured")');
        $this->assertSelectorExists('.world-block:contains("citipo/theme-efficient")');
        $this->assertSelectorExists('.world-block:contains("Archived theme")');
    }

    public function testSync()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/partner/themes/manage');
        $this->assertResponseIsSuccessful();
        $token = $this->filterGlobalCsrfToken($crawler);

        $client->request('GET', '/console/partner/themes/d325bbff-70bf-40a5-ac25-c0259c0aa126/sync?_token='.$token);
        $this->assertResponseRedirects('/console/partner/themes/manage');

        // Should have updated theme
        /** @var WebsiteTheme $theme */
        $theme = static::getContainer()->get(WebsiteThemeRepository::class)->findOneBy(['repositoryFullName' => 'citipo/theme-bold']);
        $this->assertTrue($theme->isUpdating());

        // Should have published sync message
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');
        $this->assertCount(1, $messages = $transport->get());

        /* @var SyncThemeMessage $message */
        $this->assertInstanceOf(SyncThemeMessage::class, $message = $messages[0]->getMessage());
        $this->assertSame($theme->getId(), $message->getThemeId());
    }

    public function testLinkAlreadyAuthor()
    {
        $client = static::createClient();
        $this->authenticate($client, 'adrien.duguet@citipo.com');

        $crawler = $client->request('GET', '/console/partner/themes/manage');
        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $crawler->filter('.world-block')->count());

        $client->request('GET', '/console/partner/themes/link?installation_id=20980257&apply=1');
        $this->assertResponseRedirects();

        $crawler = $client->request('GET', '/console/partner/themes/manage');
        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $crawler->filter('.world-block')->count());
    }

    public function testLinkUnlinked()
    {
        $client = static::createClient();
        $this->authenticate($client, 'adrien.duguet@citipo.com');

        $crawler = $client->request('GET', '/console/partner/themes/manage');
        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $crawler->filter('.world-block')->count());

        $client->request('GET', '/console/partner/themes/link?installation_id=20980258&apply=1');
        $this->assertResponseRedirects();

        $crawler = $client->request('GET', '/console/partner/themes/manage');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.world-block:contains("citipo/unlinked")');
    }
}
