<?php

namespace App\Tests\Controller\Console\Project\Configuration\Appearance;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Tests\WebTestCase;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LogosControllerTest extends WebTestCase
{
    public function testLogos()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => '2c720420-65fd-4360-9d77-731758008497']);
        $this->assertNull($project->getAppearanceLogoDark());
        $this->assertNull($project->getAppearanceLogoWhite());
        $this->assertNull($project->getAppearanceIcon());

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/appearance/logos');
        $button = $crawler->selectButton('Save');

        $client->submit($button->form(), [
            'logos[appearanceLogoDark]' => new UploadedFile(__DIR__.'/../../../../../Fixtures/upload/mario.png', 'mario.png'),
            'logos[appearanceLogoWhite]' => new UploadedFile(__DIR__.'/../../../../../Fixtures/upload/mario.png', 'mario.png'),
            'logos[appearanceIcon]' => new UploadedFile(__DIR__.'/../../../../../Fixtures/upload/mario.png', 'mario.png'),
        ]);

        // Check logos have been properly uploaded
        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => '2c720420-65fd-4360-9d77-731758008497']);

        $expected = [
            ['upload' => $project->getAppearanceLogoDark(), 'width' => 318, 'height' => 400],
            ['upload' => $project->getAppearanceLogoWhite(), 'width' => 318, 'height' => 400],
            ['upload' => $project->getAppearanceIcon(), 'width' => 256, 'height' => 256],
        ];

        $storage = static::getContainer()->get('cdn.storage');

        /** @var ImageManager $imageManager */
        $imageManager = static::getContainer()->get(ImageManager::class);

        foreach ($expected as $field) {
            $this->assertNotNull($field['upload']);

            $pathname = $field['upload']->getPathname();
            $this->assertTrue($storage->fileExists($pathname), $pathname.' should exist in storage.');

            $image = $imageManager->read($storage->read($pathname));
            $this->assertSame($field['width'], $image->width());
            $this->assertSame($field['height'], $image->height());
        }

        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div.world-alert:contains("Appearance configuration was successfully saved.")');

        // Check sending only a dark logo keep the other logos in place
        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/appearance/logos');
        $button = $crawler->selectButton('Save');

        $client->submit($button->form(), [
            'logos[appearanceLogoDark]' => new UploadedFile(__DIR__.'/../../../../../Fixtures/upload/mario.png', 'mario.png'),
        ]);

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => '2c720420-65fd-4360-9d77-731758008497']);
        $this->assertNotNull($project->getAppearanceLogoDark());
        $this->assertNotNull($project->getAppearanceLogoWhite());
        $this->assertNotNull($project->getAppearanceIcon());

        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div.world-alert:contains("Appearance configuration was successfully saved.")');
    }
}
