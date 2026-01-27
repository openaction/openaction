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

        $image = $imageManager->read($storage->read($pathname));
        $this->assertSame(1200, $image->width());
        $this->assertSame(630, $image->height());

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
            'update_social_accounts[phone]' => '+33101010101',
            'update_social_accounts[facebook]' => 'https://www.facebook.com/',
            'update_social_accounts[twitter]' => 'https://twitter.com/',
            'update_social_accounts[instagram]' => 'https://www.instagram.com/',
            'update_social_accounts[linkedIn]' => 'https://www.linkedin.com/in/',
            'update_social_accounts[youtube]' => 'https://www.youtube.com/',
            'update_social_accounts[medium]' => 'https://medium.com/',
            'update_social_accounts[telegram]' => 'tgalopin',
            'update_social_accounts[snapchat]' => 'https://www.snapchat.com/',
            'update_social_accounts[whatsapp]' => 'https://whatsapp.com',
            'update_social_accounts[tiktok]' => 'https://www.tiktok.com',
            'update_social_accounts[threads]' => 'https://www.threads.net/',
            'update_social_accounts[bluesky]' => 'https://bsky.app/profile/',
            'update_social_accounts[mastodon]' => 'https://mastodon.online/',
        ]);

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => '151f1340-9ad6-47c7-a8a5-838ff955eae7']);
        $this->assertSame('testcase@citipo.com', $project->getSocialEmail());
        $this->assertSame('+33101010101', $project->getSocialPhone());
        $this->assertSame('https://www.facebook.com/', $project->getSocialFacebook());
        $this->assertSame('https://twitter.com/', $project->getSocialTwitter());
        $this->assertSame('https://www.instagram.com/', $project->getSocialInstagram());
        $this->assertSame('https://www.linkedin.com/in/', $project->getSocialLinkedIn());
        $this->assertSame('https://www.youtube.com/', $project->getSocialYoutube());
        $this->assertSame('https://medium.com/', $project->getSocialMedium());
        $this->assertSame('tgalopin', $project->getSocialTelegram());
        $this->assertSame('https://www.snapchat.com/', $project->getSocialSnapchat());
        $this->assertSame('https://whatsapp.com', $project->getSocialWhatsapp());
        $this->assertSame('https://www.tiktok.com', $project->getSocialTiktok());
        $this->assertSame('https://www.threads.net/', $project->getSocialThreads());
        $this->assertSame('https://bsky.app/profile/', $project->getSocialBluesky());
        $this->assertSame('https://mastodon.online/', $project->getSocialMastodon());

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
                false,
                'email',
            ],
        ];

        yield 'message' => [
            [
                false,
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
