<?php

namespace App\Tests\Controller\Console\Project\Configuration\Appearance\Website;

use App\Entity\Project;
use App\Entity\Theme\WebsiteTheme;
use App\Repository\ProjectRepository;
use App\Repository\Theme\WebsiteThemeRepository;
use App\Tests\WebTestCase;

class ThemeControllerTest extends WebTestCase
{
    public function testUpdateSameTheme()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/appearance/website/theme');
        $this->assertResponseIsSuccessful();
        $button = $crawler->selectButton('Save');

        /** @var WebsiteTheme $theme */
        $theme = static::getContainer()->get(WebsiteThemeRepository::class)->findOneBy([
            'repositoryFullName' => 'citipo/theme-bold',
        ]);

        $client->submit($button->form(), [
            'website_theme[theme]' => $theme->getId(),
            'website_theme[appearancePrimary]' => '333',
            'website_theme[appearanceSecondary]' => '444',
            'website_theme[appearanceThird]' => '555',
            'website_theme[fontTitle]' => 'Roboto',
            'website_theme[fontText]' => 'Open Sans',
        ]);

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => '2c720420-65fd-4360-9d77-731758008497']);
        $this->assertSame('citipo/theme-bold', $project->getWebsiteTheme()->getRepositoryFullName());
        $this->assertSame('Roboto', $project->getWebsiteFontTitle());
        $this->assertSame('Open Sans', $project->getWebsiteFontText());
        $this->assertSame('333', $project->getAppearancePrimary());
        $this->assertSame('444', $project->getAppearanceSecondary());
        $this->assertSame('555', $project->getAppearanceThird());

        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div.world-alert:contains("Appearance configuration was successfully saved.")');
    }

    public function testUpdateChangeTheme()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/appearance/website/theme');
        $this->assertResponseIsSuccessful();
        $button = $crawler->selectButton('Save');

        /** @var WebsiteTheme $theme */
        $theme = static::getContainer()->get(WebsiteThemeRepository::class)->findOneBy([
            'repositoryFullName' => 'citipo/theme-efficient',
        ]);

        $client->submit($button->form(), [
            'website_theme[theme]' => $theme->getId(),
            'website_theme[appearancePrimary]' => '333',
            'website_theme[appearanceSecondary]' => '444',
            'website_theme[appearanceThird]' => '555',
            'website_theme[fontTitle]' => 'Roboto',
            'website_theme[fontText]' => 'Open Sans',
        ]);

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => '2c720420-65fd-4360-9d77-731758008497']);
        $this->assertSame('citipo/theme-efficient', $project->getWebsiteTheme()->getRepositoryFullName());
        $this->assertSame('Roboto Slab', $project->getWebsiteFontTitle());
        $this->assertSame('Roboto', $project->getWebsiteFontText());
        $this->assertSame('000', $project->getAppearancePrimary());
        $this->assertSame('111', $project->getAppearanceSecondary());
        $this->assertSame('222', $project->getAppearanceThird());

        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div.world-alert:contains("Appearance configuration was successfully saved.")');
    }
}
