<?php

namespace App\Tests\Controller\Console\Project\Configuration;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Tests\WebTestCase;

class AccessControllerTest extends WebTestCase
{
    public function testUpdate()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => '2c720420-65fd-4360-9d77-731758008497']);
        $this->assertNull($project->getWebsiteAccessUser());
        $this->assertNull($project->getWebsiteAccessPass());

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/access');
        $button = $crawler->selectButton('Save');

        $client->submit($button->form(), [
            'website_access[websiteAccessUser]' => 'username',
            'website_access[websiteAccessPass]' => 'password',
        ]);

        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => '2c720420-65fd-4360-9d77-731758008497']);
        $this->assertSame('username', $project->getWebsiteAccessUser());
        $this->assertSame('password', $project->getWebsiteAccessPass());

        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div.world-alert:contains("Configuration was successfully saved.")');
    }
}
