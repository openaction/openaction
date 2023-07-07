<?php

namespace App\Tests\Controller\Console\Project\Developers;

use App\Entity\Project;
use App\Entity\Theme\ProjectAsset;
use App\Repository\ProjectRepository;
use App\Repository\Theme\ProjectAssetRepository;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ThemeControllerTest extends WebTestCase
{
    public function testUpdateTheme()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => '2c720420-65fd-4360-9d77-731758008497']);
        $this->assertNull($project->getWebsiteCustomCss());
        $this->assertNull($project->getWebsiteCustomJs());
        $this->assertNull($project->getEmailingCustomCss());

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/developers/theme');
        $this->assertResponseIsSuccessful();

        $client->submit($crawler->filter('form[name="editor"]')->form(), [
            'website_style' => 'a',
            'website_script' => 'b',
            'website_head' => 'test',
            'website_home' => '',
            'website_list' => file_get_contents(__DIR__.'/../../../../../src/DataFixtures/Resources/theme/list.html.twig'),
            'emailing_style' => 'c',
            'emailing_legalities' => 'l',
        ]);

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => '2c720420-65fd-4360-9d77-731758008497']);
        $this->assertSame('a', $project->getWebsiteCustomCss());
        $this->assertSame('b', $project->getWebsiteCustomJs());
        $this->assertSame('c', $project->getEmailingCustomCss());
        $this->assertSame('l', $project->getEmailingLegalities());
        $this->assertSame('test', $project->getWebsiteCustomTemplates()['head.html.twig']);
        $this->assertNull($project->getWebsiteCustomTemplates()['home.html.twig']);
        $this->assertNull($project->getWebsiteCustomTemplates()['list.html.twig']);
        $this->assertNull($project->getWebsiteCustomTemplates()['content.html.twig']);

        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div.world-alert:contains("Appearance configuration was successfully saved.")');
    }

    public function testAssetAdd()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        // Shouldn't already exist
        $this->assertNull(static::getContainer()->get(ProjectAssetRepository::class)->findOneBy(['name' => 'mario.png']));

        // Upload
        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/developers/theme');
        $this->assertResponseIsSuccessful();

        $client->submit($crawler->filter('form[name="asset"]')->form(), [
            'asset[file]' => new UploadedFile(__DIR__.'/../../../../Fixtures/upload/mario.png', 'mario.png'),
        ]);

        // Check the upload
        /** @var ProjectAsset $asset */
        $asset = static::getContainer()->get(ProjectAssetRepository::class)->findOneBy(['name' => 'mario.png']);
        $this->assertInstanceOf(ProjectAsset::class, $asset);
        $this->assertSame('mario.png', $asset->getName());

        // Check the file was saved in the CDN
        $this->assertTrue(static::getContainer()->get('cdn.storage')->fileExists($asset->getFile()->getPathname()));

        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div.world-alert:contains("Theme assets were successfully updated.")');
    }

    public function testAssetRemove()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        // Should exist
        $this->assertInstanceOf(ProjectAsset::class, static::getContainer()->get(ProjectAssetRepository::class)->findOneBy(['name' => 'asset.png']));

        // Remove
        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/developers/theme');
        $this->assertResponseIsSuccessful();

        $client->click($crawler->filter('a[class="developer-editor-files-asset-item-delete"]')->link());
        $this->assertResponseRedirects();

        // Check the entity was removed
        $this->assertNull(static::getContainer()->get(ProjectAssetRepository::class)->findOneBy(['name' => 'asset.png']));

        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div.world-alert:contains("Theme assets were successfully updated.")');
    }
}
