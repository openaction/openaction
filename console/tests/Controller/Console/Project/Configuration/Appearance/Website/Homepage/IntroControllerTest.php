<?php

namespace App\Tests\Controller\Console\Project\Configuration\Appearance\Website\Homepage;

use App\Entity\Project;
use App\Entity\Upload;
use App\Repository\ProjectRepository;
use App\Tests\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Intervention\Image\ImageManager;
use League\Flysystem\FilesystemReader;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class IntroControllerTest extends WebTestCase
{
    public function testUpdate()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => '2c720420-65fd-4360-9d77-731758008497']);
        $this->assertNull($project->getWebsiteMainImage());
        $this->assertNull($project->getWebsiteMainIntroTitle());
        $this->assertNull($project->getWebsiteMainIntroContent());

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/appearance/website/intro');
        $button = $crawler->selectButton('Save');

        $client->submit($button->form(), [
            'website_intro[websiteMainImage]' => new UploadedFile(__DIR__.'/../../../../../../../Fixtures/upload/mario.png', 'mario.png'),
            'website_intro[websiteMainVideo]' => new UploadedFile(__DIR__.'/../../../../../../../Fixtures/upload/video.mp4', 'video.mp4'),
            'website_intro[websiteMainIntroTitle]' => 'Intro title',
            'website_intro[websiteMainIntroContent]' => 'Intro content',
        ]);

        $project = static::getContainer()->get(EntityManagerInterface::class)->find(Project::class, $project->getId());
        $this->assertSame('Intro title', $project->getWebsiteMainIntroTitle());
        $this->assertSame('Intro content', $project->getWebsiteMainIntroContent());

        // Check the sharer has been uploaded
        $storage = static::getContainer()->get('cdn.storage');

        /** @var ImageManager $imageManager */
        $imageManager = static::getContainer()->get(ImageManager::class);

        $this->assertInstanceOf(Upload::class, $project->getWebsiteMainImage());

        $pathname = $project->getWebsiteMainImage()->getPathname();
        $this->assertTrue($storage->fileExists($pathname), $pathname.' should exist in storage.');

        $image = $imageManager->read($storage->read($pathname));
        $this->assertSame(1192, $image->width());
        $this->assertSame(1500, $image->height());

        // Check the video has been uploaded
        /** @var FilesystemReader $storage */
        $storage = static::getContainer()->get('cdn.storage');
        $this->assertInstanceOf(Upload::class, $project->getWebsiteMainVideo());

        $pathname = $project->getWebsiteMainVideo()->getPathname();
        $this->assertTrue($storage->fileExists($pathname), $pathname.' should exist in storage.');
        $this->assertSame('video/mp4', $storage->mimeType($pathname));

        // Check the response redirects properly
        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div.world-alert:contains("Appearance configuration was successfully saved.")');

        // Remove the image
        $client->clickLink('Delete the main image');
        $this->assertResponseRedirects();

        $project = static::getContainer()->get(EntityManagerInterface::class)->find(Project::class, $project->getId());
        $this->assertNull($project->getWebsiteMainImage());
        $this->assertNotNull($project->getWebsiteMainVideo());

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div.world-alert:contains("Appearance configuration was successfully saved.")');

        // Remove the video
        $client->clickLink('Delete the main video');
        $this->assertResponseRedirects();

        $project = static::getContainer()->get(EntityManagerInterface::class)->find(Project::class, $project->getId());
        $this->assertNull($project->getWebsiteMainImage());
        $this->assertNull($project->getWebsiteMainVideo());

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div.world-alert:contains("Appearance configuration was successfully saved.")');
    }
}
