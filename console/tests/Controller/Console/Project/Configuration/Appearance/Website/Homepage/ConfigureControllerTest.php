<?php

namespace App\Tests\Controller\Console\Project\Configuration\Appearance\Website\Homepage;

use App\Entity\Website\PageBlock;
use App\Repository\Website\PageBlockRepository;
use App\Tests\WebTestCase;
use App\Website\PageBlock\BlockInterface;
use App\Website\PageBlock\HomeContentBlock;
use App\Website\PageBlock\HomeCtaBlock;
use App\Website\PageBlock\HomeEventsBlock;
use App\Website\PageBlock\HomePostsBlock;
use App\Website\PageBlock\HomeSocialsBlock;

class ConfigureControllerTest extends WebTestCase
{
    public function testCta()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/appearance/website/homepage');
        $this->assertResponseIsSuccessful();

        $crawler = $client->clickLink('Change the call to action');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save');

        $client->submit($button->form(), [
            'configure_cta_home_block[primary][label]' => 'Renamed button 1',
            'configure_cta_home_block[primary][target]' => '/new-target-1',
            'configure_cta_home_block[primary][openNewTab]' => false,
            'configure_cta_home_block[secondary][label]' => 'Renamed button 2',
            'configure_cta_home_block[secondary][target]' => '/new-target-2',
            'configure_cta_home_block[secondary][openNewTab]' => true,
        ]);

        /** @var PageBlock $block */
        $block = static::getContainer()->get(PageBlockRepository::class)->findOneBy(['type' => HomeCtaBlock::TYPE]);
        $this->assertSame(BlockInterface::PAGE_HOME, $block->getPage());
        $this->assertSame(HomeCtaBlock::TYPE, $block->getType());

        $this->assertSame('Renamed button 1', $block->getConfig()['primary']['label']);
        $this->assertSame('/new-target-1', $block->getConfig()['primary']['target']);
        $this->assertFalse($block->getConfig()['primary']['openNewTab']);
        $this->assertSame('Renamed button 2', $block->getConfig()['secondary']['label']);
        $this->assertSame('/new-target-2', $block->getConfig()['secondary']['target']);
        $this->assertTrue($block->getConfig()['secondary']['openNewTab']);

        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div.world-alert:contains("Appearance configuration was successfully saved.")');
    }

    public function testPosts()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/appearance/website/homepage');
        $this->assertResponseIsSuccessful();

        $crawler = $client->click($crawler->filter('div:contains("Latest posts") a:contains("Configure the displayed categories")')->link());
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save');

        $client->submit($button->form(), [
            'configure_posts_home_block[category]' => '',
        ]);

        /** @var PageBlock $block */
        $block = static::getContainer()->get(PageBlockRepository::class)->findOneBy(['type' => HomePostsBlock::TYPE]);
        $this->assertSame(BlockInterface::PAGE_HOME, $block->getPage());
        $this->assertSame(HomePostsBlock::TYPE, $block->getType());
        $this->assertNull($block->getConfig()['category']);

        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div.world-alert:contains("Appearance configuration was successfully saved.")');
    }

    public function testEvents()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/appearance/website/homepage');
        $this->assertResponseIsSuccessful();

        $crawler = $client->click($crawler->filter('#block-3 a:contains("Configure the displayed categories")')->link());
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save');

        $client->submit($button->form(), [
            'configure_events_home_block[category]' => '',
        ]);

        /** @var PageBlock $block */
        $block = static::getContainer()->get(PageBlockRepository::class)->findOneBy(['type' => HomeEventsBlock::TYPE]);
        $this->assertSame(BlockInterface::PAGE_HOME, $block->getPage());
        $this->assertSame(HomeEventsBlock::TYPE, $block->getType());
        $this->assertNull($block->getConfig()['category']);

        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div.world-alert:contains("Appearance configuration was successfully saved.")');
    }

    public function testContent()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/appearance/website/homepage');
        $this->assertResponseIsSuccessful();

        $crawler = $client->clickLink('Configure the content');
        $this->assertResponseIsSuccessful();

        /** @var PageBlock $block */
        $block = static::getContainer()->get(PageBlockRepository::class)->findOneBy(['type' => HomeContentBlock::TYPE]);

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/appearance/website/homepage/block/'.$block->getId().'/configure',
            ['configure_content_home_block' => ['content' => '<p>Content</p>']],
            [],
            ['HTTP_X-XSRF-TOKEN' => $this->filterGlobalCsrfToken($crawler)]
        );

        /** @var PageBlock $block */
        $block = static::getContainer()->get(PageBlockRepository::class)->findOneBy(['type' => HomeContentBlock::TYPE]);
        $this->assertSame(BlockInterface::PAGE_HOME, $block->getPage());
        $this->assertSame(HomeContentBlock::TYPE, $block->getType());
        $this->assertSame('<p>Content</p>', $block->getConfig()['content']);

        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div.world-alert:contains("Appearance configuration was successfully saved.")');
    }

    public function testSocials()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/appearance/website/homepage');
        $this->assertResponseIsSuccessful();

        $crawler = $client->clickLink('Configure the social accounts');
        $this->assertResponseIsSuccessful();

        $this->assertCount(1, $button = $crawler->selectButton('Save'));

        /** @var PageBlock $block */
        $block = static::getContainer()->get(PageBlockRepository::class)->findOneBy(['type' => HomeSocialsBlock::TYPE]);

        $this->assertSame(['facebook' => null, 'twitter' => null], $block->getConfig());

        $client->submit($button->form(), [
            'configure_socials_home_block[facebook]' => 'https://facebook.com/home-block',
            'configure_socials_home_block[twitter]' => 'https://twitter.com/home-block',
        ]);

        /** @var PageBlock $block */
        $block = static::getContainer()->get(PageBlockRepository::class)->findOneBy(['type' => HomeSocialsBlock::TYPE]);
        $this->assertSame(BlockInterface::PAGE_HOME, $block->getPage());
        $this->assertSame(HomeSocialsBlock::TYPE, $block->getType());
        $this->assertSame('https://facebook.com/home-block', $block->getConfig()['facebook']);
        $this->assertSame('https://twitter.com/home-block', $block->getConfig()['twitter']);

        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div.world-alert:contains("Appearance configuration was successfully saved.")');
    }
}
