<?php

namespace App\Tests\Controller\Console\Project\Configuration;

use App\Entity\Project;
use App\Entity\Upload;
use App\Repository\ProjectRepository;
use App\Tests\WebTestCase;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SocialNetworksControllerTest extends WebTestCase
{
    public function testUpdateMetas()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => '2c720420-65fd-4360-9d77-731758008497']);
        $this->assertNull($project->getWebsiteMetaTitle());
        $this->assertNull($project->getWebsiteMetaDescription());
        $this->assertNull($project->getWebsiteSharer());

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/social-networks/metas');
        $button = $crawler->selectButton('Save');

        $client->submit($button->form(), [
            'update_metas[websiteMetaTitle]' => 'Title',
            'update_metas[websiteMetaDescription]' => 'Description',
            'update_metas[websiteSharer]' => new UploadedFile(__DIR__.'/../../../../Fixtures/upload/mario.png', 'mario.png'),
        ]);

        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => '2c720420-65fd-4360-9d77-731758008497']);
        $this->assertSame('Title', $project->getWebsiteMetaTitle());
        $this->assertSame('Description', $project->getWebsiteMetaDescription());

        // Check the sharer has been uploaded
        $storage = static::getContainer()->get('cdn.storage');

        /** @var ImageManager $imageManager */
        $imageManager = static::getContainer()->get(ImageManager::class);

        $this->assertInstanceOf(Upload::class, $project->getWebsiteSharer());

        $pathname = $project->getWebsiteSharer()->getPathname();
        $this->assertTrue($storage->fileExists($pathname), $pathname.' should exist in storage.');

        $image = $imageManager->make($storage->read($pathname));
        $this->assertSame(1200, $image->getWidth());
        $this->assertSame(630, $image->getHeight());

        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div.world-alert:contains("Appearance configuration was successfully saved.")');
    }

    public function testUpdateSocialAccounts()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/configuration/social-networks/accounts');
        $button = $crawler->selectButton('Save');

        $client->submit($button->form(), [
            'update_social_accounts[email]' => 'testcase@citipo.com',
            'update_social_accounts[youtube]' => 'youtube.com/test',
            'update_social_accounts[snapchat]' => 'snapchatusername',
        ]);

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => '151f1340-9ad6-47c7-a8a5-838ff955eae7']);
        $this->assertSame('testcase@citipo.com', $project->getSocialEmail());
        $this->assertSame('http://youtube.com/test', $project->getSocialYoutube());
        $this->assertSame('snapchatusername', $project->getSocialSnapchat());

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div.world-alert:contains("Social networks configuration was successfully saved")');
    }

    public function provideUpdateSocialSharers(): iterable
    {
        yield 'social' => [
            [
                'facebook',
                'twitter',
                false,
                false,
                false,
                'email',
            ],
        ];

        yield 'message' => [
            [
                false,
                false,
                false,
                'telegram',
                'whatsapp',
                false,
            ],
        ];
    }

    /**
     * @dataProvider provideUpdateSocialSharers
     */
    public function testUpdateSocialSharers(array $sharers): void
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_IDF_UUID.'/configuration/social-networks/sharers');
        $button = $crawler->selectButton('Save');

        $client->submit($button->form(), [
            'update_social_sharers[sharers]' => $sharers,
        ]);

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => '151f1340-9ad6-47c7-a8a5-838ff955eae7']);

        $this->assertEqualsCanonicalizing(
            array_filter($sharers),
            $project->getSocialSharers()->toArray()
        );

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div.world-alert:contains("Social networks configuration was successfully saved")');
    }
}
