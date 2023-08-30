<?php

namespace App\Tests\Controller\Console\Project\Configuration\Appearance\Website\Homepage;

use App\Entity\Website\PageBlock;
use App\Repository\ProjectRepository;
use App\Repository\Website\PageBlockRepository;
use App\Tests\WebTestCase;
use App\Website\PageBlock\BlockInterface;
use App\Website\PageBlock\HomeContentBlock;
use App\Website\PageBlock\HomeCtaBlock;
use App\Website\PageBlock\HomeNewsletterBlock;
use App\Website\PageBlock\HomePostsBlock;
use App\Website\PageBlock\HomeSocialsBlock;

class CreateControllerTest extends WebTestCase
{
    public function provideCreate()
    {
        $types = [
            HomeContentBlock::TYPE,
            HomeCtaBlock::TYPE,
            HomeNewsletterBlock::TYPE,
            HomePostsBlock::TYPE,
            HomeSocialsBlock::TYPE,
        ];

        foreach ($types as $type) {
            yield $type => [$type];
        }
    }

    /**
     * @dataProvider provideCreate
     */
    public function testCreate(string $type)
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/configuration/appearance/website/homepage');
        $this->assertResponseIsSuccessful();

        $crawler = $client->clickLink('Add a block');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Create');

        $client->submit($button->form(), [
            'create_home_block[type]' => $type,
        ]);

        $this->assertResponseRedirects();

        // Should have been created
        $this->assertInstanceOf(PageBlock::class, static::getContainer()->get(PageBlockRepository::class)->findOneBy([
            'project' => static::getContainer()->get(ProjectRepository::class)->findOneByUuid('151f1340-9ad6-47c7-a8a5-838ff955eae7'),
            'page' => BlockInterface::PAGE_HOME,
            'type' => $type,
        ]));
    }
}
